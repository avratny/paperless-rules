# Was sind Regeln?

Regeln sind **automatische Anweisungen**, die Paperless Rules ausführt, wenn ein Dokument hochgeladen oder geändert wird.

## Wie funktioniert das?

Stell dir eine Regel wie eine **Wenn-Dann-Anweisung** vor:

> **WENN** das Dokument den Text "Rechnung" enthält,
> **DANN** füge den Tag "Rechnung" hinzu.

Das ist alles! So einfach ist eine Regel.

## Aufbau einer Regel

Jede Regel besteht aus drei Teilen:

### 1. WHEN - Die Bedingung
Was muss erfüllt sein, damit die Regel ausgeführt wird?

```dsl
WHEN str_contains(document.content, "Rechnung")
```

**In einfachen Worten:**
"Wenn der Dokumentinhalt das Wort 'Rechnung' enthält..."

### 2. DO - Die Aktion
Was soll passieren, wenn die Bedingung erfüllt ist?

```dsl
  DO addTag(tag: "Rechnung")
```

**In einfachen Worten:**
"...dann füge den Tag 'Rechnung' hinzu."

### 3. END - Das Ende
Markiert das Ende der Regel.

```dsl
END
```

## Vollständiges Beispiel

```dsl
WHEN str_contains(document.content, "Rechnung")
  DO addTag(tag: "Rechnung")
END
```

**Das macht diese Regel:**
1. Prüft, ob das Wort "Rechnung" im Dokument vorkommt
2. Wenn ja: Fügt den Tag "Rechnung" hinzu
3. Wenn nein: Macht nichts

## Mehrere Aktionen

Du kannst auch mehrere Aktionen ausführen:

```dsl
WHEN str_contains(document.content, "Rechnung")
  DO addTag(tag: "Rechnung")
  DO setDocumentType(document_type: "Rechnung")
  DO setTitle("Rechnung - " ~ document.correspondent)
END
```

**Das macht diese Regel:**
1. Prüft, ob "Rechnung" im Dokument steht
2. Wenn ja:
   - Fügt Tag "Rechnung" hinzu
   - Setzt Dokumenttyp auf "Rechnung"
   - Ändert den Titel zu "Rechnung - [Absender]"

## Wann werden Regeln ausgeführt?

Regeln werden automatisch ausgeführt:

- **Bei neuen Dokumenten** - Wenn du ein Dokument hochlädst
- **Bei Änderungen** - Wenn du ein Dokument bearbeitest

Du kannst für jede Regel festlegen, wann sie ausgeführt werden soll:
- Nur bei neuen Dokumenten
- Nur bei Änderungen
- Bei beidem

## Tipp

Fang mit einfachen Regeln an! Du kannst sie später jederzeit erweitern.

**Nächster Schritt:** Schau dir die **Variablen** an, um zu sehen, was du alles aus einem Dokument auslesen kannst.

