<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\MenuGiziHarian;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AutoMenuGiziService
{
    // Field output yang wajib dikembalikan AI dan disimpan ke tb_menu_gizi_harian.
    protected array $nutritionFields = [
        'energi',
        'protein',
        'lemak',
        'karbohidrat',
        'serat',
        'natrium',
    ];

    public function calculateOrReuseAndStore(int $menuId): array
    {
        // Ambil menu aktif agar bisa dipakai ulang di method calculate.
        $menu = Menu::findOrFail($menuId);

        // Hitung dari AI berdasarkan komposisi menu yang aktif.
        $result = $this->calculate($menuId, $menu);

        // Simpan hasil AI ke tb_menu_gizi_harian untuk menu aktif.
        $gizi = MenuGiziHarian::updateOrCreate(
            ['id_menu' => $menuId],
            $result['totals']
        );

        // Kembalikan model, sumber data, dan reasoning AI agar bisa ditampilkan di UI.
        return [
            'model' => $gizi,
            'source' => 'ai',
            'missing_ingredients' => [],
            'ai_reason' => $result['ai_reason'] ?? null,
        ];
    }

    public function calculateAndStore(int $menuId): MenuGiziHarian
    {
        // Shortcut untuk menjaga kompatibilitas pemanggilan lama.
        return $this->calculateOrReuseAndStore($menuId)['model'];
    }

    public function calculate(int $menuId, ?Menu $menu = null): array
    {
        // Jika object menu belum dikirim, ambil dari database.
        $menu = $menu ?: Menu::findOrFail($menuId);

        // Bentuk konteks menu lengkap untuk dikirim ke AI.
        $context = $this->buildAiContext($menu);

        // Panggil AI agar mengestimasi total nutrisi menu berdasarkan komposisi dan ketentuan gizi Indonesia.
        $aiPayload = $this->requestAiEstimate($context);

        // Rapikan dan validasi hasil AI sebelum disimpan ke database.
        return [
            'totals' => $this->normalizeAiTotals($aiPayload),
            'missing_ingredients' => [],
            'ai_reason' => trim((string) ($aiPayload['reasoning'] ?? $aiPayload['reason'] ?? 'Estimasi dihitung dari komposisi menu dan porsi per golongan.')),
        ];
    }

    protected function buildAiContext(Menu $menu): array
    {
        $golonganContext = $this->resolveGolonganContext((string) ($menu->golongan ?? 'pax_a'));

        return [
            'menu_id' => (int) $menu->id,
            'menu_name' => (string) ($menu->menu ?? ''),
            'golongan' => $golonganContext['code'],
            'golongan_label' => $golonganContext['label'],
            'golongan_nutrition_reference' => $golonganContext['nutrition_reference'],
            'components' => array_values(array_filter([
                $this->buildComponentContext('karbohidrat', $menu->karbohidrat, 'tb_rumus_perhitungan_karbo', $menu->id, [
                    'karbo_porsi_a',
                    'karbo_porsi_b',
                ]),
                $this->buildComponentContext('protein', $menu->protein, 'tb_rumus_perhitungan_protein', $menu->id, [
                    'protein_porsi_a',
                    'protein_porsi_b',
                ]),
                $this->buildComponentContext('sayur', $menu->sayur, 'tb_rumus_perhitungan_sayur', $menu->id, [
                    'sayur_porsi_a',
                    'sayur_porsi_b',
                    'sayur_porsi_c',
                    'sayur_porsi_d',
                ]),
                $this->buildComponentContext('buah', $menu->buah, 'tb_rumus_perhitungan_buah', $menu->id, [
                    'buah_porsi_a',
                    'buah_porsi_b',
                ]),
                $this->buildComponentContext('suplemen', $menu->susu, 'tb_rumus_perhitungan_suplemen', $menu->id, [
                    'suplemen_porsi_a',
                    'suplemen_porsi_b',
                ]),
            ])),
        ];
    }

    protected function buildComponentContext(string $componentName, $recipeId, string $formulaTable, int $menuId, array $formulaFields): array
    {
        // Jika komponen belum dipilih resepnya, tetap kirim placeholder agar prompt AI lengkap.
        if (empty($recipeId)) {
            return null;
        }

        // Ambil nama resep komponen.
        $recipe = DB::table('tb_resep')
            ->where('id', $recipeId)
            ->select('id', 'nama_resep')
            ->first();

        // Ambil rumus per pax terbaru untuk komponen ini.
        $formula = DB::table($formulaTable)
            ->where('id_menu', $menuId)
            ->orderByDesc('id')
            ->first();

        // Ambil seluruh bahan resep komponen ini.
        $ingredients = DB::table('tb_menu_bahan')
            ->join('tb_master_bahan', 'tb_menu_bahan.bahan_id', '=', 'tb_master_bahan.id')
            ->leftJoin('tb_satuan', 'tb_master_bahan.satuan_bahan', '=', 'tb_satuan.id')
            ->where('tb_menu_bahan.menu_id', $recipeId)
            ->select(
                'tb_master_bahan.bahan',
                'tb_menu_bahan.jumlah',
                'tb_satuan.satuan as satuan',
                'tb_menu_bahan.status_bahan_baku'
            )
            ->orderByRaw("FIELD(tb_menu_bahan.status_bahan_baku, 1, 2, 4, 5, 3)")
            ->orderBy('tb_master_bahan.bahan')
            ->get()
            ->map(function ($row) {
                return [
                    'bahan' => (string) $row->bahan,
                    'qty' => round((float) $row->jumlah, 2),
                    'satuan' => (string) ($row->satuan ?? ''),
                    'status' => (int) ($row->status_bahan_baku ?? 0),
                ];
            })
            ->take(15)
            ->values()
            ->all();

        // Hanya kirim field rumus yang relevan dan terisi.
        $formulaPayload = [];
        foreach ($formulaFields as $field) {
            if ($formula && isset($formula->{$field})) {
                $formulaPayload[$field] = (float) $formula->{$field};
            }
        }

        return [
            'component' => $componentName,
            'recipe_name' => $recipe ? (string) $recipe->nama_resep : null,
            'formula' => $formulaPayload,
            'ingredients' => $ingredients,
        ];
    }

    protected function resolveGolonganContext(string $golongan): array
    {
        $normalized = strtolower(trim($golongan));

        if (in_array($normalized, ['pax_b', 'b'], true)) {
            return [
                'code' => 'pax_b',
                'label' => 'Anak SMA',
                'nutrition_reference' => 'Gunakan asumsi porsi dan kebutuhan gizi untuk anak SMA.',
            ];
        }

        return [
            'code' => 'pax_a',
            'label' => 'TK sampai SD kelas 3',
            'nutrition_reference' => 'Gunakan asumsi porsi dan kebutuhan gizi untuk anak TK sampai SD kelas 3.',
        ];
    }

    protected function requestAiEstimate(array $context): array
    {
        // Ambil konfigurasi koneksi AI dari config/services.php.
        $url = (string) config('services.nutrition_ai.url');
        $apiKey = (string) config('services.nutrition_ai.api_key');
        $model = (string) config('services.nutrition_ai.model');
        $timeout = (int) config('services.nutrition_ai.timeout', 20);
        $requiresApiKey = !$this->isLocalAiEndpoint($url);

        // Jika credential belum diisi, hentikan proses dengan pesan jelas sesuai field yang benar-benar kosong.
        $missing = [];
        if ($url === '') {
            $missing[] = 'NUTRITION_AI_URL';
        }
        if ($requiresApiKey && $apiKey === '') {
            $missing[] = 'NUTRITION_AI_API_KEY';
        }
        if ($model === '') {
            $missing[] = 'NUTRITION_AI_MODEL';
        }
        if ($missing !== []) {
            throw new \RuntimeException('Konfigurasi AI nutrisi belum lengkap. Isi ' . implode(', ', $missing) . ' di environment server.');
        }

        // Prompt sistem: AI wajib mengembalikan JSON saja dan mengacu pada praktik gizi Indonesia.
        $systemPrompt = 'Anda adalah ahli gizi menu MBG Indonesia. Estimasikan total gizi per 1 porsi menu. Ikuti mapping golongan: pax_a = TK sampai SD kelas 3, pax_b = anak SMA. Gunakan praktik gizi Indonesia dan Tabel Komposisi Pangan Indonesia. Balas JSON valid saja tanpa markdown. Jangan beri penjelasan, jangan beri teks lain di luar JSON.';

        // Prompt user: kirim konteks menu apa adanya agar AI mengestimasi total nutrisi.
        $userPrompt = "Hitung estimasi total nutrisi untuk 1 porsi menu berikut.\n"
            . "Fokus output pada total menu akhir.\n"
            . "Jika formula per pax tersedia, gunakan itu sebagai bobot utama.\n"
            . "Jika ada bahan/satuan yang ambigu, gunakan estimasi paling masuk akal menurut praktik gizi Indonesia.\n"
            . "Output wajib JSON exact dengan schema: {\"energi\":number,\"protein\":number,\"lemak\":number,\"karbohidrat\":number,\"serat\":number,\"natrium\":number}.\n"
            . "Jangan tambahkan key lain.\n\n"
            . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        // Panggil endpoint chat-completions kompatibel OpenAI/OpenRouter.
        $request = Http::timeout($timeout)->acceptJson();
        if ($requiresApiKey) {
            $request = $request->withToken($apiKey);
        }

        $response = $request->post($url, [
            'model' => $model,
            'temperature' => 0,
            'max_tokens' => 160,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt],
            ],
        ]);

        // Jika API gagal, tampilkan potongan body agar mudah debug di server.
        if ($response->failed()) {
            $errorMessage = (string) data_get($response->json(), 'error.message', '');
            $errorCode = (string) data_get($response->json(), 'error.code', '');

            if ($errorCode === 'insufficient_quota') {
                throw new \RuntimeException('AI nutrisi tidak bisa dipakai karena quota API habis. Periksa billing atau ganti API key yang masih aktif.');
            }

            if ($errorCode === 'model_not_found') {
                throw new \RuntimeException('Model AI nutrisi tidak tersedia untuk API key ini. Periksa nilai NUTRITION_AI_MODEL di environment server.');
            }

            if (str_contains(strtolower($errorMessage), 'timeout')) {
                throw new \RuntimeException('AI nutrisi lokal terlalu lama merespons. Gunakan tombol hitung berdasarkan database atau pakai model AI lokal yang lebih ringan.');
            }

            if ($errorMessage !== '') {
                throw new \RuntimeException('AI nutrisi gagal merespons: ' . $errorMessage);
            }

            throw new \RuntimeException('AI nutrisi gagal merespons: ' . substr($response->body(), 0, 300));
        }

        // Ambil isi message dari response standar OpenAI/OpenRouter.
        $content = (string) data_get($response->json(), 'choices.0.message.content', '');
        if ($content === '') {
            throw new \RuntimeException('AI nutrisi tidak mengembalikan konten yang dapat diproses.');
        }

        // Extract JSON agar aman meski provider menyisipkan karakter tambahan.
        $json = $this->extractJsonObject($content);
        $decoded = json_decode($json, true);

        if (!is_array($decoded)) {
            throw new \RuntimeException('Format jawaban AI nutrisi tidak valid.');
        }

        return $decoded;
    }

    protected function normalizeAiTotals(array $payload): array
    {
        // Pastikan semua field output ada, numerik, tidak negatif, dan dibulatkan 2 digit.
        $totals = [];
        foreach ($this->nutritionFields as $field) {
            $value = (float) ($payload[$field] ?? 0);
            $totals[$field] = round(max($value, 0), 2);
        }

        return $totals;
    }

    protected function extractJsonObject(string $content): string
    {
        // Jika AI membungkus dengan code fence, bersihkan terlebih dahulu.
        $trimmed = trim($content);
        $trimmed = preg_replace('/^```json\s*|^```\s*|\s*```$/m', '', $trimmed) ?? $trimmed;

        // Jika sudah JSON object utuh, kembalikan langsung.
        if (str_starts_with($trimmed, '{') && str_ends_with($trimmed, '}')) {
            return $trimmed;
        }

        // Fallback: ambil substring dari kurung kurawal pertama sampai terakhir.
        $start = strpos($trimmed, '{');
        $end = strrpos($trimmed, '}');
        if ($start === false || $end === false || $end <= $start) {
            throw new \RuntimeException('Jawaban AI tidak mengandung JSON object yang valid.');
        }

        return substr($trimmed, $start, ($end - $start) + 1);
    }

    protected function isLocalAiEndpoint(string $url): bool
    {
        if ($url === '') {
            return false;
        }

        $host = (string) parse_url($url, PHP_URL_HOST);

        return in_array($host, ['127.0.0.1', 'localhost'], true);
    }
}