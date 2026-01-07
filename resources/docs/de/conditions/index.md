# Bedingungen - Referenz

Bedingungen bestimmen, wann eine Regel ausgeführt wird. Sie werden im `WHEN`-Block verwendet.

## Vergleichsoperatoren

| Operator | Beschreibung | Beispiel |
|----------|--------------|----------|
| `==` | Gleich | `document.title == "Rechnung"` |
| `!=` | Ungleich | `document.correspondent != null` |
| `>` | Größer als | `len(document.title) > 10` |
| `<` | Kleiner als | `len(document.title) < 50` |
| `>=` | Größer oder gleich | `count(document.tags) >= 2` |
| `<=` | Kleiner oder gleich | `count(document.tags) <= 5` |

## Logische Operatoren

| Operator | Beschreibung | Beispiel |
|----------|--------------|----------|
| `and` | UND - Beide Bedingungen müssen wahr sein | `condition1 and condition2` |
| `or` | ODER - Mindestens eine Bedingung muss wahr sein | `condition1 or condition2` |
| `not` | NICHT - Negiert die Bedingung | `not condition` |

## String-Funktionen

| Funktion | Beschreibung | Parameter | Rückgabe |
|----------|--------------|-----------|----------|
| `str_contains(haystack, needle)` | Prüft ob Text enthalten ist | Text, Suchbegriff | Boolean |
| `str_starts_with(str, prefix)` | Prüft ob Text beginnt mit | Text, Präfix | Boolean |
| `str_ends_with(str, suffix)` | Prüft ob Text endet mit | Text, Suffix | Boolean |
| `lower(str)` | Konvertiert zu Kleinbuchstaben | Text | String |
| `upper(str)` | Konvertiert zu Großbuchstaben | Text | String |
| `trim(str)` | Entfernt Leerzeichen am Anfang/Ende | Text | String |
| `len(str)` | Gibt Länge des Textes zurück | Text | Number |
| `replace(str, search, replace)` | Ersetzt Text | Text, Suche, Ersatz | String |
| `regex(str, pattern)` | Regex-Match | Text, Pattern | Boolean |

## Array-Funktionen

| Funktion | Beschreibung | Parameter | Rückgabe |
|----------|--------------|-----------|----------|
| `in(needle, haystack)` | Prüft ob Wert in Array enthalten ist | Wert, Array | Boolean |
| `count(array)` | Zählt Array-Elemente | Array | Number |

## Datum-Funktionen

| Funktion | Beschreibung | Parameter | Rückgabe |
|----------|--------------|-----------|----------|
| `reformatDate(date, format)` | Formatiert Datum um | Datum, Format | String |

## AI-Funktionen

| Funktion | Beschreibung | Parameter | Rückgabe |
|----------|--------------|-----------|----------|
| `askOllamaAi(prompt)` | Fragt Ollama AI mit Prompt | Prompt-Text | String |
| `askOllamaAiForCreationDate(content)` | Extrahiert Erstellungsdatum | Dokument-Inhalt | String |
| `askOllamaAiForDocumentNumber(content)` | Extrahiert Dokumentnummer | Dokument-Inhalt | String |

## Beispiele

### Einfacher Vergleich
```dsl
WHEN document.correspondent == "Amazon"
  DO addTag(tag: "Amazon")
END
```

### Logische Verknüpfung
```dsl
WHEN str_contains(document.content, "Rechnung") and document.correspondent == "Telekom"
  DO setTitle("Rechnung - Telekom")
END
```

### Negation
```dsl
WHEN not str_contains(document.content, "Entwurf")
  DO addTag(tag: "Final")
END
```

### Array-Prüfung
```dsl
WHEN in("Rechnung", document.tags)
  DO setDocumentType(document_type: "Rechnung")
END
```

### Längen-Prüfung
```dsl
WHEN len(document.title) > 100
  DO setTitle(title: trim(document.title))
END
```

## Hinweise

- Vergleiche sind **case-sensitive** (Groß-/Kleinschreibung wird beachtet)
- Verwende `lower()` oder `upper()` für case-insensitive Vergleiche
- `null` kann mit `== null` oder `!= null` geprüft werden
- Bedingungen können beliebig verschachtelt werden

**Für ausführliche Erklärungen siehe:** [Grundlagen → Text prüfen](../basics/text_pruefen.md) und [Grundlagen → Vergleiche](../basics/vergleiche.md)

