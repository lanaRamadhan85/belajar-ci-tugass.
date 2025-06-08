<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'category_name' => 'Laptop Gaming',
                'description'   => 'Kategori untuk laptop performa tinggi khusus gaming seperti ASUS TUF.',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'category_name' => 'Laptop Bisnis',
                'description'   => 'Kategori untuk laptop ringan dan stylish seperti Asus Vivobook.',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'category_name' => 'Laptop Mahasiswa',
                'description'   => 'Kategori untuk laptop terjangkau seperti Lenovo IdeaPad.',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('product_category')->insertBatch($data);
    }
}
