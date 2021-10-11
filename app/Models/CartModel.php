<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;

class CartModel extends Model
{
    protected $table      = 'cart';
    protected $primaryKey = 'sepet_id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    protected $allowedFields = ['books', 'user_id', 'status'];

    public function cartDetail($user_id) {

        return $this->select([
            'sepet_id',
            'books'
        ])
        ->where([
            'user_id' => $user_id,
            'status'  =>1
        ])
        ->limit(1)->first();

    }

}