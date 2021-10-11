<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['email','password','username'];
    protected $allowCallbacks = false;
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function userIDKontrol(string $id) {
        $user = $this
            ->asArray()
            ->where(['id' => $id])
            ->first();
    
        if (!$user)
            throw new Exception('Kullanıcı bulunamadı.');
    
        return $user;
    }

    public function findUserByEmailAddress(string $mail) {
        
        $user = $this
            ->asArray()
            ->where(['email' => $mail])
            ->first();
    
        return $user;

    }

    public function userInformation($user_id) {

        return $this
        ->select([
            'id',
            'email',
            'password',
            'username'
        ])
        ->where([
            'id' => $user_id
        ])
        ->limit(1)->first();

    }

    public function getUsername($user_id) {

        return $this
        ->select([
            'username'
        ])
        ->where([
            'id' => $user_id
        ])
        ->limit(1)->first();

    }

}