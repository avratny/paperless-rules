# Deine erste Regel erstellen

Lass uns gemeinsam deine erste Regel erstellen!

## Ziel

Wir erstellen eine Regel, die automatisch den Tag "Rechnung" hinzufügt, wenn das Dokument das Wort "Rechnung" enthält.

## Schritt für Schritt

### Schritt 1: Neue Regel erstellen

1. Gehe zu **Regeln** im Hauptmenü
2. Klicke auf **Neue Regel erstellen**
3. Gib einen Namen ein, z.B. "Rechnungen taggen"

### Schritt 2: Die Regel schreiben

Kopiere diesen Code in das Regel-Feld:

```dsl
WHEN str_contains(document.content, "Rechnung")
  DO addTag(tag: "Rechnung")
END
```

### Schritt 3: Verstehen, was passiert

Lass uns die Regel Zeile für Zeile durchgehen:

#### Zeile 1: Die Bedingung
```dsl
WHEN str_contains(document.content, "Rechnung")
```

- `WHEN` = "Wenn..."
- `str_contains()` = "enthält der Text..."
- `document.content` = "...der Dokumentinhalt..."
- `"Rechnung"` = "...das Wort 'Rechnung'"

**In einfachen Worten:**  
"Wenn der Dokumentinhalt das Wort 'Rechnung' enthält..."

#### Zeile 2: Die Aktion
```dsl
  DO addTag(tag: "Rechnung")
```

- `DO` = "...dann mache..."
- `addTag()` = "...füge einen Tag hinzu..."
- `tag: "Rechnung"` = "...mit dem Namen 'Rechnung'"

**In einfachen Worten:**  
"...dann füge den Tag 'Rechnung' hinzu."

#### Zeile 3: Das Ende
```dsl
END
```

Markiert das Ende der Regel.

### Schritt 4: Regel speichern

1. Klicke auf **Speichern**
2. Die Regel wird automatisch auf Fehler geprüft
3. Wenn alles korrekt ist, wird die Regel aktiviert

### Schritt 5: Regel testen

Du kannst die Regel sofort testen:

1. Klicke auf **Auf Dokument ausführen**
2. Gib eine Dokument-ID ein
3. Klicke auf **Ausführen**
4. Schau dir das Ergebnis an

## Erweiterte Version

Möchtest du mehr machen? Hier eine erweiterte Version:

```dsl
WHEN str_contains(document.content, "Rechnung")
  DO addTag(tag: "Rechnung")
  DO setDocumentType(document_type: "Rechnung")
END
```

Diese Regel:
- Fügt den Tag "Rechnung" hinzu
- Setzt den Dokumenttyp auf "Rechnung"

## Was du gelernt hast

- Wie man eine Regel erstellt  
- Wie man eine Bedingung schreibt  
- Wie man eine Aktion ausführt  
- Wie man eine Regel testet

## Nächste Schritte

Jetzt kannst du:
- Die **Variablen** durchsehen, um zu sehen, was du alles auslesen kannst
- Die **Bedingungen** anschauen, um komplexere Prüfungen zu machen
- Die **Aktionen** erkunden, um mehr zu automatisieren
- Die **Beispiele** durchgehen, um Inspiration zu bekommen

Viel Spaß beim Automatisieren!

