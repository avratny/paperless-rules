<?php

namespace App\Services\Ollama;

use App\Services\SettingsService;
use ArdaGnsrn\Ollama\Ollama;

class OllamaService
{
    private string $nullPrompt = 'If you find nothing or you are not sure, return only the NULL. Do not add any other text.';
    private string $url;
    private string $model;

    public function __construct(?SettingsService $settingsService = null)
    {
        // Use SettingsService if provided, otherwise create new instance
        $settingsService = $settingsService ?? app(SettingsService::class);

        $this->url = $settingsService->getOllamaUrl();
        $this->model = $settingsService->getOllamaModel();
    }


    public function askOllamaAi(string $prompt): string
    {
        $prompt .= ' ' . $this->nullPrompt;

        try {
            // Create Ollama client with configured URL
            $client = Ollama::client($this->url);

            // Send completion request to Ollama
            $response = $client->completions()->create([
                'model' => $this->model,
                'prompt' => $prompt,
            ]);

            // Get the response text
            $answer = trim($response->response);

            // If the answer is "NULL" or empty, return empty string
            if (strtoupper($answer) === 'NULL' || empty($answer)) {
                return '';
            }

            return $answer;
        } catch (\Exception $e) {
            // Log error and return empty string
            \Log::error('Ollama API error: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Extract document number from document content using AI
     * Looks for invoice numbers, purchase order numbers, contract numbers, reference numbers, etc.
     *
     * @param string $documentContent The document content to analyze
     * @return string The extracted document number or empty string if not found
     */
    public function askOllamaAiForDocumentNumber(string $documentContent): string
    {
        $prompt = 'Extract the main document number from this document. '
            . 'It could be an invoice number, purchase order number, contract number, reference number, '
            . 'order number, ticket number, or any other identifying number. '
            . 'Return ONLY the number/identifier itself, without any prefix text like "Invoice No:" or "Number:". '
            . 'If there are multiple numbers, return the most important one (usually the invoice/order/contract number). '
            . 'Document content: ' . $documentContent;

        $result = $this->askOllamaAi($prompt);

        // Clean up the result - remove common prefixes
        $result = preg_replace('/^(Invoice|Order|Contract|Reference|Ticket|Document|No\.?|Nr\.?|Number|#)\s*:?\s*/i', '', $result);

        // Trim whitespace
        $result = trim($result);

        return $result;
    }
    public function askOllamaAiForCreationDate(string $documentContent): string
    {
        $prompt = 'When was this document created? Give me the answer in the format "YYYY-MM-DD". Document content: ' . $documentContent;
        $potential_date = $this->askOllamaAi($prompt);

        // If empty response, return empty string
        if (empty($potential_date)) {
            return '';
        }

        // Try to extract date in YYYY-MM-DD format using regex
        if (preg_match('/(\d{4})-(\d{2})-(\d{2})/', $potential_date, $matches)) {
            $year = (int)$matches[1];
            $month = (int)$matches[2];
            $day = (int)$matches[3];

            // Validate the date
            if (checkdate($month, $day, $year)) {
                return sprintf('%04d-%02d-%02d', $year, $month, $day);
            }
        }

        // Try other common date formats and convert them
        // Format: DD.MM.YYYY or DD/MM/YYYY
        if (preg_match('/(\d{1,2})[.\\/](\d{1,2})[.\\/](\d{4})/', $potential_date, $matches)) {
            $day = (int)$matches[1];
            $month = (int)$matches[2];
            $year = (int)$matches[3];

            if (checkdate($month, $day, $year)) {
                return sprintf('%04d-%02d-%02d', $year, $month, $day);
            }
        }

        // Format: MM/DD/YYYY (American format)
        if (preg_match('/(\d{1,2})\/(\d{1,2})\/(\d{4})/', $potential_date, $matches)) {
            $month = (int)$matches[1];
            $day = (int)$matches[2];
            $year = (int)$matches[3];

            if (checkdate($month, $day, $year)) {
                return sprintf('%04d-%02d-%02d', $year, $month, $day);
            }
        }

        // Try to parse with strtotime as last resort
        $timestamp = strtotime($potential_date);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }

        // No valid date found, return empty string
        return '';
    }

    /**
     * Get all available models from Ollama
     *
     * @return array Array of model names
     */
    public function getAvailableModels(): array
    {
        try {
            // Create Ollama client with configured URL
            $client = Ollama::client($this->url);

            // Get list of models
            $response = $client->models()->list();

            // Extract model names from response
            $models = [];
            foreach ($response->models as $model) {
                $models[] = $model->name;
            }

            return $models;
        } catch (\Exception $e) {
            // Log error and return empty array
            \Log::error('Ollama API error while fetching models: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Test connection to Ollama server
     *
     * @return bool True if connection is successful
     */
    public function testConnection(): bool
    {
        try {
            // Create Ollama client with configured URL
            $client = Ollama::client($this->url);

            // Try to list models as a simple connection test
            $client->models()->list();

            return true;
        } catch (\Exception $e) {
            // Log error and return false
            \Log::error('Ollama connection test failed: ' . $e->getMessage());
            return false;
        }
    }


}
