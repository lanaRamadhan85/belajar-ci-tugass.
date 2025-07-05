<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TransactionDetailSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'transaction_id' => 1,
                'product_id' => 1,
                'jumlah' => 1,
                'diskon' => 0,
                'subtotal_harga' => 10899000,
                'created_at' => date("Y-m-d H:i:s"),
            ],
            [
                'transaction_id' => 1,
                'product_id' => 2,
                'jumlah' => 2,
                'diskon' => 0,
                'subtotal_harga' => 2 * 6899000,
                'created_at' => date("Y-m-d H:i:s"),
            ],
            [
                'transaction_id' => 2,
                'product_id' => 3,
                'jumlah' => 1,
                'diskon' => 0,
                'subtotal_harga' => 6299000,
                'created_at' => date("Y-m-d H:i:s"),
            ]
        ];
        foreach ($data as $item) {
            $this->db->table('transaction_detail')->insert($item);
        }
    }
} 