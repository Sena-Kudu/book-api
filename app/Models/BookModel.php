<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;

class BookModel extends Model
{
    protected $table      = 'book';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id','book_id'];
    protected $allowCallbacks = false;

    public function getAllBooks() {

        return $this->select([
            'id',
            'book_name',
            'image',

        ])
        ->findAll();
       
    }

    public function getBookDetail($book_id) {

        return $this->select([
            'id',
            'book_name',
            'image',
            'pub_year',
            'author_name',
            'type',
            'price',
            'description',
            'page'
        ])
        ->where([
            'id' => $book_id
        ])
        ->limit(1)
        ->first();

    }

    public function getCartDetail($book_id) {

        return $this->select([
            'id as book_id',
            'book_name',
            'image',
            'price'
        ])
        ->where([
            'id' => $book_id
        ])
        ->limit(1)->first();

    }
}