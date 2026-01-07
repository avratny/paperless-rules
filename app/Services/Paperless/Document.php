<?php

namespace App\Services\Paperless;

class Document
{
    private PaperlessService $service;
    private bool $isDirty = false;
    private int $modificationCount = 0; // Track number of modifications

    // Document Properties von der API
    public int $id;
    public ?int $correspondent;
    public ?int $document_type;
    public ?int $storage_path;
    public string $title;
    public string $content;
    public array $tags;
    public ?string $created;
    public string $created_date;
    public string $modified;
    public string $added;
    public ?string $archive_serial_number;
    public string $original_file_name;
    public ?string $archived_file_name;
    public ?int $owner;
    public ?array $notes;
    public array $custom_fields;

    // Weitere Properties
    public ?string $download_url;
    public ?string $thumbnail_url;
    public ?string $checksum;

    public function __construct(PaperlessService $service, array $data)
    {
        $this->service = $service;

        // Properties aus API-Daten befüllen
        $this->id = $data['id'];
        $this->correspondent = $data['correspondent'] ?? null;
        $this->document_type = $data['document_type'] ?? null;
        $this->storage_path = $data['storage_path'] ?? null;
        $this->title = $data['title'] ?? '';
        $this->content = $data['content'] ?? '';
        $this->tags = $data['tags'] ?? [];
        $this->created = $data['created'] ?? null;
        $this->created_date = $data['created_date'] ?? '';
        $this->modified = $data['modified'] ?? '';
        $this->added = $data['added'] ?? '';
        $this->archive_serial_number = $data['archive_serial_number'] ?? null;
        $this->original_file_name = $data['original_file_name'] ?? '';
        $this->archived_file_name = $data['archived_file_name'] ?? null;
        $this->owner = $data['owner'] ?? null;
        $this->notes = $data['notes'] ?? null;
        $this->custom_fields = $data['custom_fields'] ?? [];
        $this->download_url = $data['download_url'] ?? null;
        $this->thumbnail_url = $data['thumbnail_url'] ?? null;
        $this->checksum = $data['checksum'] ?? null;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'correspondent' => $this->correspondent,
            'document_type' => $this->document_type,
            'storage_path' => $this->storage_path,
            'title' => $this->title,
            'content' => $this->content,
            'tags' => $this->tags,
            'created' => $this->created,
            'created_date' => $this->created_date,
            'modified' => $this->modified,
            'added' => $this->added,
            'archive_serial_number' => $this->archive_serial_number,
            'original_file_name' => $this->original_file_name,
            'archived_file_name' => $this->archived_file_name,
            'owner' => $this->owner,
            'notes' => $this->notes,
            'custom_fields' => $this->custom_fields,
        ];
    }

    public function isModified(): bool
    {
        return $this->isDirty;
    }

    /**
     * Markiert das Dokument als geändert
     */
    public function markAsDirty(): void
    {
        $this->isDirty = true;
        $this->modificationCount++;
    }

    /**
     * Get current modification count (for tracking changes per rule)
     */
    public function getModificationCount(): int
    {
        return $this->modificationCount;
    }

    /**
     * Setzt den Titel des Dokuments
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
        $this->markAsDirty();
    }

    /**
     * Gibt den PaperlessService zurück
     */
    public function getService(): PaperlessService
    {
        return $this->service;
    }

    /**
     * Speichert das Dokument nach Paperless
     *
     * @param bool $force Erzwingt das Speichern auch wenn keine Änderungen vorliegen
     */
    public function save(bool $force = false): void
    {
        $this->service->saveDocument($this, $force);
    }

    public function getTags(): array
    {
        // Gibt einen Array aller Tag-Namen im Dokument zurück
        $tagNames = [];

        foreach ($this->tags as $tagId) {
            $tag = $this->service->findTagById($tagId);
            if ($tag) {
                $tagNames[] = $tag['name'];
            }
        }

        return $tagNames;
    }

    public function removeTagByName(string $name): void
    {
        // Entfernt einen Tag anhand des Namens
        $tag = $this->service->findTagByName($name);
        if (!$tag) {
            return;
        }

        $this->tags = array_values(array_filter($this->tags, fn($id) => $id !== $tag['id']));
        $this->markAsDirty();
    }

    public function addTagByName(string $name, bool $createIfNotExists = false): void
    {
        // Fügt einen Tag anhand des Namens hinzu
        $tag = $this->service->findTagByName($name);

        if (!$tag && $createIfNotExists) {
            $tag = $this->service->createTag($name);
        }

        if (!$tag) {
            return;
        }

        if (!in_array($tag['id'], $this->tags)) {
            $this->tags[] = $tag['id'];
            $this->markAsDirty();
        }
    }

    public function getDocumentType(): ?string
    {
        // Gibt den Namen des Document Types zurück
        if (!$this->document_type) {
            return null;
        }

        $documentType = $this->service->findDocumentTypeById($this->document_type);
        return $documentType ? $documentType['name'] : null;
    }

    public function setDocumentType(string $name, bool $createIfNotExists = false): void
    {
        // Setzt den Document Type des Dokuments anhand des Namens
        $documentType = $this->service->findDocumentTypeByName($name);

        if (!$documentType && $createIfNotExists) {
            $documentType = $this->service->createDocumentType($name);
        }

        if (!$documentType) {
            return;
        }

        $this->document_type = $documentType['id'];
        $this->markAsDirty();
    }

    public function getCorrespondent(): ?string
    {
        // Gibt den Namen des Correspondents zurück
        if (!$this->correspondent) {
            return null;
        }

        $correspondent = $this->service->findCorrespondentById($this->correspondent);
        return $correspondent ? $correspondent['name'] : null;
    }

    public function setCorrespondent(string $name, bool $createIfNotExists = false): void
    {
        // Setzt den Correspondent des Dokuments anhand des Namens
        $correspondent = $this->service->findCorrespondentByName($name);

        if (!$correspondent && $createIfNotExists) {
            $correspondent = $this->service->createCorrespondent($name);
        }

        if (!$correspondent) {
            return;
        }

        $this->correspondent = $correspondent['id'];
        $this->markAsDirty();
    }

    public function getCustomField(string $name): mixed
    {
        // Gibt den Wert des Custom Fields zurück
        $customField = $this->service->findCustomFieldByName($name);

        if (!$customField) {
            return null;
        }

        foreach ($this->custom_fields as $field) {
            if ($field['field'] === $customField['id']) {
                return $this->parseCustomFieldValue($customField, $field['value']);
            }
        }

        return null;
    }

    public function setCustomField(string $name, mixed $value): void
    {
        // Setzt den Wert eines Custom Fields
        $customField = $this->service->findCustomFieldByName($name);

        if (!$customField) {
            return;
        }

        // Wert entsprechend dem Feldtyp formatieren
        $formattedValue = $this->formatCustomFieldValue($customField, $value);

        $found = false;

        foreach ($this->custom_fields as &$field) {
            if ($field['field'] === $customField['id']) {
                $field['value'] = $formattedValue;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $this->custom_fields[] = [
                'field' => $customField['id'],
                'value' => $formattedValue
            ];
        }

        $this->markAsDirty();
    }

    // Helper-Methoden

    /**
     * Formatiert einen Wert für die API basierend auf dem Custom Field Typ
     */
    private function formatCustomFieldValue(array $customField, mixed $value): mixed
    {
        $fieldType = $customField['data_type'] ?? 'string';

        switch ($fieldType) {
            case 'string':
            case 'url':
                // String und URL: Als String zurückgeben
                return (string) $value;

            case 'boolean':
                // Boolean: Als boolean zurückgeben
                if (is_bool($value)) {
                    return $value;
                }
                // String-Konvertierung: "true", "1", "yes" -> true
                if (is_string($value)) {
                    return in_array(strtolower($value), ['true', '1', 'yes', 'on']);
                }
                return (bool) $value;

            case 'integer':
                // Integer: Als Integer zurückgeben
                return (int) $value;

            case 'float':
                // Float: Als Float zurückgeben
                return (float) $value;

            case 'date':
                // Date: Format YYYY-MM-DD
                if ($value instanceof \DateTime) {
                    return $value->format('Y-m-d');
                }
                // Wenn bereits ein String, direkt zurückgeben (sollte schon im richtigen Format sein)
                return (string) $value;

            case 'monetary':
                // Monetary: Format z.B. EUR1234.56
                // Kein Tausendertrenner, . als Dezimaltrenner, Währung ohne Leerzeichen davor
                if (is_numeric($value)) {
                    // Wenn nur eine Zahl übergeben wird, ohne Währung
                    return (string) $value;
                }
                // Entferne Tausendertrenner und stelle sicher, dass . als Dezimaltrenner verwendet wird
                $cleaned = str_replace([',', ' '], ['', ''], (string) $value);
                return $cleaned;

            case 'select':
                // Bei Select-Feldern: Finde die Option-ID anhand des Labels
                $options = $customField['extra_data']['select_options'] ?? [];

                // Wenn bereits eine ID übergeben wurde
                if (is_int($value)) {
                    return $value;
                }

                // Suche nach Label
                foreach ($options as $option) {
                    if ($option['label'] === $value) {
                        return $option['id'];
                    }
                }

                // Fallback: Versuche als ID zu interpretieren
                return is_numeric($value) ? (int) $value : $value;

            case 'documentlink':
                // Document Link: Array von Document IDs oder einzelne ID
                if (is_array($value)) {
                    return array_map('intval', $value);
                }
                return [(int) $value];

            default:
                // Fallback: Als String zurückgeben
                return (string) $value;
        }
    }

    /**
     * Parst einen Wert von der API basierend auf dem Custom Field Typ
     */
    private function parseCustomFieldValue(array $customField, mixed $value): mixed
    {
        $fieldType = $customField['data_type'] ?? 'string';

        switch ($fieldType) {
            case 'boolean':
                return (bool) $value;

            case 'integer':
                return (int) $value;

            case 'float':
                return (float) $value;

            case 'select':
                // Bei Select: Gib das Label zurück statt der ID
                $options = $customField['extra_data']['select_options'] ?? [];
                foreach ($options as $option) {
                    if ($option['id'] === $value) {
                        return $option['label'];
                    }
                }
                return $value;

            case 'documentlink':
                // Document Link: Array von IDs
                return is_array($value) ? $value : [$value];

            default:
                // String, URL, Date, Monetary: Als String zurückgeben
                return $value;
        }
    }
}
