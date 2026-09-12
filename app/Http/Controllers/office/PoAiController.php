<?php

namespace App\Http\Controllers\Office;

use App\Http\Controllers\Controller;
use App\Services\PoAiService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Controller untuk AI-powered suggestions dalam PO Management
 * 
 * Handles AJAX requests untuk:
 * - Quantity optimization
 * - Supplier recommendations
 * - Price analysis
 * - Delivery time prediction
 */
class PoAiController extends Controller
{
    /**
     * Get quantity suggestion untuk bahan tertentu
     * 
     * AJAX Endpoint: POST /api/po/ai/suggest-quantity
     * 
     * Request Parameters:
     * - bahan_id (required): ID bahan
     * - rincian_menu_id (optional): ID rincian menu untuk menu-specific analysis
     * - current_qty (optional): Quantity yang user input (untuk context)
     * - kontrak_id (optional): ID kontrak untuk supplier constraints
     * 
     * Response:
     * {
     *   "success": true,
     *   "suggested_quantity": 10.5,
     *   "historical_analysis": {...},
     *   "box_info": {...},
     *   "confidence_level": "high",
     *   "reason": "Berdasarkan 8 data penggunaan..."
     * }
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function suggestQuantity(Request $request): JsonResponse
    {
        // Validate input
        $validated = $request->validate([
            'bahan_id' => 'required|integer|exists:tb_master_bahan,id',
            'rincian_menu_id' => 'nullable|integer|exists:rincian_menu_harian,id',
            'current_qty' => 'nullable|numeric|min:0',
            'kontrak_id' => 'nullable|integer|exists:tb_kontrak,id',
        ]);

        try {
            // Call AI service
            $suggestion = PoAiService::suggestQuantity(
                $validated['bahan_id'],
                $validated['rincian_menu_id'] ?? null,
                $validated['current_qty'] ?? null,
                $validated['kontrak_id'] ?? null
            );

            // Check if error
            if (!$suggestion['success']) {
                return response()->json($suggestion, 422);
            }

            // Log suggestion untuk audit
            $this->logAiSuggestion('suggest_quantity', $validated, $suggestion);

            return response()->json($suggestion, 200);

        } catch (\Exception $e) {
            \Log::error('PoAiController::suggestQuantity error', [
                'message' => $e->getMessage(),
                'request' => $validated,
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Gagal mendapatkan saran quantity',
                'details' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Get supplier recommendation untuk bahan
     * (Feature untuk tahap 2)
     * 
     * AJAX Endpoint: POST /api/po/ai/recommend-supplier
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function recommendSupplier(Request $request): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Feature coming soon in phase 2',
        ], 501);
    }

    /**
     * Analyze pricing untuk bahan
     * (Feature untuk tahap 2)
     * 
     * AJAX Endpoint: POST /api/po/ai/analyze-pricing
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function analyzePricing(Request $request): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Feature coming soon in phase 2',
        ], 501);
    }

    /**
     * Predict delivery ETA
     * (Feature untuk tahap 2)
     * 
     * AJAX Endpoint: POST /api/po/ai/predict-eta
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function predictDeliveryEta(Request $request): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Feature coming soon in phase 2',
        ], 501);
    }

    /**
     * Log AI suggestion untuk audit trail dan training data
     * 
     * @param string $type Tipe suggestion (suggest_quantity, etc)
     * @param array $request Input dari user
     * @param array $suggestion Output dari AI service
     * @return void
     */
    private function logAiSuggestion($type, $request, $suggestion)
    {
        try {
            // TODO: Create PoAiLog model dan simpan ke database untuk audit trail
            // Untuk sekarang, just log ke file
            \Log::channel('ai_suggestions')->info($type, [
                'user_id' => auth()->id(),
                'input' => $request,
                'output' => $suggestion,
                'timestamp' => now(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to log AI suggestion', ['error' => $e->getMessage()]);
        }
    }
}
