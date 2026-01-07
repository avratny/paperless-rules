# Variables - Reference

Variables contain information about the document and can be used in conditions and actions.

## Document Properties

| Variable | Type | Description | Example |
|----------|------|-------------|---------|
| `document.id` | Number | Document ID | `123` |
| `document.title` | String | Document title | `"Invoice_2024.pdf"` |
| `document.content` | String | Text content (OCR) | `"Invoice No. 12345..."` |
| `document.tags` | Array | List of tags | `["Invoice", "Amazon"]` |
| `document.created_date` | String | Creation date | `"2024-01-15"` |
| `document.modified` | String | Modification date | `"2024-01-16"` |
| `document.added` | String | Added date | `"2024-01-15"` |
| `document.original_file_name` | String | Original filename | `"scan_001.pdf"` |
| `document.archive_serial_number` | Number | Archive serial number (ASN) | `42` |
| `document.document_type` | String | Document type | `"Invoice"` |
| `document.correspondent` | String | Correspondent | `"Amazon"` |

## Context Variables

| Variable | Type | Description | Example |
|----------|------|-------------|---------|
| `now` | String | Current date/time | `"2024-01-15 14:30:00"` |

## Define Custom Variables

Use `LET` to define custom variables:

```dsl
LET company = "Amazon"
LET prefix = "Invoice - "
LET newTitle = prefix ~ company
  DO setTitle(title: newTitle)
```

## Null Values

Unset values are `null`:

```dsl
WHEN document.correspondent == null
  DO setCorrespondent(correspondent: "Unknown")
END
```

## Array Access

Tags are an array and can be used with array functions:

```dsl
WHEN count(document.tags) > 5
  DO removeTag(tag: "temp")
END
```

```dsl
WHEN in("Invoice", document.tags)
  DO setDocumentType(document_type: "Invoice")
END
```

## Examples

### Use title
```dsl
WHEN str_contains(document.title, "Amazon")
  DO setCorrespondent(correspondent: "Amazon")
END
```

### Search content
```dsl
WHEN str_contains(document.content, "Invoice")
  DO addTag(tag: "Invoice")
END
```

### Check date
```dsl
WHEN document.created_date != null
  DO setTitle(title: document.correspondent ~ " - " ~ document.created_date)
END
```

### Check tags
```dsl
WHEN count(document.tags) == 0
  DO addTag(tag: "Untagged")
END
```

### Check correspondent
```dsl
WHEN document.correspondent == null
  DO setCorrespondent(correspondent: "Unknown")
END
```

### Use ASN
```dsl
WHEN document.archive_serial_number != null
  DO setTitle(title: "ASN-" ~ document.archive_serial_number ~ " - " ~ document.title)
END
```

## Notes

- All string values are **case-sensitive**
- Use `lower()` or `upper()` for case-insensitive comparisons
- `null` means "not set"
- Arrays can be checked with `count()` and `in()`
- Strings can be concatenated with `~`

**For detailed explanations see:** [Variables → document](document.md)

