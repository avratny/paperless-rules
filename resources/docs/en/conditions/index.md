# Conditions - Reference

Conditions determine when a rule is executed. They are used in the `WHEN` block.

## Comparison Operators

| Operator | Description | Example |
|----------|-------------|---------|
| `==` | Equal | `document.title == "Invoice"` |
| `!=` | Not equal | `document.correspondent != null` |
| `>` | Greater than | `len(document.title) > 10` |
| `<` | Less than | `len(document.title) < 50` |
| `>=` | Greater or equal | `count(document.tags) >= 2` |
| `<=` | Less or equal | `count(document.tags) <= 5` |

## Logical Operators

| Operator | Description | Example |
|----------|-------------|---------|
| `and` | AND - Both conditions must be true | `condition1 and condition2` |
| `or` | OR - At least one condition must be true | `condition1 or condition2` |
| `not` | NOT - Negates the condition | `not condition` |

## String Functions

| Function | Description | Parameters | Returns |
|----------|-------------|------------|---------|
| `str_contains(haystack, needle)` | Checks if text contains | Text, Search term | Boolean |
| `str_starts_with(str, prefix)` | Checks if text starts with | Text, Prefix | Boolean |
| `str_ends_with(str, suffix)` | Checks if text ends with | Text, Suffix | Boolean |
| `lower(str)` | Converts to lowercase | Text | String |
| `upper(str)` | Converts to uppercase | Text | String |
| `trim(str)` | Removes whitespace at start/end | Text | String |
| `len(str)` | Returns text length | Text | Number |
| `replace(str, search, replace)` | Replaces text | Text, Search, Replace | String |
| `regex(str, pattern)` | Regex match | Text, Pattern | Boolean |

## Array Functions

| Function | Description | Parameters | Returns |
|----------|-------------|------------|---------|
| `in(needle, haystack)` | Checks if value is in array | Value, Array | Boolean |
| `count(array)` | Counts array elements | Array | Number |

## Date Functions

| Function | Description | Parameters | Returns |
|----------|-------------|------------|---------|
| `reformatDate(date, format)` | Reformats date | Date, Format | String |

## AI Functions

| Function | Description | Parameters | Returns |
|----------|-------------|------------|---------|
| `askOllamaAi(prompt)` | Asks Ollama AI with prompt | Prompt text | String |
| `askOllamaAiForCreationDate(content)` | Extracts creation date | Document content | String |
| `askOllamaAiForDocumentNumber(content)` | Extracts document number | Document content | String |

## Examples

### Simple comparison
```dsl
WHEN document.correspondent == "Amazon"
  DO addTag(tag: "Amazon")
END
```

### Logical combination
```dsl
WHEN str_contains(document.content, "Invoice") and document.correspondent == "Amazon"
  DO setTitle("Invoice - Amazon")
END
```

### Negation
```dsl
WHEN not str_contains(document.content, "Draft")
  DO addTag(tag: "Final")
END
```

### Array check
```dsl
WHEN in("Invoice", document.tags)
  DO setDocumentType(document_type: "Invoice")
END
```

### Length check
```dsl
WHEN len(document.title) > 100
  DO setTitle(title: trim(document.title))
END
```

## Notes

- Comparisons are **case-sensitive**
- Use `lower()` or `upper()` for case-insensitive comparisons
- `null` can be checked with `== null` or `!= null`
- Conditions can be nested arbitrarily

**For detailed explanations see:** [Basics → Text checks](../basics/text_checks.md)

