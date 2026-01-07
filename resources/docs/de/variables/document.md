# Dokument-Informationen

Das `document` Objekt enthält alle Informationen über das aktuelle Dokument, das gerade verarbeitet wird.

## Was kann ich auslesen?

### 📄 Grundlegende Informationen

#### `document.title`
Der Titel des Dokuments.

**Beispiel:**
```dsl
WHEN str_contains(document.title, "Rechnung")
  DO addTag(tag: "Rechnung")
END
```

**Was macht das?**
Prüft, ob der Titel das Wort "Rechnung" enthält.

---

#### `document.content`
Der komplette Text-Inhalt des Dokuments (OCR-Text).

**Beispiel:**
```dsl
WHEN str_contains(document.content, "Telekom")
  DO setCorrespondent(correspondent: "Telekom")
END
```

**Was macht das?**
Prüft, ob im Dokumenttext "Telekom" vorkommt und setzt dann den Korrespondenten.

---

#### `document.original_file_name`
Der ursprüngliche Dateiname beim Hochladen.

**Beispiel:**
```dsl
WHEN str_contains(document.original_file_name, "scan")
  DO addTag(tag: "Gescannt")
END
```

**Was macht das?**
Wenn der Dateiname "scan" enthält, wird der Tag "Gescannt" hinzugefügt.

---

### Metadaten

#### `document.correspondent`
Der Name des Korrespondenten (Absender/Empfänger).

**Beispiel:**
```dsl
WHEN document.correspondent = "Amazon"
  DO setDocumentType(document_type: "Online-Bestellung")
END
```

**Was macht das?**
Wenn der Korrespondent "Amazon" ist, wird der Dokumenttyp gesetzt.

---

#### `document.document_type`
Der Name des Dokumenttyps.

**Beispiel:**
```dsl
WHEN document.document_type = "Rechnung"
  DO addTag(tag: "Buchhaltung")
END
```

**Was macht das?**
Wenn der Dokumenttyp "Rechnung" ist, wird der Tag "Buchhaltung" hinzugefügt.

---

#### `document.tags`
Liste aller Tags des Dokuments.

**Beispiel:**
```dsl
WHEN str_contains(document.content, "wichtig")
  DO addTag(tag: "Wichtig")
END
```

---

### 📅 Datum-Informationen

#### `document.created_date`
Das Erstellungsdatum des Dokuments (aus dem Dokument extrahiert).

**Beispiel:**
```dsl
WHEN document.created_date != null
  DO setTitle(document.correspondent ~ " - " + document.created_date)
END
```

**Was macht das?**
Wenn ein Erstellungsdatum vorhanden ist, wird der Titel mit Korrespondent und Datum gesetzt.

---

#### `document.added`
Wann das Dokument zu Paperless hinzugefügt wurde.

---

#### `document.modified`
Wann das Dokument zuletzt geändert wurde.

---

### 🔢 Weitere Informationen

#### `document.id`
Die eindeutige ID des Dokuments in Paperless.

**Beispiel:**
```dsl
WHEN document.id > 1000
  DO addTag(tag: "Neues Dokument")
END
```

---

#### `document.archive_serial_number`
Die Archiv-Seriennummer (ASN) des Dokuments.

**Beispiel:**
```dsl
WHEN document.archive_serial_number != null
  DO setTitle("ASN-" + document.archive_serial_number ~ " - " + document.title)
END
```

**Was macht das?**
Wenn eine ASN vorhanden ist, wird sie dem Titel vorangestellt.

---

## Tipps

### Kombiniere mehrere Informationen

```dsl
WHEN str_contains(document.content, "Rechnung") AND document.correspondent = "Telekom"
  DO setTitle("Rechnung - Telekom - " + document.created_date)
  DO addTag(tag: "Rechnung")
  DO setDocumentType(document_type: "Rechnung")
END
```

### Prüfe auf leere Werte

```dsl
WHEN document.correspondent = null
  DO setCorrespondent(correspondent: "Unbekannt")
END
```

**Was macht das?**
Wenn kein Korrespondent gesetzt ist, wird "Unbekannt" eingetragen.

---

## Nächste Schritte

Jetzt weißt du, welche Informationen du aus einem Dokument auslesen kannst!

Schau dir als Nächstes an:
- **Bedingungen** - Wie du diese Informationen prüfen kannst
- **Aktionen** - Was du mit diesen Informationen machen kannst
- **Beispiele** - Fertige Regeln zum Kopieren
