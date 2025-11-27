<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProduksiSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Matikan FK
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('produksi_batches')->truncate();
        DB::table('produksi')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ================================
        //   MASTER PRODUK (49 PRODUK)
        // ================================
        $produkList = [

            // ================== TABLET NON SALUT ==================
            ['Coric 100', 'Tablet Non Salut', 'TABLET_NON_SALUT', 30],
            ['Folic 400', 'Tablet Non Salut', 'TABLET_NON_SALUT', 30],
            ['Masflu Forte', 'Tablet Non Salut', 'TABLET_NON_SALUT', 30],
            ['Masneuro', 'Tablet Non Salut', 'TABLET_NON_SALUT', 30],
            ['Phenzacol', 'Tablet Non Salut', 'TABLET_NON_SALUT', 30],
            ['Samcalvit tab', 'Tablet Non Salut', 'TABLET_NON_SALUT', 30],
            ['Samcodin', 'Tablet Non Salut', 'TABLET_NON_SALUT', 30],
            ['SAMCONAL', 'Tablet Non Salut', 'TABLET_NON_SALUT', 30],
            ['Samconal Kaplet', 'Tablet Non Salut', 'TABLET_NON_SALUT', 30],
            ['Samcovask 10 mg', 'Tablet Non Salut', 'TABLET_NON_SALUT', 30],
            ['Samcovask 5 mg', 'Tablet Non Salut', 'TABLET_NON_SALUT', 30],
            ['SAMMOXIN FK', 'Tablet Non Salut', 'TABLET_NON_SALUT', 30],
            ['SAMDATICID TABLET', 'Tablet Non Salut', 'TABLET_NON_SALUT', 30],
            ['Toxaprim tab', 'Tablet Non Salut', 'TABLET_NON_SALUT', 30],
            ['Samcohistin', 'Tablet Non Salut', 'TABLET_NON_SALUT', 30],
            ['Amlodipine 5 mg', 'Tablet Non Salut', 'TABLET_NON_SALUT', 30],
            ['AMLODIPINE 10MG', 'Tablet Non Salut', 'TABLET_NON_SALUT', 30],
            ['Coric 300', 'Tablet Non Salut', 'TABLET_NON_SALUT', 30],
            ['Samcoxol Tab', 'Tablet Non Salut', 'TABLET_NON_SALUT', 30],
            ['Mastatin 10', 'Tablet Non Salut', 'TABLET_NON_SALUT', 30],
            ['Mastatin 20', 'Tablet Non Salut', 'TABLET_NON_SALUT', 30],
            ['Samcodexon', 'Tablet Non Salut', 'TABLET_NON_SALUT', 30],
            ['Amoxicillin Tryhidrate', 'Tablet Non Salut', 'TABLET_NON_SALUT', 30],

            // ================== FILM COATING ==================
            ['COSTAN FK', 'Tablet Film Coating', 'TABLET_SALUT', 30],
            ['Diaramid', 'Tablet Film Coating', 'TABLET_SALUT', 30],
            ['DOMESTRIUM TABLET', 'Tablet Film Coating', 'TABLET_SALUT', 30],
            ['Samcofenac 50', 'Tablet Film Coating', 'TABLET_SALUT', 30],
            ['Samquinor', 'Tablet Film Coating', 'TABLET_SALUT', 30],
            ['Diclofenac Sodium', 'Tablet Film Coating', 'TABLET_SALUT', 30],
            ['Corizine 10 mg', 'Tablet Film Coating', 'TABLET_SALUT', 30],
            ['Ciprofloxacin HCL', 'Tablet Film Coating', 'TABLET_SALUT', 30],
            ['Samtaflam 50', 'Tablet Film Coating', 'TABLET_SALUT', 30],

            // ================== TABLET SALUT GULA ==================
            ["Bundavin 30's", 'Tablet Salut Gula', 'TABLET_SALUT', 30],
            ["BUNDAVIN DUS", 'Tablet Salut Gula', 'TABLET_SALUT', 30],
            ['Betamin 100s', 'Tablet Salut Gula', 'TABLET_SALUT', 30],
            ['Betamin 2000s', 'Tablet Salut Gula', 'TABLET_SALUT', 30],
            ['Evitan 60s', 'Tablet Salut Gula', 'TABLET_SALUT', 30],
            ['Samcorbex Strip', 'Tablet Salut Gula', 'TABLET_SALUT', 30],
            ['VIT BC+B12 BOTOL', 'Tablet Salut Gula', 'TABLET_SALUT', 30],
            ['Vit BC+B12 1500s', 'Tablet Salut Gula', 'TABLET_SALUT', 30],
            ['Vit BC+B12 Strip', 'Tablet Salut Gula', 'TABLET_SALUT', 30],

            // ================== KAPSUL ==================
            ['Samcefad 500', 'Kapsul', 'KAPSUL', 30],
            ['Samcobion', 'Kapsul', 'KAPSUL', 30],
            ['Samrox -20', 'Kapsul', 'KAPSUL', 30],

            // ================== DRY SYRUP ==================
            ['SAMMOXIN DS', 'Dry Syrup', 'DRY_SYRUP', 25],

            // ================== CLO ==================
            ['CLO 100', 'CLO', 'CLO', 210],
            ['CLO 50', 'CLO', 'CLO', 210],

            // ================== CAIRAN LUAR ==================
            ['OBAT GIGI BKT', 'Obat Luar', 'CAIRAN_LUAR', 30],
            ['Obat Gosok Bunga Merah', 'Obat Luar', 'CAIRAN_LUAR', 30],
        ];

        // ============================
        // INSERT ROWS
        // ============================
        $rows = [];
        $i = 1;

        foreach ($produkList as $p) {
            $rows[] = [
                'kode_produk'     => sprintf('PRD-%03d', $i++),
                'nama_produk'     => $p[0],
                'bentuk_sediaan'  => $p[1],
                'tipe_alur'       => $p[2],
                'leadtime_target' => $p[3],
                'is_aktif'        => 1,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        DB::table('produksi')->insert($rows);
    }
}
