---
title: document
---

# Paperless Document

In Paperless Rules, the document that the rule refers to is directly available in the rules. It can be referenced in the rule definition with `document`.

## Properties    

| Property | Description | Type |
|----------|-------------|------|
| `id` | Document ID | `int` |
| `title` | Document title | `string` |
| `content` | Document content (OCR text) | `string` |
| `tags` | Array of tag names | `array` |
| `created_date` | Creation date | `Carbon` |
| `modified` | Modified date | `Carbon` |
| `added` | Added date | `Carbon` |
| `original_file_name` | Original file name | `string` |
| `archive_serial_number` | Archive serial number | `string` |
| `document_type` | Document type name (String) | `string` |
| `correspondent` | Correspondent name (String) | `string` |

## Example

```dsl
WHEN str_contains(document.content, "Invoice")
THEN
  DO addTag(tag: "invoice")
END
```

