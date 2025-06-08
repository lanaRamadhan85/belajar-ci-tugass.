<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductCategoryModel extends Model
{
    protected $table = 'product_category';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'category_name',
        'created_at',
        'updated_at'
    ];
}
