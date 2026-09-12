<?php

namespace App\Services;

use App\Models\TbPoBahan;
use App\Models\TbMasterBahan;
use App\Models\TbRincianKontrak;
use App\Models\rincian_menu_harian;
use App\Models\BoxBahanBaku;
use App\Models\Buffer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Service untuk AI-powered suggestions dalam PO Management
 * 
 * Menyediakan analisis data historis dan rekomendasi cerdas untuk:
 * - Optimisasi jumlah pemesanan
 * - Rekomendasi supplier
 * - Analisis harga
 * - Prediksi delivery time
 */
class PoAiService
{
    /**
     * Suggest optimal quantity untuk bahan dalam PO
     * 
     * Logic:
     * 1. Cari historical usage dari bahan ini untuk menu yang sama
     * 2. Hitung average, min, max usage
     * 3. Apply buffer percentage
     * 4. Round up ke nearest box (jika ada data box)
     * 5. Match dengan supplier constraints
     * 
     * @param int $bahanId ID bahan master
     * @param int|null $rincianMenuId ID rincian menu harian (untuk menu-specific analysis)
     * @param float $currentQty Quantity yang user input
     * @param int|null $kontrakId ID kontrak untuk constraint supplier
     * @return array suggestion dengan detail analysis
     */
    public static function suggestQuantity($bahanId, $rincianMenuId = null, $currentQty = null, $kontrakId = null)
    {
        try {
            // Get basic bahan info
            $bahan = TbMasterBahan::find($bahanId);
            if (!$bahan) {
                return self::errorResponse('Bahan tidak ditemukan');
            }

            // 1. Analisis historical usage
            $historicalAnalysis = self::analyzeHistoricalUsage($bahanId, $rincianMenuId);

            // 2. Get buffer configuration
            $buffer = Buffer::first();
            $bufferPercentage = $buffer->buffer_po ?? 15; // default 15%

            // 3. Calculate suggested quantity
            $suggestedQty = $historicalAnalysis['average_usage'];
            
            if ($suggestedQty <= 0 && $currentQty > 0) {
                // Jika tidak ada historical data, gunakan input user tapi apply buffer
                $suggestedQty = $currentQty;
            }

            // Apply buffer
            $bufferAmount = $suggestedQty * ($bufferPercentage / 100);
            $qtyWithBuffer = $suggestedQty + $bufferAmount;

            // 4. Calculate box information
            $boxInfo = self::calculateBoxRequirements($bahanId, $qtyWithBuffer);

            // 5. Check supplier constraints
            $supplierConstraints = null;
            if ($kontrakId) {
                $supplierConstraints = self::getSupplierConstraints($bahanId, $kontrakId);
                
                // Adjust quantity jika tidak memenuhi minimum order
                if ($supplierConstraints && $supplierConstraints['min_order'] && $qtyWithBuffer < $supplierConstraints['min_order']) {
                    $qtyWithBuffer = $supplierConstraints['min_order'];
                }
            }

            // 6. Build recommendation
            $recommendation = [
                'success' => true,
                'suggested_quantity' => round($qtyWithBuffer, 2),
                'user_input_quantity' => $currentQty,
                'buffer_percentage' => $bufferPercentage,
                'buffer_amount' => round($bufferAmount, 2),
                'historical_analysis' => $historicalAnalysis,
                'box_info' => $boxInfo,
                'supplier_constraints' => $supplierConstraints,
                'confidence_level' => self::calculateConfidenceLevel($historicalAnalysis),
                'reason' => self::generateRecommendationReason($historicalAnalysis, $bufferPercentage),
            ];

            return $recommendation;

        } catch (\Exception $e) {
            \Log::error('PoAiService::suggestQuantity error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'bahan_id' => $bahanId
            ]);
            return self::errorResponse($e->getMessage());
        }
    }

    /**
     * Analisis historical usage dari bahan
     * 
     * @param int $bahanId
     * @param int|null $rincianMenuId untuk filter spesifik menu
     * @return array analysis dengan average, min, max, data_points
     */
    private static function analyzeHistoricalUsage($bahanId, $rincianMenuId = null)
    {
        $query = TbPoBahan::where('id_bahan', $bahanId)
            ->where('deleted_at', null) // soft delete aware jika ada
            ->whereNotNull('jumlah_bahan')
            ->where('jumlah_bahan', '>', 0);

        // Filter by menu jika spesifik
        if ($rincianMenuId) {
            $query->where('id_rincian_bahan', $rincianMenuId);
        }

        // Ambil last 12 months data
        $query->where('tanggal_digunakan', '>=', Carbon::now()->subMonths(12));

        $historicalData = $query->get();

        if ($historicalData->isEmpty()) {
            return [
                'data_points' => 0,
                'average_usage' => 0,
                'min_usage' => 0,
                'max_usage' => 0,
                'last_usage' => null,
                'usage_trend' => 'no_data',
                'usage_variance' => 0,
            ];
        }

        $quantities = $historicalData->pluck('jumlah_bahan')->toArray();
        $average = array_sum($quantities) / count($quantities);
        $min = min($quantities);
        $max = max($quantities);
        $variance = count($quantities) > 1 ? $this->calculateVariance($quantities, $average) : 0;

        // Determine trend - compare recent vs older
        $recent = $historicalData->where('tanggal_digunakan', '>=', Carbon::now()->subMonths(3))->pluck('jumlah_bahan')->avg();
        $trend = 'stable';
        if ($recent > $average * 1.1) {
            $trend = 'increasing';
        } elseif ($recent < $average * 0.9) {
            $trend = 'decreasing';
        }

        return [
            'data_points' => count($quantities),
            'average_usage' => $average,
            'min_usage' => $min,
            'max_usage' => $max,
            'last_usage' => $historicalData->sortByDesc('tanggal_digunakan')->first()->jumlah_bahan ?? 0,
            'last_po_date' => $historicalData->sortByDesc('tanggal_digunakan')->first()->tanggal_digunakan ?? null,
            'usage_trend' => $trend,
            'usage_variance' => round($variance, 2),
        ];
    }

    /**
     * Hitung box requirements
     * 
     * @param int $bahanId
     * @param float $quantity
     * @return array|null
     */
    private static function calculateBoxRequirements($bahanId, $quantity)
    {
        $boxData = BoxBahanBaku::where('id_bahan', $bahanId)->first();

        if (!$boxData) {
            return null;
        }

        $boxesNeeded = ceil($quantity / $boxData->isi_per_box);

        return [
            'isi_per_box' => $boxData->isi_per_box,
            'boxes_needed' => $boxesNeeded,
            'total_with_boxes' => $boxesNeeded * $boxData->isi_per_box,
            'excess_quantity' => ($boxesNeeded * $boxData->isi_per_box) - $quantity,
        ];
    }

    /**
     * Get supplier constraints untuk bahan
     * 
     * @param int $bahanId
     * @param int $kontrakId
     * @return array|null
     */
    private static function getSupplierConstraints($bahanId, $kontrakId)
    {
        $rincian = TbRincianKontrak::where('id_kontrak', $kontrakId)
            ->where('id_bahan', $bahanId)
            ->where('status', 1)
            ->first();

        if (!$rincian) {
            return null;
        }

        return [
            'harga_bahan' => $rincian->harga_bahan,
            'merek_bahan' => $rincian->merek_bahan,
            'satuan_bahan' => $rincian->satuan_bahan,
            'min_order' => $rincian->min_order ?? null,
            'max_order' => $rincian->max_order ?? null,
        ];
    }

    /**
     * Calculate confidence level untuk recommendation
     * Based on data points dan variance
     * 
     * @param array $analysis
     * @return string high|medium|low
     */
    private static function calculateConfidenceLevel($analysis)
    {
        $dataPoints = $analysis['data_points'] ?? 0;
        $variance = $analysis['usage_variance'] ?? 0;

        // High confidence: banyak data points + variance kecil
        if ($dataPoints >= 10 && $variance < 2) {
            return 'high';
        }

        // Medium confidence: cukup data points
        if ($dataPoints >= 5) {
            return 'medium';
        }

        // Low confidence: data minimal
        return 'low';
    }

    /**
     * Generate human-readable reason untuk recommendation
     * 
     * @param array $analysis
     * @param float $bufferPercentage
     * @return string
     */
    private static function generateRecommendationReason($analysis, $bufferPercentage)
    {
        if ($analysis['data_points'] == 0) {
            return "Tidak ada data historis. Gunakan estimasi Anda dengan buffer {$bufferPercentage}%.";
        }

        $trend = $analysis['usage_trend'] ?? 'stable';
        $variance = $analysis['usage_variance'] ?? 0;

        $reason = "Berdasarkan {$analysis['data_points']} data penggunaan terakhir: ";
        $reason .= "rata-rata {$analysis['average_usage']} ({$analysis['min_usage']} - {$analysis['max_usage']}) ";

        if ($trend === 'increasing') {
            $reason .= "dengan tren naik. ";
        } elseif ($trend === 'decreasing') {
            $reason .= "dengan tren turun. ";
        } else {
            $reason .= "stabil. ";
        }

        $reason .= "Sudah ditambah buffer {$bufferPercentage}% untuk contingency.";

        return $reason;
    }

    /**
     * Calculate variance untuk data set
     * 
     * @param array $data
     * @param float $average
     * @return float
     */
    private static function calculateVariance($data, $average)
    {
        $squaredDifferences = array_map(function ($value) use ($average) {
            return pow($value - $average, 2);
        }, $data);

        return array_sum($squaredDifferences) / count($squaredDifferences);
    }

    /**
     * Helper untuk error response
     * 
     * @param string $message
     * @return array
     */
    private static function errorResponse($message)
    {
        return [
            'success' => false,
            'error' => $message,
        ];
    }

    /**
     * Recommend best supplier untuk bahan
     * (untuk future use - priority medium)
     * 
     * @param int $bahanId
     * @return array
     */
    public static function recommendSupplier($bahanId)
    {
        // TODO: Implementasi di tahap 2
        return [
            'success' => false,
            'message' => 'Feature coming soon in phase 2',
        ];
    }

    /**
     * Analyze pricing untuk bahan
     * (untuk future use - priority medium)
     * 
     * @param int $bahanId
     * @param float $currentPrice
     * @return array
     */
    public static function analyzePricing($bahanId, $currentPrice = null)
    {
        // TODO: Implementasi di tahap 2
        return [
            'success' => false,
            'message' => 'Feature coming soon in phase 2',
        ];
    }
}
