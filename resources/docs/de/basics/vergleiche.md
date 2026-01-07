# Vergleiche

Mit Vergleichen kannst du prüfen, ob Werte gleich, größer oder kleiner sind.

## Gleich - `=`

Prüft, ob zwei Werte exakt gleich sind.

### Beispiele

#### Text vergleichen
```dsl
WHEN document.correspondent = "Amazon"
  DO setDocumentType(document_type: "Online-Bestellung")
END
```

**Was macht das?**  
Wenn der Korrespondent exakt "Amazon" ist, wird der Dokumenttyp gesetzt.

---

#### Dokumenttyp prüfen
```dsl
WHEN document.document_type = "Rechnung"
  DO addTag(tag: "Buchhaltung")
END
```

**Was macht das?**  
Wenn der Dokumenttyp "Rechnung" ist, wird ein Tag hinzugefügt.

---

## Ungleich - `!=`

Prüft, ob zwei Werte NICHT gleich sind.

### Beispiele

#### Nicht leer
```dsl
WHEN document.correspondent != null
  DO setTitle(document.correspondent ~ " - " + document.title)
END
```

**Was macht das?**  
Wenn ein Korrespondent gesetzt ist (nicht leer), wird der Titel angepasst.

---

#### Nicht ein bestimmter Wert
```dsl
WHEN document.correspondent != "Unbekannt"
  DO addTag(tag: "Bekannter Absender")
END
```

**Was macht das?**  
Wenn der Korrespondent NICHT "Unbekannt" ist, wird ein Tag hinzugefügt.

---

## 🔢 Größer/Kleiner

### Größer als - `>`
```dsl
WHEN document.id > 1000
  DO addTag(tag: "Neues Dokument")
END
```

**Was macht das?**  
Wenn die Dokument-ID größer als 1000 ist, wird ein Tag hinzugefügt.

---

### Kleiner als - `<`
```dsl
WHEN document.id < 100
  DO addTag(tag: "Altes Dokument")
END
```

---

### Größer oder gleich - `>=`
```dsl
WHEN document.id >= 1000
  DO addTag(tag: "Ab Dokument 1000")
END
```

---

### Kleiner oder gleich - `<=`
```dsl
WHEN document.id <= 100
  DO addTag(tag: "Bis Dokument 100")
END
```

---

## Null-Prüfungen

### Ist leer (null)
```dsl
WHEN document.correspondent = null
  DO setCorrespondent(correspondent: "Unbekannt")
END
```

**Was macht das?**  
Wenn kein Korrespondent gesetzt ist, wird "Unbekannt" eingetragen.

---

### Ist nicht leer (nicht null)
```dsl
WHEN document.archive_serial_number != null
  DO setTitle("ASN-" + document.archive_serial_number)
END
```

**Was macht das?**  
Wenn eine Archiv-Seriennummer vorhanden ist, wird sie in den Titel eingefügt.

---

## Praktische Beispiele

### Mehrere Vergleiche kombinieren
```dsl
WHEN document.correspondent = "Telekom" AND document.document_type = "Rechnung"
  DO setTitle("Rechnung - Telekom - " + document.created_date)
  DO addTag(tag: "Telekom-Rechnung")
END
```

**Was macht das?**  
Nur wenn BEIDE Bedingungen erfüllt sind, werden die Aktionen ausgeführt.

---

### Entweder-Oder
```dsl
WHEN document.correspondent = "Amazon" OR document.correspondent = "eBay"
  DO setDocumentType(document_type: "Online-Bestellung")
END
```

**Was macht das?**  
Wenn der Korrespondent "Amazon" ODER "eBay" ist, wird der Dokumenttyp gesetzt.

---

## Wichtig: Groß-/Kleinschreibung

Bei Text-Vergleichen wird zwischen Groß- und Kleinschreibung unterschieden:

```dsl
"Amazon" = "Amazon"   → Gleich
"Amazon" = "amazon"   → Nicht gleich
"Amazon" = "AMAZON"   → Nicht gleich
```

### Lösung: Alles in Kleinbuchstaben umwandeln

```dsl
WHEN lower(document.correspondent) = "amazon"
  DO setDocumentType(document_type: "Online-Bestellung")
END
```

Jetzt funktioniert es mit: "Amazon", "amazon", "AMAZON", etc.

---

## Zusammenfassung

| Operator | Bedeutung | Beispiel |
|----------|-----------|----------|
| `=` | Gleich | `document.correspondent = "Amazon"` |
| `!=` | Ungleich | `document.correspondent != null` |
| `>` | Größer als | `document.id > 1000` |
| `<` | Kleiner als | `document.id < 100` |
| `>=` | Größer oder gleich | `document.id >= 1000` |
| `<=` | Kleiner oder gleich | `document.id <= 100` |

---

## Nächste Schritte

- Lerne **Mehrere Bedingungen kombinieren** mit AND/OR
- Schau dir die **Aktionen** an, um zu sehen, was du alles machen kannst
- Probiere die **Beispiele** aus

