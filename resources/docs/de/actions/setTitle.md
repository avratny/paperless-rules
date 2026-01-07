# Titel setzen - `setTitle()`

Ändert den Titel des Dokuments.

## Syntax

```dsl
  DO setTitle(title: "Neuer Titel")
```

## Was macht diese Aktion?

Setzt einen neuen Titel für das Dokument. Der alte Titel wird überschrieben.

## Beispiele

### Einfacher fester Titel

```dsl
WHEN str_contains(document.content, "Rechnung")
  DO setTitle(title: "Rechnung")
END
```

**Was macht das?**
Setzt den Titel auf "Rechnung", wenn das Wort im Dokument vorkommt.

---

### Titel mit Korrespondent

```dsl
WHEN document.correspondent != null
  DO setTitle(title: "Dokument - " ~ document.correspondent)
END
```

**Was macht das?**
Setzt den Titel auf "Dokument - [Absender]", z.B. "Dokument - Telekom".

**Wie funktioniert das?**
- `~` verbindet Texte (String-Konkatenation)
- `document.correspondent` ist der Absender

---

### Titel mit Datum

```dsl
WHEN document.created_date != null
  DO setTitle(title: document.correspondent ~ " - " ~ document.created_date)
END
```

**Was macht das?**
Setzt den Titel auf "[Absender] - [Datum]", z.B. "Telekom - 2024-01-15".

---

### Titel mit mehreren Informationen

```dsl
WHEN str_contains(document.content, "Rechnung")
  DO setTitle(title: "Rechnung - " ~ document.correspondent ~ " - " ~ document.created_date)
END
```

**Was macht das?**
Setzt den Titel auf "Rechnung - [Absender] - [Datum]", z.B. "Rechnung - Telekom - 2024-01-15".

---

### Alten Titel behalten und erweitern

```dsl
WHEN str_contains(document.content, "wichtig")
  DO setTitle(title: "[WICHTIG] " ~ document.title)
END
```

**Was macht das?**
Fügt "[WICHTIG]" vor den bestehenden Titel, z.B. "[WICHTIG] Rechnung Telekom".

---

## Wichtig zu wissen

### Der Titel wird überschrieben
Wenn du `setTitle()` verwendest, wird der alte Titel komplett ersetzt!

**Vorher:** "Scan_2024_01_15.pdf"
**Nachher:** "Rechnung - Telekom"

### Texte verbinden mit `~`
Du kannst mehrere Texte mit `~` verbinden (String-Konkatenation):

```dsl
"Text1" ~ " " ~ "Text2"  →  "Text1 Text2"
```

### Variablen verwenden
Du kannst alle Dokument-Informationen verwenden:
- `document.title` - Aktueller Titel
- `document.correspondent` - Absender
- `document.document_type` - Dokumenttyp
- `document.created_date` - Erstellungsdatum
- etc.

---

## Zusammenfassung

| Was | Wie | Beispiel |
|-----|-----|----------|
| Fester Titel | `setTitle(title: "Text")` | `setTitle(title: "Rechnung")` |
| Mit Variable | `setTitle(title: "Text" ~ variable)` | `setTitle(title: "Rechnung - " ~ document.correspondent)` |
| Alten Titel behalten | `setTitle(title: "Text" ~ document.title)` | `setTitle(title: "[NEU] " ~ document.title)` |

---

## Siehe auch

- **Variablen** - Welche Informationen du verwenden kannst
- **Beispiele** - Fertige Regeln mit `setTitle()`
- **Andere Aktionen** - Was du noch alles machen kannst
