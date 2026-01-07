<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Rule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Rules\Editor;

class RuleEditorTest extends TestCase
{
    use RefreshDatabase;

    public function test_keywords_are_uppercased_on_save(): void
    {
        // Create a rule with lowercase keywords
        $dslWithLowercase = <<<'DSL'
when str_contains(document.content, "invoice")
then
let company = "ACME"
do addTag(tag: company)
else
do addTag(tag: "other")
end
DSL;

        Livewire::test(Editor::class)
            ->set('name', 'Test Rule')
            ->set('enabled', true)
            ->set('on_create', true)
            ->set('on_change', false)
            ->set('order', 0)
            ->set('ruleContent', $dslWithLowercase)
            ->call('save');

        // Check that the rule was created
        $this->assertDatabaseHas('rules', [
            'name' => 'Test Rule',
        ]);

        // Get the created rule
        $rule = Rule::where('name', 'Test Rule')->first();

        // Check that keywords are uppercased
        $this->assertStringContainsString('WHEN', $rule->rule);
        $this->assertStringContainsString('THEN', $rule->rule);
        $this->assertStringContainsString('LET', $rule->rule);
        $this->assertStringContainsString('DO', $rule->rule);
        $this->assertStringContainsString('ELSE', $rule->rule);
        $this->assertStringContainsString('END', $rule->rule);

        // Check that the content is preserved (case-sensitive parts)
        $this->assertStringContainsString('str_contains', $rule->rule);
        $this->assertStringContainsString('document.content', $rule->rule);
        $this->assertStringContainsString('"ACME"', $rule->rule);
    }

    public function test_mixed_case_keywords_are_normalized(): void
    {
        $dslWithMixedCase = <<<'DSL'
When str_contains(document.content, "test")
Then
Do addTag(tag: "test")
End
DSL;

        Livewire::test(Editor::class)
            ->set('name', 'Mixed Case Rule')
            ->set('enabled', true)
            ->set('on_create', true)
            ->set('on_change', false)
            ->set('order', 0)
            ->set('ruleContent', $dslWithMixedCase)
            ->call('save');

        $rule = Rule::where('name', 'Mixed Case Rule')->first();

        // All keywords should be uppercase
        $this->assertStringContainsString('WHEN', $rule->rule);
        $this->assertStringContainsString('THEN', $rule->rule);
        $this->assertStringContainsString('DO', $rule->rule);
        $this->assertStringContainsString('END', $rule->rule);

        // Function names should be preserved
        $this->assertStringContainsString('str_contains', $rule->rule);
    }

    public function test_comments_are_preserved(): void
    {
        $dslWithComments = <<<'DSL'
// This is a comment
when str_contains(document.content, "test")
then
// Another comment
do addTag(tag: "test")
end
DSL;

        Livewire::test(Editor::class)
            ->set('name', 'Rule with Comments')
            ->set('enabled', true)
            ->set('on_create', true)
            ->set('on_change', false)
            ->set('order', 0)
            ->set('ruleContent', $dslWithComments)
            ->call('save');

        $rule = Rule::where('name', 'Rule with Comments')->first();

        // Comments should be preserved
        $this->assertStringContainsString('// This is a comment', $rule->rule);
        $this->assertStringContainsString('// Another comment', $rule->rule);

        // Keywords should still be uppercase
        $this->assertStringContainsString('WHEN', $rule->rule);
        $this->assertStringContainsString('DO', $rule->rule);
    }
}

