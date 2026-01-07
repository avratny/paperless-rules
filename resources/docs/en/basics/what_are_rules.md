# What are Rules?

Rules are **automatic instructions** that Paperless Rules executes when a document is uploaded or changed.

## How does it work?

Think of a rule like an **if-then statement**:

> **IF** the document contains the text "Invoice",  
> **THEN** add the tag "Invoice".

That's it! That's how simple a rule is.

## Structure of a Rule

Every rule consists of three parts:

### 1. **WHEN** - The Condition
What must be fulfilled for the rule to be executed?

```dsl
WHEN str_contains(document.content, "Invoice")
```

**In simple words:**  
"If the document content contains the word 'Invoice'..."

### 2. **DO** - The Action
What should happen when the condition is met?

```dsl
  DO addTag(tag: "Invoice")
```

**In simple words:**  
"...then add the tag 'Invoice'."

### 3. **END** - The End
Marks the end of the rule.

```dsl
END
```

## Complete Example

```dsl
WHEN str_contains(document.content, "Invoice")
  DO addTag(tag: "Invoice")
END
```

**What this rule does:**
1. Checks if the word "Invoice" appears in the document
2. If yes: Adds the tag "Invoice"
3. If no: Does nothing

## Multiple Actions

You can also execute multiple actions:

```dsl
WHEN str_contains(document.content, "Invoice")
  DO addTag(tag: "Invoice")
  DO setDocumentType(document_type: "Invoice")
  DO setTitle("Invoice - " + document.correspondent)
END
```

**What this rule does:**
1. Checks if "Invoice" is in the document
2. If yes:
   - Adds tag "Invoice"
   - Sets document type to "Invoice"
   - Changes the title to "Invoice - [Sender]"

## When are Rules Executed?

Rules are automatically executed:

- **For new documents** - When you upload a document  
- **For changes** - When you edit a document

You can specify for each rule when it should be executed:
- Only for new documents
- Only for changes
- For both

## Tip

Start with simple rules! You can always expand them later.

**Next step:** Check out the **Variables** to see what you can read from a document.

