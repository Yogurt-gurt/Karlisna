<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            [
                'name' => 'Dashboard',
                'slug' => 'dashboard',
                'access' => 'manager,ketua,admin',
                'icon' => '<i class="icon-grid menu-icon"></i>',
            ],
            [
                'name' => 'Data Anggota',
                'slug' => 'data-anggota',
                'access' => 'manager,ketua,admin',
            ],
            [
                'name' => 'Data Pinjaman',
                'slug' => 'data-pinjaman',
                'access' => 'manager,ketua,admin',
            ],
            [
                'name' => 'Data Simpanan',
                'slug' => 'data-pinjaman',
                'access' => 'manager,ketua,admin',
            ],
            [
                'name' => 'Laporan Mutasi',
                'slug' => 'laporan-mutasi',
                'access' => 'manager,ketua,admin',
            ],
        ];
        foreach ($menus as $menu) {
            Menu::insert([
                'name' => $menu['name'],
                'slug' => $menu['slug'],
                'access' => $menu['access'],
                'created_at' => now(),
            ]);
        }
    }
}
