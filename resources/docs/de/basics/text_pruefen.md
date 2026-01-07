# Text prüfen

Mit Text-Prüfungen kannst du überprüfen, ob bestimmte Wörter oder Texte in deinem Dokument vorkommen.

## Text enthält - `str_contains()`

Prüft, ob ein Text einen bestimmten Suchbegriff enthält.

### Syntax
```dsl
str_contains(text, "suchbegriff")
```

### Beispiele

#### Einfache Suche
```dsl
WHEN str_contains(document.content, "Rechnung")
  DO addTag(tag: "Rechnung")
END
```

**Was macht das?**  
Wenn das Wort "Rechnung" irgendwo im Dokument vorkommt, wird der Tag hinzugefügt.

---

#### Im Titel suchen
```dsl
WHEN str_contains(document.title, "Amazon")
  DO setCorrespondent(correspondent: "Amazon")
END
```

**Was macht das?**  
Wenn "Amazon" im Titel steht, wird der Korrespondent gesetzt.

---

#### Im Dateinamen suchen
```dsl
WHEN str_contains(document.original_file_name, "scan")
  DO addTag(tag: "Gescannt")
END
```

**Was macht das?**  
Wenn der Dateiname "scan" enthält, wird ein Tag hinzugefügt.

---

## Text beginnt mit - `str_starts_with()`

Prüft, ob ein Text mit einem bestimmten Wort beginnt.

### Syntax
```dsl
str_starts_with(text, "anfang")
```

### Beispiele

#### Titel beginnt mit...
```dsl
WHEN str_starts_with(document.title, "Rechnung")
  DO setDocumentType(document_type: "Rechnung")
END
```

**Was macht das?**  
Wenn der Titel mit "Rechnung" beginnt, wird der Dokumenttyp gesetzt.

---

## Groß-/Kleinschreibung

### Problem
Standardmäßig wird zwischen Groß- und Kleinschreibung unterschieden:
- "Rechnung" ≠ "rechnung"
- "RECHNUNG" ≠ "Rechnung"

### Lösung: Alles in Kleinbuchstaben umwandeln

```dsl
WHEN str_contains(lower(document.content), "rechnung")
  DO addTag(tag: "Rechnung")
END
```

**Was macht das?**  
- `lower()` wandelt den Text in Kleinbuchstaben um
- Findet jetzt: "Rechnung", "rechnung", "RECHNUNG", etc.

---

## Praktische Beispiele

### Mehrere Begriffe suchen

```dsl
WHEN str_contains(document.content, "Rechnung") OR str_contains(document.content, "Invoice")
  DO addTag(tag: "Rechnung")
END
```

**Was macht das?**  
Sucht nach "Rechnung" ODER "Invoice" (für deutsche und englische Rechnungen).

---

### Kombination mit anderen Bedingungen

```dsl
WHEN str_contains(document.content, "Rechnung") AND document.correspondent = "Telekom"
  DO setTitle("Rechnung - Telekom")
  DO addTag(tag: "Rechnung")
  DO setDocumentType(document_type: "Rechnung")
END
```

**Was macht das?**  
Nur wenn BEIDE Bedingungen erfüllt sind (enthält "Rechnung" UND Korrespondent ist "Telekom"), werden die Aktionen ausgeführt.

---

### Negation - NICHT enthalten

```dsl
WHEN NOT str_contains(document.content, "Entwurf")
  DO addTag(tag: "Final")
END
```

**Was macht das?**  
Wenn das Wort "Entwurf" NICHT im Dokument vorkommt, wird der Tag "Final" hinzugefügt.

---

## Zusammenfassung

| Funktion | Was macht sie? | Beispiel |
|----------|----------------|----------|
| `str_contains()` | Prüft ob Text enthalten ist | `str_contains(document.content, "Rechnung")` |
| `str_starts_with()` | Prüft ob Text damit beginnt | `str_starts_with(document.title, "RE:")` |
| `lower()` | Wandelt in Kleinbuchstaben um | `lower(document.title)` |
| `upper()` | Wandelt in Großbuchstaben um | `upper(document.title)` |

---

## Nächste Schritte

- Schau dir **Vergleiche** an, um Werte zu vergleichen
- Lerne **Mehrere Bedingungen kombinieren**, um komplexere Regeln zu erstellen
- Probiere die **Beispiele** aus, um zu sehen, wie es in der Praxis funktioniert

