<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DiskonSeeder extends Seeder
{
    public function run()
    {
        $tanggal = date('Y-m-d');
        $created_at = date('Y-m-d H:i:s');
        for ($i = 0; $i < 10; $i++) {
            $data = [
                'tanggal' => date('Y-m-d', strtotime("$tanggal +$i day")),
                'nominal' => 100000,
                'created_at' => $created_at,
                'updated_at' => null
            ];
            $this->db->table('diskon')->insert($data);
        }
    }
} 