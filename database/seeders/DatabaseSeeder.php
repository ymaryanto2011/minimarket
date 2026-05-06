<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Product;
use App\Models\StoreProfile;
use App\Models\User;
use App\Models\Unit;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Store profile
        StoreProfile::updateOrCreate(['id' => 1], [
            'name'    => 'Minimarket Indo Jaya',
            'address' => 'Jl. Pahlawan No. 12, Kota ABC',
            'phone'   => '021-12345678',
            'email'   => 'indo.jaya@email.com',
        ]);

        // Admin user
        User::updateOrCreate(['email' => 'admin@minimarket.local'], [
            'name'      => 'Admin',
            'password'  => Hash::make(''),
            'role'      => 'admin',
            'is_active' => true,
        ]);

        // Supervisor user
        User::updateOrCreate(['email' => 'supervisor@minimarket.local'], [
            'name'      => 'Supervisor',
            'password'  => Hash::make(''),
            'role'      => 'supervisor',
            'is_active' => true,
        ]);

        // Cashier user
        User::updateOrCreate(['email' => 'kasir@minimarket.local'], [
            'name'      => 'Kasir 1',
            'password'  => Hash::make(''),
            'role'      => 'cashier',
            'is_active' => true,
        ]);

        // Units / Satuan
        $unitNames = ['pcs', 'kg', 'gram', 'liter', 'ml', 'botol', 'sachet', 'karton', 'lusin', 'pak', 'sak', 'strip', 'box', 'dus', 'roll'];
        foreach ($unitNames as $name) {
            Unit::firstOrCreate(['name' => $name]);
        }

        // Categories
        $categories = ['Minuman', 'Makanan', 'Snack', 'Sembako', 'Rokok', 'Kebersihan', 'Kesehatan'];
        $catIds = [];
        foreach ($categories as $name) {
            $cat = Category::firstOrCreate(['name' => $name]);
            $catIds[$name] = $cat->id;
        }

        // Sample products (barcode: valid EAN-13 with GS1 Indonesia prefix 899)
        $products = [
            ['code' => 'P001', 'barcode' => '8992001000016', 'name' => 'Indomie Goreng',       'category' => 'Makanan',    'retail' => 3500,  'wholesale' => 3200,  'min_wq' => 12, 'stock' => 100, 'min_stock' => 24, 'unit' => 'pcs'],
            ['code' => 'P002', 'barcode' => '8992001000023', 'name' => 'Teh Botol Sosro 350ml', 'category' => 'Minuman',    'retail' => 5000,  'wholesale' => 4500,  'min_wq' => 24, 'stock' => 80,  'min_stock' => 12, 'unit' => 'botol'],
            ['code' => 'P003', 'barcode' => '8992001000030', 'name' => 'Aqua 600ml',           'category' => 'Minuman',    'retail' => 4000,  'wholesale' => 3600,  'min_wq' => 24, 'stock' => 120, 'min_stock' => 24, 'unit' => 'botol'],
            ['code' => 'P004', 'barcode' => '8992001000047', 'name' => 'Gula Pasir 1kg',       'category' => 'Sembako',    'retail' => 15000, 'wholesale' => 14000, 'min_wq' => 5,  'stock' => 50,  'min_stock' => 10, 'unit' => 'kg'],
            ['code' => 'P005', 'barcode' => '8992001000054', 'name' => 'Minyak Goreng 1L',     'category' => 'Sembako',    'retail' => 18000, 'wholesale' => 16500, 'min_wq' => 5,  'stock' => 30,  'min_stock' => 10, 'unit' => 'liter'],
            ['code' => 'P006', 'barcode' => '8992001000061', 'name' => 'Beras 5kg',            'category' => 'Sembako',    'retail' => 65000, 'wholesale' => 62000, 'min_wq' => 3,  'stock' => 20,  'min_stock' => 5,  'unit' => 'sak'],
            ['code' => 'P007', 'barcode' => '8992001000078', 'name' => 'Chitato Sapi Panggang', 'category' => 'Snack',      'retail' => 12000, 'wholesale' => 11000, 'min_wq' => 10, 'stock' => 60,  'min_stock' => 12, 'unit' => 'pcs'],
            ['code' => 'P008', 'barcode' => '8992001000085', 'name' => 'Kopi Kapal Api',       'category' => 'Minuman',    'retail' => 2000,  'wholesale' => 1800,  'min_wq' => 20, 'stock' => 200, 'min_stock' => 30, 'unit' => 'sachet'],
            ['code' => 'P009', 'barcode' => '8992001000092', 'name' => 'Sabun Lifebuoy',       'category' => 'Kebersihan', 'retail' => 8000,  'wholesale' => 7200,  'min_wq' => 10, 'stock' => 40,  'min_stock' => 10, 'unit' => 'pcs'],
            ['code' => 'P010', 'barcode' => '8992001000108', 'name' => 'Panadol Tablet',       'category' => 'Kesehatan',  'retail' => 5000,  'wholesale' => 4500,  'min_wq' => 12, 'stock' => 4,   'min_stock' => 10, 'unit' => 'strip'],
        ];

        foreach ($products as $p) {
            Product::updateOrCreate(['code' => $p['code']], [
                'name'              => $p['name'],
                'barcode'           => $p['barcode'],
                'category_id'       => $catIds[$p['category']],
                'retail_price'      => $p['retail'],
                'wholesale_price'   => $p['wholesale'],
                'min_wholesale_qty' => $p['min_wq'],
                'stock'             => $p['stock'],
                'min_stock'         => $p['min_stock'],
                'unit'              => $p['unit'],
                'is_active'         => true,
            ]);
        }
    }
}
