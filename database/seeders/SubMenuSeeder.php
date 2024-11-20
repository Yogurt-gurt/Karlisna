<?php

namespace Database\Seeders;

use App\Models\SubMenu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sub_menus = [
            [
                'menu_id' => 1,
                'name' => 'Main Dashboard',
                'slug' => 'main-dashboard',
                'access' => 'manager,ketua,admin',
                'url' => [
                    'manager' => route('home-manager'),
                    'ketua' => route('home-ketua'),
                    'admin' => route('home-admin'),
                ],
            ],
            [
                'menu_id' => 2,
                'name' => 'Data Anggota',
                'slug' => 'data-anggota',
                'access' => 'manager,ketua,admin',
                'url' => [
                    'manager' => route('approve-manager'),
                    'ketua' => route('approve-ketua'),
                ],
            ],
            [
                'menu_id' => 3,
                'name' => 'Data Pinjaman',
                'slug' => 'data-pinjaman',
                'access' => 'manager,ketua,admin',
                'url' => [
                    'manager' => route('data.pinjaman.manager'),
                ]
            ]
        ];

        foreach ($sub_menus as $sub_menu) {
            SubMenu::insert([
                'menu_id' => $sub_menu['menu_id'],
                'name' => $sub_menu['name'],
                'slug' => $sub_menu['slug'],
                'access' => $sub_menu['access'],
                'url' => $sub_menu['url'],
            ]);
        }
    }
}
