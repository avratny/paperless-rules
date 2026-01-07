# Rechnungen automatisch sortieren

Diese Regel erkennt Rechnungen automatisch und organisiert sie.

## Was macht diese Regel?

Wenn ein Dokument das Wort "Rechnung" enthält:
1. Fügt den Tag "Rechnung" hinzu
2. Setzt den Dokumenttyp auf "Rechnung"
3. Passt den Titel an

## Die Regel

```dsl
WHEN str_contains(document.content, "Rechnung")
  DO addTag(tag: "Rechnung")
  DO setDocumentType(document_type: "Rechnung")
  DO setTitle("Rechnung - " + document.correspondent)
END
```

## Schritt für Schritt erklärt

### Zeile 1: Die Bedingung
```dsl
WHEN str_contains(document.content, "Rechnung")
```

**Was passiert hier?**
- `str_contains()` sucht nach Text
- `document.content` ist der Dokumentinhalt
- `"Rechnung"` ist der Suchbegriff

**In einfachen Worten:**  
"Wenn das Wort 'Rechnung' im Dokument vorkommt..."

---

### Zeile 2: Tag hinzufügen
```dsl
  DO addTag(tag: "Rechnung")
```

**Was passiert hier?**
- Fügt den Tag "Rechnung" zum Dokument hinzu
- Der Tag muss in Paperless bereits existieren

---

### Zeile 3: Dokumenttyp setzen
```dsl
  DO setDocumentType(document_type: "Rechnung")
```

**Was passiert hier?**
- Setzt den Dokumenttyp auf "Rechnung"
- Der Dokumenttyp muss in Paperless bereits existieren

---

### Zeile 4: Titel anpassen
```dsl
  DO setTitle("Rechnung - " + document.correspondent)
```

**Was passiert hier?**
- Setzt einen neuen Titel
- `~` verbindet Texte (String-Konkatenation)
- `document.correspondent` ist der Absender

**Beispiel-Ergebnis:**  
Wenn der Korrespondent "Telekom" ist, wird der Titel: "Rechnung - Telekom"

---

## Erweiterte Version

### Auch englische Rechnungen erkennen

```dsl
WHEN str_contains(document.content, "Rechnung") OR str_contains(document.content, "Invoice")
  DO addTag(tag: "Rechnung")
  DO setDocumentType(document_type: "Rechnung")
  DO setTitle("Rechnung - " + document.correspondent)
END
```

**Was ist neu?**
- Sucht nach "Rechnung" ODER "Invoice"
- Funktioniert für deutsche und englische Rechnungen

---

### Groß-/Kleinschreibung ignorieren

```dsl
WHEN str_contains(lower(document.content), "rechnung")
  DO addTag(tag: "Rechnung")
  DO setDocumentType(document_type: "Rechnung")
  DO setTitle("Rechnung - " + document.correspondent)
END
```

**Was ist neu?**
- `lower()` wandelt alles in Kleinbuchstaben um
- Findet jetzt: "Rechnung", "rechnung", "RECHNUNG", etc.

---

### Nur von bestimmten Absendern

```dsl
WHEN str_contains(document.content, "Rechnung") AND document.correspondent = "Telekom"
  DO addTag(tag: "Telekom-Rechnung")
  DO setDocumentType(document_type: "Rechnung")
  DO setTitle("Rechnung - Telekom - " + document.created_date)
END
```

**Was ist neu?**
- Prüft zusätzlich, ob der Korrespondent "Telekom" ist
- Fügt einen spezifischen Tag hinzu
- Fügt das Datum in den Titel ein

---

## Tipps

### Tipp 1: Tags und Dokumenttypen müssen existieren
Stelle sicher, dass die Tags und Dokumenttypen in Paperless bereits angelegt sind!

### Tipp 2: Teste die Regel
Nutze die Funktion "Auf Dokument ausführen", um die Regel zu testen, bevor du sie aktivierst.

### Tipp 3: Fang einfach an
Beginne mit einer einfachen Version und erweitere sie nach und nach.

---

## Weitere Ideen

Du kannst diese Regel erweitern:
- Füge weitere Suchbegriffe hinzu (z.B. "Invoice", "Faktura")
- Setze zusätzliche Tags (z.B. "Buchhaltung")
- Extrahiere das Rechnungsdatum
- Setze eine Archiv-Seriennummer

---

## Verwandte Themen

- **Text prüfen** - Mehr über `str_contains()`
- **Vergleiche** - Mehr über Bedingungen
- **Aktionen** - Alle verfügbaren Aktionen

