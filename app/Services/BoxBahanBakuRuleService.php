<?php

namespace App\Services;

class BoxBahanBakuRuleService
{
    public function calculate(string $namaBahan, int $jenisBahan, string $satuanBahan): array
    {
        $nama = strtolower(trim($namaBahan));
        $satuan = strtolower(trim($satuanBahan));

        $isLeafyVegetable = $this->containsAny($nama, [
            'bayam', 'sawi', 'kangkung', 'selada', 'daun', 'pakcoy', 'caisim'
        ]);

        $isHardVegetable = $this->containsAny($nama, [
            'wortel', 'kentang', 'labu', 'lobak', 'ubi'
        ]);

        $isSmallFruit = $this->containsAny($nama, [
            'kelengkeng', 'anggur', 'stroberry', 'strawberry'
        ]);

        $isLargeFruit = $this->containsAny($nama, [
            'jeruk', 'apel'
        ]);

        $isiPerBox = 100;

        if ($jenisBahan === 3) {
            if ($isLeafyVegetable) {
                $isiPerBox = 3000;
            } elseif ($isHardVegetable) {
                $isiPerBox = 5000;
            } else {
                $isiPerBox = 5000;
            }
        } elseif ($jenisBahan === 2) {
            if ($this->containsAny($satuan, ['papan'])) {
                $isiPerBox = 50;
            } elseif ($this->containsAny($satuan, ['pcs', 'pc', 'biji', 'butir'])) {
                $isiPerBox = 100;
            } else {
                $isiPerBox = 5000;
            }
        } elseif ($jenisBahan === 4) {
            if ($this->containsAny($satuan, ['pcs', 'pc', 'biji', 'butir'])) {
                if ($isSmallFruit) {
                    $isiPerBox = 500;
                } elseif ($isLargeFruit) {
                    $isiPerBox = 50;
                } else {
                    $isiPerBox = 100;
                }
            } else {
                $isiPerBox = 100;
            }
        } elseif ($jenisBahan === 5) {
            if ($this->containsAny($nama, ['susu'])) {
                $isiPerBox = 100;
            } else {
                $isiPerBox = 100;
            }
        } else {
            if ($this->containsAny($satuan, ['gram', 'gr', 'ml'])) {
                $isiPerBox = 5000;
            } else {
                $isiPerBox = 100;
            }
        }

        $penyusutan = 0;
        if ($jenisBahan === 3) {
            if ($isLeafyVegetable) {
                $penyusutan = 20;
            } elseif ($isHardVegetable) {
                $penyusutan = 5;
            }
        }

        $hasilMatang = $isiPerBox - ($isiPerBox * $penyusutan / 100);

        return [
            'isi_per_box' => (int) round($isiPerBox),
            'penyusutan' => (int) round($penyusutan),
            'hasil_matang' => (int) round($hasilMatang),
        ];
    }

    private function containsAny(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }
}
