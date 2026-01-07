# Automatically Sort Invoices

This rule automatically recognizes invoices and organizes them.

## What does this rule do?

When a document contains the word "Invoice":
1. Adds the tag "Invoice"
2. Sets the document type to "Invoice"
3. Adjusts the title

## The Rule

```dsl
WHEN str_contains(document.content, "Invoice")
  DO addTag(tag: "Invoice")
  DO setDocumentType(document_type: "Invoice")
  DO setTitle("Invoice - " + document.correspondent)
END
```

## Explained Step by Step

### Line 1: The Condition
```dsl
WHEN str_contains(document.content, "Invoice")
```

**What happens here?**
- `str_contains()` searches for text
- `document.content` is the document content
- `"Invoice"` is the search term

**In simple words:**  
"If the word 'Invoice' appears in the document..."

---

### Line 2: Add Tag
```dsl
  DO addTag(tag: "Invoice")
```

**What happens here?**
- Adds the tag "Invoice" to the document
- The tag must already exist in Paperless

---

### Line 3: Set Document Type
```dsl
  DO setDocumentType(document_type: "Invoice")
```

**What happens here?**
- Sets the document type to "Invoice"
- The document type must already exist in Paperless

---

### Line 4: Adjust Title
```dsl
  DO setTitle("Invoice - " + document.correspondent)
```

**What happens here?**
- Sets a new title
- `~` connects texts (string concatenation)
- `document.correspondent` is the sender

**Example result:**  
If the correspondent is "Amazon", the title becomes: "Invoice - Amazon"

---

## Extended Version

### Also recognize invoices in other languages

```dsl
WHEN str_contains(document.content, "Invoice") OR str_contains(document.content, "Rechnung")
  DO addTag(tag: "Invoice")
  DO setDocumentType(document_type: "Invoice")
  DO setTitle("Invoice - " + document.correspondent)
END
```

**What's new?**
- Searches for "Invoice" OR "Rechnung"
- Works for English and German invoices

---

### Ignore case

```dsl
WHEN str_contains(lower(document.content), "invoice")
  DO addTag(tag: "Invoice")
  DO setDocumentType(document_type: "Invoice")
  DO setTitle("Invoice - " + document.correspondent)
END
```

**What's new?**
- `lower()` converts everything to lowercase
- Now finds: "Invoice", "invoice", "INVOICE", etc.

---

## Tips

### Tip 1: Tags and document types must exist
Make sure the tags and document types are already created in Paperless!

### Tip 2: Test the rule
Use the "Execute on Document" function to test the rule before activating it.

### Tip 3: Start simple
Begin with a simple version and expand it gradually.

---

## Related Topics

- **Text Checks** - More about `str_contains()`
- **Comparisons** - More about conditions
- **Actions** - All available actions

