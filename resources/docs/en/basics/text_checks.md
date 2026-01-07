# Text Checks

With text checks you can verify if certain words or texts appear in your document.

## Text contains - `str_contains()`

Checks if a text contains a specific search term.

### Syntax
```dsl
str_contains(text, "searchterm")
```

### Examples

#### Simple search
```dsl
WHEN str_contains(document.content, "Invoice")
  DO addTag(tag: "Invoice")
END
```

**What does this do?**  
If the word "Invoice" appears anywhere in the document, the tag is added.

---

#### Search in title
```dsl
WHEN str_contains(document.title, "Amazon")
  DO setCorrespondent(correspondent: "Amazon")
END
```

**What does this do?**  
If "Amazon" is in the title, the correspondent is set.

---

## Text starts with - `str_starts_with()`

Checks if a text starts with a specific word.

### Syntax
```dsl
str_starts_with(text, "beginning")
```

### Example

```dsl
WHEN str_starts_with(document.title, "Invoice")
  DO setDocumentType(document_type: "Invoice")
END
```

**What does this do?**  
If the title starts with "Invoice", the document type is set.

---

## Case Sensitivity

### Problem
By default, case is distinguished:
- "Invoice" ≠ "invoice"
- "INVOICE" ≠ "Invoice"

### Solution: Convert everything to lowercase

```dsl
WHEN str_contains(lower(document.content), "invoice")
  DO addTag(tag: "Invoice")
END
```

**What does this do?**  
- `lower()` converts the text to lowercase
- Now finds: "Invoice", "invoice", "INVOICE", etc.

---

## Practical Examples

### Search for multiple terms

```dsl
WHEN str_contains(document.content, "Invoice") OR str_contains(document.content, "Bill")
  DO addTag(tag: "Invoice")
END
```

**What does this do?**  
Searches for "Invoice" OR "Bill".

---

### Combination with other conditions

```dsl
WHEN str_contains(document.content, "Invoice") AND document.correspondent = "Amazon"
  DO setTitle("Invoice - Amazon")
  DO addTag(tag: "Invoice")
END
```

**What does this do?**  
Only if BOTH conditions are met, the actions are executed.

---

## Summary

| Function | What does it do? | Example |
|----------|------------------|---------|
| `str_contains()` | Checks if text is contained | `str_contains(document.content, "Invoice")` |
| `str_starts_with()` | Checks if text starts with | `str_starts_with(document.title, "RE:")` |
| `lower()` | Converts to lowercase | `lower(document.title)` |
| `upper()` | Converts to uppercase | `upper(document.title)` |

