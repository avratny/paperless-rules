<?php

namespace Database\Seeders;

use App\Models\Rule;
use Illuminate\Database\Seeder;

class RuleSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Vordefinierte valide DSL-Regeln
        $ruleTemplates = [
            // Unbedingte Regeln (ohne WHEN)
            [
                'name' => 'Alle Dokumente als importiert markieren',
                'rule' => <<<'DSL'
DO addTag(tag: "imported")
DSL,
            ],
            [
                'name' => 'Standard-Kategorisierung',
                'rule' => <<<'DSL'
DO addTag(tag: "unprocessed")
DO setCustomField(field: "status", value: "new")
DSL,
            ],
            [
                'name' => 'Initiale Dokumentverarbeitung',
                'rule' => <<<'DSL'
LET defaultType = "Dokument"
DO setDocumentType(type: defaultType)
DO addTag(tag: "needs-review")
DO setCustomField(field: "reviewed", value: "false")
DSL,
            ],
            // Bedingte Regeln (mit WHEN)
            [
                'name' => 'Rechnung automatisch taggen',
                'rule' => <<<'DSL'
WHEN str_contains(document.content, "Rechnung")
THEN
    DO addTag(tag: "invoice")
END
DSL,
            ],
            [
                'name' => 'Vertrag erkennen und kategorisieren',
                'rule' => <<<'DSL'
WHEN str_contains(document.content, "Vertrag") or str_contains(document.content, "Vereinbarung")
THEN
    DO addTag(tag: "contract")
    DO setDocumentType(type: "Vertrag")
END
DSL,
            ],
            [
                'name' => 'ACME Corp Korrespondent setzen',
                'rule' => <<<'DSL'
WHEN str_contains(document.content, "ACME Corp")
THEN
    LET company = "ACME Corp"
    DO setCorrespondent(name: company)
    DO addTag(tag: "acme")
END
DSL,
            ],
            [
                'name' => 'Dringende Dokumente markieren',
                'rule' => <<<'DSL'
WHEN str_contains(document.content, "dringend") or str_contains(document.content, "urgent")
THEN
    DO addTag(tag: "urgent")
    DO setCustomField(field: "priority", value: "high")
ELSE
    DO addTag(tag: "normal")
END
DSL,
            ],
            [
                'name' => 'Bezahlte Rechnungen verarbeiten',
                'rule' => <<<'DSL'
WHEN str_contains(document.content, "Rechnung") and str_contains(document.content, "bezahlt")
THEN
    DO addTag(tag: "invoice")
    DO addTag(tag: "paid")
    DO setCustomField(field: "status", value: "paid")
END
DSL,
            ],
            [
                'name' => 'Mahnung erkennen',
                'rule' => <<<'DSL'
WHEN str_contains(document.content, "Mahnung") or str_contains(document.content, "Zahlungserinnerung")
THEN
    DO addTag(tag: "reminder")
    DO setDocumentType(type: "Mahnung")
    DO setCustomField(field: "priority", value: "high")
END
DSL,
            ],
            [
                'name' => 'Angebot kategorisieren',
                'rule' => <<<'DSL'
WHEN str_contains(document.title, "Angebot") or str_contains(document.content, "Angebot")
THEN
    DO addTag(tag: "offer")
    DO setDocumentType(type: "Angebot")
END
DSL,
            ],
            [
                'name' => 'Lieferschein verarbeiten',
                'rule' => <<<'DSL'
WHEN str_contains(document.content, "Lieferschein")
THEN
    DO addTag(tag: "delivery-note")
    DO setDocumentType(type: "Lieferschein")
END
DSL,
            ],
            [
                'name' => 'Bestellung erkennen',
                'rule' => <<<'DSL'
WHEN str_contains(document.content, "Bestellung") or str_contains(document.content, "Order")
THEN
    DO addTag(tag: "order")
    DO setDocumentType(type: "Bestellung")
END
DSL,
            ],
            [
                'name' => 'Quittung taggen',
                'rule' => <<<'DSL'
WHEN str_contains(document.content, "Quittung") or str_contains(document.content, "Kassenbon")
THEN
    DO addTag(tag: "receipt")
    DO setDocumentType(type: "Quittung")
END
DSL,
            ],
        ];

        // Zusätzliche dynamisch generierte Regeln
        $companies = ['Microsoft', 'Google', 'Amazon', 'Apple', 'Tesla', 'BMW', 'Siemens', 'SAP', 'Bosch', 'Allianz'];
        $documentTypes = ['Brief', 'E-Mail', 'Protokoll', 'Bericht', 'Formular', 'Antrag', 'Bescheinigung', 'Zertifikat'];

        foreach ($companies as $company) {
            $ruleTemplates[] = [
                'name' => "{$company} Dokumente erkennen",
                'rule' => <<<DSL
WHEN str_contains(document.content, "{$company}")
THEN
    DO setCorrespondent(name: "{$company}")
    DO addTag(tag: "{$this->slugify($company)}")
END
DSL,
            ];
        }

        foreach ($documentTypes as $docType) {
            $tag = $this->slugify($docType);
            $ruleTemplates[] = [
                'name' => "{$docType} automatisch kategorisieren",
                'rule' => <<<DSL
WHEN str_contains(document.content, "{$docType}")
THEN
    DO addTag(tag: "{$tag}")
    DO setDocumentType(type: "{$docType}")
END
DSL,
            ];
        }

        // Komplexere Regeln
        $ruleTemplates[] = [
            'name' => 'Verschachtelte Rechnungsverarbeitung',
            'rule' => <<<'DSL'
WHEN str_contains(document.content, "Rechnung")
THEN
    DO addTag(tag: "invoice")
    WHEN str_contains(document.content, "bezahlt")
    THEN
        DO addTag(tag: "paid")
        DO setCustomField(field: "status", value: "paid")
    ELSE
        WHEN str_contains(document.content, "überfällig")
        THEN
            DO addTag(tag: "overdue")
            DO setCustomField(field: "status", value: "overdue")
        ELSE
            DO addTag(tag: "pending")
            DO setCustomField(field: "status", value: "pending")
        END
    END
END
DSL,
        ];

        $ruleTemplates[] = [
            'name' => 'Dokument nach Titel klassifizieren',
            'rule' => <<<'DSL'
WHEN str_starts_with(document.title, "RE:")
THEN
    DO addTag(tag: "reply")
ELSE
    WHEN str_starts_with(document.title, "FW:")
    THEN
        DO addTag(tag: "forwarded")
    END
END
DSL,
        ];

        $ruleTemplates[] = [
            'name' => 'Lange Dokumente markieren',
            'rule' => <<<'DSL'
WHEN len(document.content) > 10000
THEN
    DO addTag(tag: "long-document")
END
DSL,
        ];

        // Erstelle alle Rules
        $count = 0;
        foreach ($ruleTemplates as $index => $template) {
            // Zufällige Wahrscheinlichkeiten
            $enabled = rand(1, 100) > 20; // 80% aktiviert
            $onCreate = rand(1, 100) > 40; // 60% on_create
            $onChange = rand(1, 100) > 50; // 50% on_change

            // Stelle sicher, dass mindestens einer true ist
            if (!$onCreate && !$onChange) {
                $onCreate = (bool)rand(0, 1);
                $onChange = !$onCreate;
            }

            Rule::create([
                'name' => $template['name'],
                'rule' => $template['rule'],
                'enabled' => $enabled,
                'on_create' => $onCreate,
                'on_change' => $onChange,
                'order' => $index * 10, // Assign order based on index, with gaps for flexibility
            ]);
            $count++;
        }

        $this->command->info("{$count} Rules mit valider DSL-Syntax wurden erfolgreich erstellt!");
    }

    /**
     * Convert string to slug
     */
    private function slugify(string $text): string
    {
        $text = strtolower($text);
        $text = str_replace(['ä', 'ö', 'ü', 'ß'], ['ae', 'oe', 'ue', 'ss'], $text);
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        return trim($text, '-');
    }
}

