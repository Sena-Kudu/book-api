<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;

class FavoriteModel extends Model
{
    protected $table      = 'favorite_book';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id','book_id'];
    protected $allowCallbacks = false;

    public function getUserFavorites($user_id) {

        return $this->select([
            'book.id as book_id',
            'book.book_name',
            'book.image'
        ])
        ->join('book','favorite_book.book_id = book.id')
        ->where([
            'favorite_book.user_id' => $user_id
        ])
        ->findAll();

    }

    public function selectFavId($user_id,$book_id) {

        return $this->select([
            'id as fav_id'
        ])
        ->where([
            'user_id' => $user_id,
            'book_id' => $book_id
        ])
        ->limit(1)->first();
    }

}