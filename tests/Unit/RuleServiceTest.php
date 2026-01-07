<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\Rules\RuleService;
use App\Services\Paperless\Document;
use App\Services\Paperless\PaperlessService;

class RuleServiceTest extends TestCase
{
    private function createMockDocument(): Document
    {
        $mockService = $this->createMock(PaperlessService::class);

        // Mock the getDocumentType method
        $mockService->method('findDocumentTypeById')
            ->willReturn(['id' => 1, 'name' => 'Invoice']);

        // Mock the getCorrespondent method
        $mockService->method('findCorrespondentById')
            ->willReturn(['id' => 1, 'name' => 'ACME Corp']);

        $data = [
            'id' => 1,
            'title' => 'Test Invoice',
            'content' => 'This is a test invoice from ACME Corp',
            'tags' => [],
            'created_date' => '2024-01-01',
            'modified' => '2024-01-01T10:00:00Z',
            'added' => '2024-01-01T10:00:00Z',
            'original_file_name' => 'invoice.pdf',
            'document_type' => 1,
            'correspondent' => 1,
        ];

        return new Document($mockService, $data);
    }

    public function test_parse_simple_rule(): void
    {
        $service = new RuleService();

        $dsl = <<<'DSL'
WHEN str_contains(document.content, "invoice")
THEN
DO addTag(tag: "invoice")
END
DSL;

        $ast = $service->parse($dsl);

        $this->assertIsArray($ast);
        $this->assertArrayHasKey('when', $ast);
        $this->assertArrayHasKey('then', $ast);
        $this->assertArrayHasKey('else', $ast);
        $this->assertEquals('str_contains(document.content, "invoice")', $ast['when']);
    }

    public function test_parse_rule_with_let(): void
    {
        $service = new RuleService();

        $dsl = <<<'DSL'
WHEN str_contains(document.title, "Invoice")
THEN
LET company = "ACME"
DO addTag(tag: company)
END
DSL;

        $ast = $service->parse($dsl);

        $this->assertCount(2, $ast['then']);
        $this->assertEquals('let', $ast['then'][0]['type']);
        $this->assertEquals('company', $ast['then'][0]['name']);
        $this->assertEquals('do', $ast['then'][1]['type']);
    }

    public function test_parse_rule_with_else(): void
    {
        $service = new RuleService();

        $dsl = <<<'DSL'
WHEN str_contains(document.content, "urgent")
THEN
DO addTag(tag: "urgent")
ELSE
DO addTag(tag: "normal")
END
DSL;

        $ast = $service->parse($dsl);

        $this->assertCount(1, $ast['then']);
        $this->assertCount(1, $ast['else']);
    }

    public function test_dry_run_rule(): void
    {
        $service = new RuleService();
        $document = $this->createMockDocument();

        $dsl = <<<'DSL'
WHEN str_contains(document.content, "invoice")
THEN
DO addTag(tag: "invoice")
END
DSL;

        $result = $service->testRule($dsl, $document);

        $this->assertTrue($result['success'],
            isset($result['errors']) ? implode("\n", $result['errors']) : 'Unknown error'
        );
        $this->assertArrayHasKey('result', $result);
    }

    public function test_parse_error_handling(): void
    {
        $service = new RuleService();

        $dsl = <<<'DSL'
WHEN str_contains(document.content, "test")
DO addTag(tag: "test")
DSL;

        $result = $service->testRule($dsl, $this->createMockDocument());

        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('errors', $result);
    }

    public function test_parse_value_types(): void
    {
        $service = new RuleService();

        $dsl = <<<'DSL'
WHEN true
THEN
DO setCustomField(field: "amount", value: 123.45)
DO setCustomField(field: "paid", value: true)
DO setCustomField(field: "note", value: "test")
END
DSL;

        $ast = $service->parse($dsl);

        $this->assertCount(3, $ast['then']);
        $this->assertEquals(123.45, $ast['then'][0]['args']['value']);
        $this->assertTrue($ast['then'][1]['args']['value']);
        $this->assertEquals('test', $ast['then'][2]['args']['value']);
    }

    public function test_dot_notation_and_array_syntax(): void
    {
        $service = new RuleService();
        $document = $this->createMockDocument();

        // Test with dot notation
        $dslDot = <<<'DSL'
WHEN str_contains(document.content, "invoice")
THEN
DO addTag(tag: "test-dot")
END
DSL;

        $resultDot = $service->testRule($dslDot, $document);
        $this->assertTrue($resultDot['success']);

        // Test with array syntax
        $dslArray = <<<'DSL'
WHEN str_contains(document["content"], "invoice")
THEN
DO addTag(tag: "test-array")
END
DSL;

        $resultArray = $service->testRule($dslArray, $document);
        $this->assertTrue($resultArray['success']);
    }

    public function test_parse_nested_when(): void
    {
        $service = new RuleService();

        $dsl = <<<'DSL'
WHEN str_contains(document.content, "urgent")
THEN
  WHEN str_contains(document.content, "invoice")
  THEN
    DO addTag(tag: "urgent-invoice")
  ELSE
    DO addTag(tag: "urgent-other")
  END
ELSE
  DO addTag(tag: "normal")
END
DSL;

        $ast = $service->parse($dsl);

        $this->assertIsArray($ast);
        $this->assertEquals('str_contains(document.content, "urgent")', $ast['when']);
        $this->assertCount(1, $ast['then']);
        $this->assertEquals('when', $ast['then'][0]['type']);
        $this->assertEquals('str_contains(document.content, "invoice")', $ast['then'][0]['when']);
        $this->assertCount(1, $ast['then'][0]['then']);
        $this->assertCount(1, $ast['then'][0]['else']);
    }

    public function test_execute_nested_when_then_branch(): void
    {
        $service = new RuleService();
        $document = $this->createMockDocument();

        // Document content contains "invoice" but not "urgent"
        $dsl = <<<'DSL'
WHEN str_contains(document.content, "invoice")
THEN
  WHEN str_contains(document.content, "ACME")
  THEN
    DO addTag(tag: "acme-invoice")
  ELSE
    DO addTag(tag: "other-invoice")
  END
ELSE
  DO addTag(tag: "not-invoice")
END
DSL;

        $result = $service->testRule($dsl, $document);

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('trace', $result['result']);

        // Find the nested when trace
        $nestedWhenTrace = null;
        foreach ($result['result']['trace'] as $trace) {
            if ($trace['type'] === 'when' && isset($trace['nested']) && $trace['nested']) {
                $nestedWhenTrace = $trace;
                break;
            }
        }

        $this->assertNotNull($nestedWhenTrace);
        $this->assertTrue($nestedWhenTrace['result']); // ACME is in content
    }

    public function test_execute_nested_when_else_branch(): void
    {
        $service = new RuleService();
        $document = $this->createMockDocument();

        $dsl = <<<'DSL'
WHEN str_contains(document.content, "notfound")
THEN
  DO addTag(tag: "found")
ELSE
  WHEN str_contains(document.content, "invoice")
  THEN
    DO addTag(tag: "invoice-in-else")
  END
END
DSL;

        $result = $service->testRule($dsl, $document);

        $this->assertTrue($result['success']);
    }

    public function test_deeply_nested_when(): void
    {
        $service = new RuleService();

        $dsl = <<<'DSL'
WHEN true
THEN
  WHEN true
  THEN
    WHEN true
    THEN
      DO addTag(tag: "level-3")
    END
  END
END
DSL;

        $ast = $service->parse($dsl);

        // Verify 3 levels of nesting
        $this->assertEquals('when', $ast['then'][0]['type']);
        $this->assertEquals('when', $ast['then'][0]['then'][0]['type']);
        $this->assertEquals('do', $ast['then'][0]['then'][0]['then'][0]['type']);
    }

    public function test_parse_with_comments(): void
    {
        $service = new RuleService();

        $dsl = <<<'DSL'
// This is a comment at the top
WHEN str_contains(document.content, "invoice")
THEN
// Comment before action
DO addTag(tag: "invoice")
// Another comment
DO addTag(tag: "processed")
END
// Comment at the end
DSL;

        $ast = $service->parse($dsl);

        $this->assertIsArray($ast);
        $this->assertEquals('str_contains(document.content, "invoice")', $ast['when']);
        $this->assertCount(2, $ast['then']);
        $this->assertEquals('addTag', $ast['then'][0]['action']);
        $this->assertEquals('addTag', $ast['then'][1]['action']);
    }

    public function test_comments_in_nested_when(): void
    {
        $service = new RuleService();

        $dsl = <<<'DSL'
// Main condition
WHEN str_contains(document.content, "urgent")
THEN
  // Nested condition for invoice check
  WHEN str_contains(document.content, "invoice")
  THEN
    // Add urgent invoice tag
    DO addTag(tag: "urgent-invoice")
  END
END
DSL;

        $ast = $service->parse($dsl);

        $this->assertEquals('when', $ast['then'][0]['type']);
        $this->assertCount(1, $ast['then'][0]['then']);
    }

    public function test_document_type_and_correspondent_properties(): void
    {
        $service = new RuleService();
        $document = $this->createMockDocument();

        // Test document_type property
        $dslDocumentType = <<<'DSL'
WHEN document.document_type == "Invoice"
THEN
DO addTag(tag: "has-invoice-type")
END
DSL;

        $resultDocumentType = $service->testRule($dslDocumentType, $document);
        $this->assertTrue($resultDocumentType['success'],
            isset($resultDocumentType['errors']) ? implode("\n", $resultDocumentType['errors']) : 'Unknown error'
        );

        // Test correspondent property
        $dslCorrespondent = <<<'DSL'
WHEN document.correspondent == "ACME Corp"
THEN
DO addTag(tag: "from-acme")
END
DSL;

        $resultCorrespondent = $service->testRule($dslCorrespondent, $document);
        $this->assertTrue($resultCorrespondent['success'],
            isset($resultCorrespondent['errors']) ? implode("\n", $resultCorrespondent['errors']) : 'Unknown error'
        );

        // Test using str_contains with correspondent
        $dslContains = <<<'DSL'
WHEN str_contains(document.correspondent, "ACME")
THEN
DO addTag(tag: "acme-related")
END
DSL;

        $resultContains = $service->testRule($dslContains, $document);
        $this->assertTrue($resultContains['success'],
            isset($resultContains['errors']) ? implode("\n", $resultContains['errors']) : 'Unknown error'
        );
    }

    public function test_create_actions(): void
    {
        $service = new RuleService();
        $mockService = $this->createMock(PaperlessService::class);

        // Mock findTagByName to return null (tag doesn't exist)
        $mockService->method('findTagByName')
            ->willReturn(null);

        // Mock createTag to return a new tag
        $mockService->method('createTag')
            ->willReturn(['id' => 999, 'name' => 'NewTag']);

        // Mock findDocumentTypeByName to return null
        $mockService->method('findDocumentTypeByName')
            ->willReturn(null);

        // Mock createDocumentType to return a new type
        $mockService->method('createDocumentType')
            ->willReturn(['id' => 888, 'name' => 'NewType']);

        // Mock findCorrespondentByName to return null
        $mockService->method('findCorrespondentByName')
            ->willReturn(null);

        // Mock createCorrespondent to return a new correspondent
        $mockService->method('createCorrespondent')
            ->willReturn(['id' => 777, 'name' => 'NewCorrespondent']);

        $data = [
            'id' => 1,
            'title' => 'Test Document',
            'content' => 'Test content',
            'tags' => [],
            'created_date' => '2024-01-01',
            'modified' => '2024-01-01T10:00:00Z',
            'added' => '2024-01-01T10:00:00Z',
            'original_file_name' => 'test.pdf',
        ];

        $document = new \App\Services\Paperless\Document($mockService, $data);

        // Test createTag
        $dslTag = <<<'DSL'
DO createTag(name: "NewTag")
DSL;

        $resultTag = $service->testRule($dslTag, $document);
        $this->assertTrue($resultTag['success'],
            isset($resultTag['errors']) ? implode("\n", $resultTag['errors']) : 'Unknown error'
        );

        // Test createDocumentType
        $dslType = <<<'DSL'
DO createDocumentType(name: "NewType")
DSL;

        $resultType = $service->testRule($dslType, $document);
        $this->assertTrue($resultType['success'],
            isset($resultType['errors']) ? implode("\n", $resultType['errors']) : 'Unknown error'
        );

        // Test createCorrespondent
        $dslCorrespondent = <<<'DSL'
DO createCorrespondent(name: "NewCorrespondent")
DSL;

        $resultCorrespondent = $service->testRule($dslCorrespondent, $document);
        $this->assertTrue($resultCorrespondent['success'],
            isset($resultCorrespondent['errors']) ? implode("\n", $resultCorrespondent['errors']) : 'Unknown error'
        );
    }

    public function test_create_actions_already_exist(): void
    {
        $service = new RuleService();
        $mockService = $this->createMock(PaperlessService::class);

        // Mock findTagByName to return existing tag
        $mockService->method('findTagByName')
            ->willReturn(['id' => 1, 'name' => 'ExistingTag']);

        // Mock findDocumentTypeByName to return existing type
        $mockService->method('findDocumentTypeByName')
            ->willReturn(['id' => 2, 'name' => 'ExistingType']);

        // Mock findCorrespondentByName to return existing correspondent
        $mockService->method('findCorrespondentByName')
            ->willReturn(['id' => 3, 'name' => 'ExistingCorrespondent']);

        $data = [
            'id' => 1,
            'title' => 'Test Document',
            'content' => 'Test content',
            'tags' => [],
            'created_date' => '2024-01-01',
            'modified' => '2024-01-01T10:00:00Z',
            'added' => '2024-01-01T10:00:00Z',
            'original_file_name' => 'test.pdf',
        ];

        $document = new \App\Services\Paperless\Document($mockService, $data);

        // Test createTag with existing tag
        $dslTag = <<<'DSL'
DO createTag(name: "ExistingTag")
DSL;

        $resultTag = $service->testRule($dslTag, $document);
        $this->assertTrue($resultTag['success'],
            isset($resultTag['errors']) ? implode("\n", $resultTag['errors']) : 'Unknown error'
        );
        // In dry run mode, the action is not actually executed, so we just verify it parses correctly
        $this->assertArrayHasKey('result', $resultTag);
        $this->assertArrayHasKey('trace', $resultTag['result']);
    }

    public function test_string_concatenation(): void
    {
        $service = new RuleService();
        $document = $this->createMockDocument();

        // Test simple string concatenation
        $dsl = <<<'DSL'
LET wort1 = "Hallo"
LET wort2 = "Welt"
LET satz = wort1 ~ " " ~ wort2
DO setTitle(title: satz)
DSL;

        $result = $service->testRule($dsl, $document);

        $this->assertTrue($result['success'],
            isset($result['errors']) ? implode("\n", $result['errors']) : 'Unknown error'
        );

        // Check that the trace contains the expected title
        $trace = $result['result']['trace'];
        $setTitleAction = null;
        foreach ($trace as $item) {
            if ($item['type'] === 'do' && $item['action'] === 'setTitle') {
                $setTitleAction = $item;
                break;
            }
        }

        $this->assertNotNull($setTitleAction);
        $this->assertEquals('Hallo Welt', $setTitleAction['args']['title']);
    }

    public function test_string_concatenation_with_document_property(): void
    {
        $service = new RuleService();
        $document = $this->createMockDocument();

        // Test concatenation with document property
        $dsl = <<<'DSL'
LET wort1 = "Hallo"
LET wort2 = "wie"
LET title = document.title
LET satz = wort1 ~ " " ~ wort2 ~ " | " ~ title
DO setCustomField(field: "greeting", value: satz)
DSL;

        $result = $service->testRule($dsl, $document);

        $this->assertTrue($result['success'],
            isset($result['errors']) ? implode("\n", $result['errors']) : 'Unknown error'
        );

        // Check that the trace contains the expected value
        $trace = $result['result']['trace'];
        $setCustomFieldAction = null;
        foreach ($trace as $item) {
            if ($item['type'] === 'do' && $item['action'] === 'setCustomField') {
                $setCustomFieldAction = $item;
                break;
            }
        }

        $this->assertNotNull($setCustomFieldAction);
        $this->assertEquals('Hallo wie | Test Invoice', $setCustomFieldAction['args']['value']);
    }

    public function test_string_concatenation_in_conditional(): void
    {
        $service = new RuleService();
        $document = $this->createMockDocument();

        // Test concatenation in WHEN/THEN block
        $dsl = <<<'DSL'
WHEN str_contains(document.content, "invoice")
THEN
LET prefix = "Invoice_"
LET suffix = "_2024"
LET newTitle = prefix ~ document.title ~ suffix
DO setTitle(title: newTitle)
END
DSL;

        $result = $service->testRule($dsl, $document);

        $this->assertTrue($result['success'],
            isset($result['errors']) ? implode("\n", $result['errors']) : 'Unknown error'
        );

        // Check that the trace contains the expected title
        $trace = $result['result']['trace'];
        $setTitleAction = null;
        foreach ($trace as $item) {
            if ($item['type'] === 'do' && $item['action'] === 'setTitle') {
                $setTitleAction = $item;
                break;
            }
        }

        $this->assertNotNull($setTitleAction);
        $this->assertEquals('Invoice_Test Invoice_2024', $setTitleAction['args']['title']);
    }

    public function test_lowercase_keywords_are_accepted(): void
    {
        $service = new RuleService();
        $document = $this->createMockDocument();

        // Test with lowercase keywords
        $dsl = <<<'DSL'
when str_contains(document.content, "invoice")
then
let company = "ACME"
do addTag(tag: company)
else
do addTag(tag: "other")
end
DSL;

        $result = $service->testRule($dsl, $document);

        $this->assertTrue($result['success'],
            isset($result['errors']) ? implode("\n", $result['errors']) : 'Unknown error'
        );
    }

    public function test_mixed_case_keywords_are_accepted(): void
    {
        $service = new RuleService();
        $document = $this->createMockDocument();

        // Test with mixed case keywords
        $dsl = <<<'DSL'
When str_contains(document.content, "invoice")
Then
Let company = "ACME"
Do addTag(tag: company)
Else
Do addTag(tag: "other")
End
DSL;

        $result = $service->testRule($dsl, $document);

        $this->assertTrue($result['success'],
            isset($result['errors']) ? implode("\n", $result['errors']) : 'Unknown error'
        );
    }
}

