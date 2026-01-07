# Actions - Reference

Actions modify the document. They are used in the `DO` block.

## Tag Actions

| Action | Description | Parameters | Example |
|--------|-------------|------------|---------|
| `addTag(tag: "name")` | Adds tag | `tag`: Tag name | `addTag(tag: "Invoice")` |
| `removeTag(tag: "name")` | Removes tag | `tag`: Tag name | `removeTag(tag: "temp")` |
| `createTag(name: "name")` | Creates tag (if not exists) | `name`: Tag name | `createTag(name: "New")` |

## Document Type Actions

| Action | Description | Parameters | Example |
|--------|-------------|------------|---------|
| `setDocumentType(document_type: "name")` | Sets document type | `document_type`: Type name | `setDocumentType(document_type: "Invoice")` |
| `createDocumentType(name: "name")` | Creates document type (if not exists) | `name`: Type name | `createDocumentType(name: "Letter")` |

## Correspondent Actions

| Action | Description | Parameters | Example |
|--------|-------------|------------|---------|
| `setCorrespondent(correspondent: "name")` | Sets correspondent | `correspondent`: Name | `setCorrespondent(correspondent: "Amazon")` |
| `createCorrespondent(name: "name")` | Creates correspondent (if not exists) | `name`: Name | `createCorrespondent(name: "ACME Corp")` |

## Title Action

| Action | Description | Parameters | Example |
|--------|-------------|------------|---------|
| `setTitle(title: "text")` | Sets title | `title`: New title | `setTitle(title: "Invoice - Amazon")` |

## Custom Field Action

| Action | Description | Parameters | Example |
|--------|-------------|------------|---------|
| `setCustomField(field: "name", value: "value")` | Sets custom field | `field`: Field name<br>`value`: Value | `setCustomField(field: "status", value: "paid")` |

## Multiple Actions

Actions can be combined:

```dsl
WHEN str_contains(document.content, "Invoice")
  DO addTag(tag: "Invoice")
  DO setDocumentType(document_type: "Invoice")
  DO setTitle(title: "Invoice - " ~ document.correspondent)
END
```

## Variables in Actions

Variables can be used in actions:

```dsl
LET company = "Amazon"
  DO setCorrespondent(correspondent: company)
  DO addTag(tag: company)
  DO setTitle(title: "Invoice - " ~ company)
```

## Create vs. Set

**Set actions** require that the value already exists:
- `setDocumentType()` - Document type must exist
- `setCorrespondent()` - Correspondent must exist
- `addTag()` - Tag must exist

**Create actions** create the value if it doesn't exist:
- `createDocumentType()` - Creates document type automatically
- `createCorrespondent()` - Creates correspondent automatically
- `createTag()` - Creates tag automatically

## Examples

### Add tag
```dsl
  DO addTag(tag: "Invoice")
```

### Set title with variables
```dsl
  DO setTitle(title: "Invoice - " ~ document.correspondent ~ " - " ~ document.created_date)
```

### Create and set correspondent
```dsl
  DO createCorrespondent(name: "ACME Corp")
  DO setCorrespondent(correspondent: "ACME Corp")
```

### Set custom field
```dsl
  DO setCustomField(field: "invoice_number", value: "INV-12345")
```

### Multiple tags
```dsl
  DO addTag(tag: "Invoice")
  DO addTag(tag: "Important")
  DO addTag(tag: "2024")
```

## Notes

- Actions are executed in the order they are defined
- `create*` actions are idempotent (can be executed multiple times)
- `set*` actions overwrite existing values
- `addTag()` only adds if tag is not already present
- `removeTag()` only removes if tag is present

**For detailed explanations see:** Individual action pages like [setTitle](setTitle.md), [setCorrespondent](setCorrespondent.md)

