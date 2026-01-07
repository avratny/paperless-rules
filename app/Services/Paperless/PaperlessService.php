<?php

namespace App\Services\Paperless;

use App\Services\SettingsService;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class PaperlessService
{
    private string $url;
    private string $api_key;
    private int $apiVersion = 9;

    // Cache für Metadaten
    private ?array $tagsCache = null;
    private ?array $documentTypesCache = null;
    private ?array $correspondentsCache = null;
    private ?array $customFieldsCache = null;
    private ?array $storagePathsCache = null;

    public function __construct(?SettingsService $settingsService = null)
    {
        // Use SettingsService if provided, otherwise create new instance
        $settingsService = $settingsService ?? app(SettingsService::class);

        $this->url = rtrim($settingsService->getPaperlessUrl(), '/');
        $this->api_key = $settingsService->getPaperlessApiKey();
    }

    /**
     * Lädt ein Dokument von der Paperless API
     */
    public function loadDocument(int $documentId): Document
    {
        $response = $this->makeRequest('GET', "/api/documents/{$documentId}/");

        if (!$response->successful()) {
            throw new \Exception("Failed to load document {$documentId}: " . $response->body());
        }

        return new Document($this, $response->json());
    }

    /**
     * Speichert Änderungen an einem Dokument
     *
     * @param Document $document Das zu speichernde Dokument
     * @param bool $force Erzwingt das Speichern auch wenn keine Änderungen vorliegen
     */
    public function saveDocument(Document $document, bool $force = false): void
    {
        if (!$force && !$document->isModified()) {
            return;
        }

        $documentId = $document->id;

        // Nur die editierbaren Felder senden (nicht content, modified, added, etc.)
        $data = [
            'title' => $document->title,
            'correspondent' => $document->correspondent,
            'document_type' => $document->document_type,
            'storage_path' => $document->storage_path,
            'tags' => $document->tags,
            'created_date' => $document->created_date,
            'archive_serial_number' => $document->archive_serial_number,
            'custom_fields' => $document->custom_fields,
        ];

        $response = $this->makeRequest('PATCH', "/api/documents/{$documentId}/", $data);

        if (!$response->successful()) {
            throw new \Exception("Failed to save document {$documentId}: " . $response->body());
        }
    }

    /**
     * Retrieves all Document Types as array
     */
    public function getAllDocumentTypes(): array
    {
        if ($this->documentTypesCache === null) {
            $this->documentTypesCache = $this->getAllPaginated('/api/document_types/');
        }
        return $this->documentTypesCache;
    }

    /**
     * Retrieves all Tags in Hierarchical order
     */
    public function getAllTags(): array
    {
        if ($this->tagsCache === null) {
            $this->tagsCache = $this->getAllPaginated('/api/tags/');
        }
        return $this->tagsCache;
    }

    /**
     * Retrieves all Correspondents
     */
    public function getAllCorrespondents(): array
    {
        if ($this->correspondentsCache === null) {
            $this->correspondentsCache = $this->getAllPaginated('/api/correspondents/');
        }
        return $this->correspondentsCache;
    }

    /**
     * Retrieves all Custom Fields
     */
    public function getAllCustomFields(): array
    {
        if ($this->customFieldsCache === null) {
            $this->customFieldsCache = $this->getAllPaginated('/api/custom_fields/');
        }
        return $this->customFieldsCache;
    }

    /**
     * Retrieves all Storage Paths
     */
    public function getAllStoragePaths(): array
    {
        if ($this->storagePathsCache === null) {
            $this->storagePathsCache = $this->getAllPaginated('/api/storage_paths/');
        }
        return $this->storagePathsCache;
    }

    /**
     * Erstellt einen neuen Tag
     */
    public function createTag(string $name): array
    {
        $response = $this->makeRequest('POST', '/api/tags/', [
            'name' => $name,
        ]);

        if (!$response->successful()) {
            throw new \Exception("Failed to create tag '{$name}': " . $response->body());
        }

        $tag = $response->json();

        // Cache aktualisieren
        if ($this->tagsCache !== null) {
            $this->tagsCache[] = $tag;
        }

        return $tag;
    }

    /**
     * Erstellt einen neuen Document Type
     */
    public function createDocumentType(string $name): array
    {
        $response = $this->makeRequest('POST', '/api/document_types/', [
            'name' => $name,
        ]);

        if (!$response->successful()) {
            throw new \Exception("Failed to create document type '{$name}': " . $response->body());
        }

        $documentType = $response->json();

        // Cache aktualisieren
        if ($this->documentTypesCache !== null) {
            $this->documentTypesCache[] = $documentType;
        }

        return $documentType;
    }

    /**
     * Erstellt einen neuen Correspondent
     */
    public function createCorrespondent(string $name): array
    {
        $response = $this->makeRequest('POST', '/api/correspondents/', [
            'name' => $name,
        ]);

        if (!$response->successful()) {
            throw new \Exception("Failed to create correspondent '{$name}': " . $response->body());
        }

        $correspondent = $response->json();

        // Cache aktualisieren
        if ($this->correspondentsCache !== null) {
            $this->correspondentsCache[] = $correspondent;
        }

        return $correspondent;
    }

    /**
     * Findet einen Tag anhand der ID
     */
    public function findTagById(int $id): ?array
    {
        foreach ($this->getAllTags() as $tag) {
            if ($tag['id'] === $id) {
                return $tag;
            }
        }
        return null;
    }

    /**
     * Findet einen Tag anhand des Namens
     */
    public function findTagByName(string $name): ?array
    {
        foreach ($this->getAllTags() as $tag) {
            if ($tag['name'] === $name) {
                return $tag;
            }
        }
        return null;
    }

    /**
     * Findet einen Document Type anhand der ID
     */
    public function findDocumentTypeById(int $id): ?array
    {
        foreach ($this->getAllDocumentTypes() as $type) {
            if ($type['id'] === $id) {
                return $type;
            }
        }
        return null;
    }

    /**
     * Findet einen Document Type anhand des Namens
     */
    public function findDocumentTypeByName(string $name): ?array
    {
        foreach ($this->getAllDocumentTypes() as $type) {
            if ($type['name'] === $name) {
                return $type;
            }
        }
        return null;
    }

    /**
     * Findet einen Correspondent anhand der ID
     */
    public function findCorrespondentById(int $id): ?array
    {
        foreach ($this->getAllCorrespondents() as $correspondent) {
            if ($correspondent['id'] === $id) {
                return $correspondent;
            }
        }
        return null;
    }

    /**
     * Findet einen Correspondent anhand des Namens
     */
    public function findCorrespondentByName(string $name): ?array
    {
        foreach ($this->getAllCorrespondents() as $correspondent) {
            if ($correspondent['name'] === $name) {
                return $correspondent;
            }
        }
        return null;
    }

    /**
     * Findet ein Custom Field anhand des Namens
     */
    public function findCustomFieldByName(string $name): ?array
    {
        foreach ($this->getAllCustomFields() as $field) {
            if ($field['name'] === $name) {
                return $field;
            }
        }
        return null;
    }

    /**
     * Hilfsmethode für HTTP-Requests
     */
    private function makeRequest(string $method, string $endpoint, array $data = []): Response
    {
        $url = $this->url . $endpoint;

        $request = Http::withHeaders([
            'Authorization' => 'Token ' . $this->api_key,
            'Accept' => 'application/json; version=' . $this->apiVersion,
        ]);

        return match(strtoupper($method)) {
            'GET' => $request->get($url, $data),
            'POST' => $request->post($url, $data),
            'PATCH' => $request->patch($url, $data),
            'PUT' => $request->put($url, $data),
            'DELETE' => $request->delete($url, $data),
            default => throw new \Exception("Unsupported HTTP method: {$method}"),
        };
    }

    /**
     * Hilfsmethode zum Abrufen aller paginierten Ergebnisse
     */
    private function getAllPaginated(string $endpoint, array $params = []): array
    {
        $allResults = [];
        $page = 1;
        $pageSize = 100;

        do {
            $response = $this->makeRequest('GET', $endpoint, array_merge($params, [
                'page' => $page,
                'page_size' => $pageSize,
            ]));

            if (!$response->successful()) {
                throw new \Exception("Failed to fetch {$endpoint}: " . $response->body());
            }

            $data = $response->json();
            $results = $data['results'] ?? [];
            $allResults = array_merge($allResults, $results);

            $hasNext = !empty($data['next']);
            $page++;

        } while ($hasNext);

        return $allResults;
    }

    /**
     * Fetches documents that have been created or modified since a given timestamp
     * Excludes locked documents
     *
     * @param string|null $since ISO 8601 timestamp (e.g., "2024-01-15T10:30:00Z")
     * @return array Array of document IDs with their event types
     */
    public function getDocumentsSince(?string $since = null): array
    {
        $params = [
            'ordering' => 'modified',
        ];

        // If a timestamp is provided, filter by modified date
        if ($since !== null) {
            // Paperless uses modified__gte for filtering
            $params['modified__gte'] = $since;
        }

        // Fetch all documents matching the criteria
        $documents = $this->getAllPaginated('/api/documents/', $params);

        $result = [];

        foreach ($documents as $doc) {
            $documentId = $doc['id'];

            // Skip locked documents
            if (\App\Models\LockedDocument::isLocked($documentId)) {
                \Log::info("Skipping locked document {$documentId} during polling");
                continue;
            }

            // Determine event type based on added vs modified date
            // If added and modified are very close (within 5 seconds), it's a new document
            $added = new \Carbon\Carbon($doc['added']);
            $modified = new \Carbon\Carbon($doc['modified']);
            $diffInSeconds = abs($added->diffInSeconds($modified));

            // If document was added recently (within the polling window), treat as on_create
            // Otherwise, treat as on_change
            $eventType = $diffInSeconds <= 5 ? 'on_create' : 'on_change';

            $result[] = [
                'id' => $documentId,
                'event_type' => $eventType,
                'added' => $doc['added'],
                'modified' => $doc['modified'],
            ];
        }

        return $result;
    }
}
