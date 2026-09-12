<?php

namespace App\Http\Controllers\office;

use App\Http\Controllers\Controller;
use App\Models\MenuBahan;
use App\Models\Resep;
use App\Models\ResepTahapMasak;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ResepTahapMasakController extends Controller
{
    public function index($id): View
    {
        $resep = Resep::findOrFail($id);
        $steps = ResepTahapMasak::where('id_resep', $id)
            ->orderBy('tahap')
            ->get();
        $bahanOptions = $this->getBahanOptionsByResep($id);
        $bahanMap = $bahanOptions->pluck('bahan', 'id')->toArray();

        return view('office.resep_tahap_masak.index', [
            'header' => 'Tahap Cara Memasak Resep',
            'resep' => $resep,
            'steps' => $steps,
            'bahanOptions' => $bahanOptions,
            'bahanMap' => $bahanMap,
            'editStep' => null,
            'editStepSelectedBahanIds' => [],
        ]);
    }

    public function store(Request $request, $id): RedirectResponse
    {
        $resep = Resep::findOrFail($id);

        $validated = $request->validate([
            'tahap' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('tb_resep_tahap_masak', 'tahap')->where(function ($query) use ($id) {
                    return $query->where('id_resep', $id);
                }),
            ],
            'keterangan' => 'required|string',
            'id_bahan' => 'nullable|array',
            'id_bahan.*' => [
                'integer',
                Rule::exists('tb_menu_bahan', 'bahan_id')->where(function ($query) use ($id) {
                    return $query->where('menu_id', $id);
                }),
            ],
        ]);

        $selectedBahanIds = $validated['id_bahan'] ?? [];
        $selectedBahanNames = $this->mapBahanIdsToNames($selectedBahanIds, $id);

        ResepTahapMasak::create([
            'id_resep' => $resep->id,
            'tahap' => $validated['tahap'],
            'keterangan' => $validated['keterangan'],
            'id_bahan' => $selectedBahanNames,
        ]);

        return redirect()
            ->route('resep.tahap-masak.index', $resep->id)
            ->with('success', 'Tahap memasak berhasil ditambahkan.');
    }

    public function edit($id, ResepTahapMasak $tahapMasak): View
    {
        $resep = Resep::findOrFail($id);

        if ((int) $tahapMasak->id_resep !== (int) $resep->id) {
            abort(404);
        }

        $steps = ResepTahapMasak::where('id_resep', $id)
            ->orderBy('tahap')
            ->get();
        $bahanOptions = $this->getBahanOptionsByResep($id);
        $bahanMap = $bahanOptions->pluck('bahan', 'id')->toArray();
        $editStepSelectedBahanIds = $this->normalizeSelectedBahanIds($tahapMasak->id_bahan ?? [], $id);

        return view('office.resep_tahap_masak.index', [
            'header' => 'Tahap Cara Memasak Resep',
            'resep' => $resep,
            'steps' => $steps,
            'bahanOptions' => $bahanOptions,
            'bahanMap' => $bahanMap,
            'editStep' => $tahapMasak,
            'editStepSelectedBahanIds' => $editStepSelectedBahanIds,
        ]);
    }

    public function update(Request $request, $id, ResepTahapMasak $tahapMasak): RedirectResponse
    {
        $resep = Resep::findOrFail($id);

        if ((int) $tahapMasak->id_resep !== (int) $resep->id) {
            abort(404);
        }

        $validated = $request->validate([
            'tahap' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('tb_resep_tahap_masak', 'tahap')
                    ->ignore($tahapMasak->id)
                    ->where(function ($query) use ($id) {
                        return $query->where('id_resep', $id);
                    }),
            ],
            'keterangan' => 'required|string',
            'id_bahan' => 'nullable|array',
            'id_bahan.*' => [
                'integer',
                Rule::exists('tb_menu_bahan', 'bahan_id')->where(function ($query) use ($id) {
                    return $query->where('menu_id', $id);
                }),
            ],
        ]);

        $selectedBahanIds = $validated['id_bahan'] ?? [];
        $selectedBahanNames = $this->mapBahanIdsToNames($selectedBahanIds, $id);

        $tahapMasak->update([
            'tahap' => $validated['tahap'],
            'keterangan' => $validated['keterangan'],
            'id_bahan' => $selectedBahanNames,
        ]);

        return redirect()
            ->route('resep.tahap-masak.index', $resep->id)
            ->with('success', 'Tahap memasak berhasil diperbarui.');
    }

    public function destroy($id, ResepTahapMasak $tahapMasak): RedirectResponse
    {
        $resep = Resep::findOrFail($id);

        if ((int) $tahapMasak->id_resep !== (int) $resep->id) {
            abort(404);
        }

        $tahapMasak->delete();

        return redirect()
            ->route('resep.tahap-masak.index', $resep->id)
            ->with('success', 'Tahap memasak berhasil dihapus.');
    }

    protected function getBahanOptionsByResep($id)
    {
        return MenuBahan::query()
            ->join('tb_master_bahan', 'tb_menu_bahan.bahan_id', '=', 'tb_master_bahan.id')
            ->where('tb_menu_bahan.menu_id', $id)
            ->select('tb_master_bahan.id', 'tb_master_bahan.bahan')
            ->distinct()
            ->orderBy('tb_master_bahan.bahan')
            ->get();
    }

    protected function mapBahanIdsToNames(array $selectedBahanIds, $id): array
    {
        if (empty($selectedBahanIds)) {
            return [];
        }

        $bahanMap = $this->getBahanOptionsByResep($id)
            ->pluck('bahan', 'id')
            ->toArray();

        return collect($selectedBahanIds)
            ->map(function ($bahanId) use ($bahanMap) {
                return $bahanMap[$bahanId] ?? null;
            })
            ->filter()
            ->values()
            ->all();
    }

    protected function normalizeSelectedBahanIds(array $storedBahanValues, $id): array
    {
        if (empty($storedBahanValues)) {
            return [];
        }

        $bahanOptions = $this->getBahanOptionsByResep($id);
        $idToName = $bahanOptions->pluck('bahan', 'id')->toArray();
        $nameToId = array_flip($idToName);

        return collect($storedBahanValues)
            ->map(function ($value) use ($idToName, $nameToId) {
                if (is_numeric($value)) {
                    $id = (int) $value;
                    return array_key_exists($id, $idToName) ? $id : null;
                }

                if (is_string($value)) {
                    return $nameToId[$value] ?? null;
                }

                return null;
            })
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
