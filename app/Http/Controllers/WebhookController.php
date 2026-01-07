<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessDocumentRules;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{

    /**
     * Handle incoming webhook from Paperless NGX
     */
    public function handle(Request $request): JsonResponse
    {
        // Determine action from query parameter
        $action = $request->query('action', '');

        // Validate action
        if (!in_array($action, ['create', 'change'])) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid action. Use ?action=create or ?action=change'
            ], 400);
        }

        // Convert action to internal event type
        $eventType = $action === 'create' ? 'on_create' : 'on_change';

        try {
            // Read webhook data from Paperless NGX
            $data = $request->json()->all();

            // Validate input data
            if (empty($data) || !isset($data['document_url'])) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid webhook data - missing document_url'
                ], 400);
            }

            // Extract document ID from URL
            // Format: "None/documents/80/" or "http://domain/documents/80/"
            $documentUrl = $data['document_url'];
            if (!preg_match('/\/documents\/(\d+)/', $documentUrl, $matches)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Could not extract document_id from URL'
                ], 400);
            }

            $documentId = (int) $matches[1];

            // Log webhook received
            Log::info("Webhook received for document {$documentId}, action: {$action}");

            // Dispatch job to queue for processing
            ProcessDocumentRules::dispatch($documentId, $eventType);

            Log::info("Job dispatched for document {$documentId}, event type: {$eventType}");

            return response()->json([
                'success' => true,
                'message' => "Document {$documentId} queued for processing",
                'document_id' => $documentId,
                'event_type' => $eventType,
                'queued' => true
            ]);

        } catch (\Exception $e) {
            Log::error("Webhook processing error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

