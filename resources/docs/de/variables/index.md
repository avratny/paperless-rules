# Variablen - Referenz

Variablen enthalten Informationen über das Dokument und können in Bedingungen und Aktionen verwendet werden.

## Document-Eigenschaften

| Variable | Typ | Beschreibung | Beispiel |
|----------|-----|--------------|----------|
| `document.id` | Number | Dokument-ID | `123` |
| `document.title` | String | Titel des Dokuments | `"Rechnung_2024.pdf"` |
| `document.content` | String | Textinhalt (OCR) | `"Rechnung Nr. 12345..."` |
| `document.tags` | Array | Liste der Tags | `["Rechnung", "Telekom"]` |
| `document.created_date` | String | Erstellungsdatum | `"2024-01-15"` |
| `document.modified` | String | Änderungsdatum | `"2024-01-16"` |
| `document.added` | String | Hinzugefügt am | `"2024-01-15"` |
| `document.original_file_name` | String | Original-Dateiname | `"scan_001.pdf"` |
| `document.archive_serial_number` | Number | Archiv-Seriennummer (ASN) | `42` |
| `document.document_type` | String | Dokumenttyp | `"Rechnung"` |
| `document.correspondent` | String | Korrespondent | `"Telekom"` |

## Kontext-Variablen

| Variable | Typ | Beschreibung | Beispiel |
|----------|-----|--------------|----------|
| `now` | String | Aktuelles Datum/Zeit | `"2024-01-15 14:30:00"` |

## Eigene Variablen definieren

Mit `LET` können eigene Variablen definiert werden:

```dsl
LET company = "Amazon"
LET prefix = "Rechnung - "
LET newTitle = prefix ~ company
  DO setTitle(title: newTitle)
```

## Null-Werte

Nicht gesetzte Werte sind `null`:

```dsl
WHEN document.correspondent == null
  DO setCorrespondent(correspondent: "Unbekannt")
END
```

## Array-Zugriff

Tags sind ein Array und können mit Array-Funktionen verwendet werden:

```dsl
WHEN count(document.tags) > 5
  DO removeTag(tag: "temp")
END
```

```dsl
WHEN in("Rechnung", document.tags)
  DO setDocumentType(document_type: "Rechnung")
END
```

## Beispiele

### Titel verwenden
```dsl
WHEN str_contains(document.title, "Amazon")
  DO setCorrespondent(correspondent: "Amazon")
END
```

### Inhalt durchsuchen
```dsl
WHEN str_contains(document.content, "Rechnung")
  DO addTag(tag: "Rechnung")
END
```

### Datum prüfen
```dsl
WHEN document.created_date != null
  DO setTitle(title: document.correspondent ~ " - " ~ document.created_date)
END
```

### Tags prüfen
```dsl
WHEN count(document.tags) == 0
  DO addTag(tag: "Untagged")
END
```

### Korrespondent prüfen
```dsl
WHEN document.correspondent == null
  DO setCorrespondent(correspondent: "Unbekannt")
END
```

### ASN verwenden
```dsl
WHEN document.archive_serial_number != null
  DO setTitle(title: "ASN-" ~ document.archive_serial_number ~ " - " ~ document.title)
END
```

## Hinweise

- Alle String-Werte sind **case-sensitive**
- Verwende `lower()` oder `upper()` für case-insensitive Vergleiche
- `null` bedeutet "nicht gesetzt"
- Arrays können mit `count()` und `in()` geprüft werden
- Strings können mit `~` verknüpft werden

**Für ausführliche Erklärungen siehe:** [Variables → document](document.md)

