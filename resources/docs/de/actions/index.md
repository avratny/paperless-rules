# Aktionen - Referenz

Aktionen ändern das Dokument. Sie werden im `DO`-Block verwendet.

## Tag-Aktionen

| Aktion | Beschreibung | Parameter | Beispiel |
|--------|--------------|-----------|----------|
| `addTag(tag: "name")` | Fügt Tag hinzu | `tag`: Tag-Name | `addTag(tag: "Rechnung")` |
| `removeTag(tag: "name")` | Entfernt Tag | `tag`: Tag-Name | `removeTag(tag: "temp")` |
| `createTag(name: "name")` | Erstellt Tag (falls nicht vorhanden) | `name`: Tag-Name | `createTag(name: "Neu")` |

## Dokumenttyp-Aktionen

| Aktion | Beschreibung | Parameter | Beispiel |
|--------|--------------|-----------|----------|
| `setDocumentType(document_type: "name")` | Setzt Dokumenttyp | `document_type`: Typ-Name | `setDocumentType(document_type: "Rechnung")` |
| `createDocumentType(name: "name")` | Erstellt Dokumenttyp (falls nicht vorhanden) | `name`: Typ-Name | `createDocumentType(name: "Brief")` |

## Korrespondent-Aktionen

| Aktion | Beschreibung | Parameter | Beispiel |
|--------|--------------|-----------|----------|
| `setCorrespondent(correspondent: "name")` | Setzt Korrespondent | `correspondent`: Name | `setCorrespondent(correspondent: "Amazon")` |
| `createCorrespondent(name: "name")` | Erstellt Korrespondent (falls nicht vorhanden) | `name`: Name | `createCorrespondent(name: "ACME Corp")` |

## Titel-Aktion

| Aktion | Beschreibung | Parameter | Beispiel |
|--------|--------------|-----------|----------|
| `setTitle(title: "text")` | Setzt Titel | `title`: Neuer Titel | `setTitle(title: "Rechnung - Amazon")` |

## Custom Field-Aktion

| Aktion | Beschreibung | Parameter | Beispiel |
|--------|--------------|-----------|----------|
| `setCustomField(field: "name", value: "wert")` | Setzt Custom Field | `field`: Feld-Name<br>`value`: Wert | `setCustomField(field: "status", value: "paid")` |

## Mehrere Aktionen

Aktionen können kombiniert werden:

```dsl
WHEN str_contains(document.content, "Rechnung")
  DO addTag(tag: "Rechnung")
  DO setDocumentType(document_type: "Rechnung")
  DO setTitle(title: "Rechnung - " ~ document.correspondent)
END
```

## Variablen in Aktionen

Variablen können in Aktionen verwendet werden:

```dsl
LET company = "Amazon"
  DO setCorrespondent(correspondent: company)
  DO addTag(tag: company)
  DO setTitle(title: "Rechnung - " ~ company)
```

## Create vs. Set

**Set-Aktionen** setzen voraus, dass der Wert bereits existiert:
- `setDocumentType()` - Dokumenttyp muss existieren
- `setCorrespondent()` - Korrespondent muss existieren
- `addTag()` - Tag muss existieren

**Create-Aktionen** erstellen den Wert, falls er nicht existiert:
- `createDocumentType()` - Erstellt Dokumenttyp automatisch
- `createCorrespondent()` - Erstellt Korrespondent automatisch
- `createTag()` - Erstellt Tag automatisch

## Beispiele

### Tag hinzufügen
```dsl
  DO addTag(tag: "Rechnung")
```

### Titel setzen mit Variablen
```dsl
  DO setTitle(title: "Rechnung - " ~ document.correspondent ~ " - " ~ document.created_date)
```

### Korrespondent erstellen und setzen
```dsl
  DO createCorrespondent(name: "ACME Corp")
  DO setCorrespondent(correspondent: "ACME Corp")
```

### Custom Field setzen
```dsl
  DO setCustomField(field: "invoice_number", value: "INV-12345")
```

### Mehrere Tags
```dsl
  DO addTag(tag: "Rechnung")
  DO addTag(tag: "Wichtig")
  DO addTag(tag: "2024")
```

## Hinweise

- Aktionen werden in der Reihenfolge ausgeführt, in der sie definiert sind
- `create*`-Aktionen sind idempotent (können mehrfach ausgeführt werden)
- `set*`-Aktionen überschreiben vorhandene Werte
- `addTag()` fügt nur hinzu, wenn Tag noch nicht vorhanden
- `removeTag()` entfernt nur, wenn Tag vorhanden ist

**Für ausführliche Erklärungen siehe:** Einzelne Action-Seiten wie [setTitle](setTitle.md), [setCorrespondent](setCorrespondent.md)

