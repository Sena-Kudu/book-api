<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\BookModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use ReflectionException;

class Dash extends BaseController {

    protected $userModel;
    protected $bookModel;
    protected $userID;

    public function __construct() {

        helper('jwt');

        $this->userModel = new UserModel();
        $this->bookModel = new BookModel();
        $this->userID = getUserID();

    }

    public function dash() {

        $get_books = array_map(function($books) {
            settype($books['id'], 'int');
            return $books;
        }, $this->bookModel->getAllBooks());

        /** Dashboarda gönderilecek bilgiler */
        $data = [
            'user' => [
                'status' => false,
                'data' => null
            ],
            'book' => [
                'status' => true,
                'data' => $get_books
            ]
        ];

        /** User Kontrol */
        if($this->userID !== false) {

            $user = $this->userModel->getUsername($this->userID);
            $data['user'] = [
                'status' => true,
                'data'   => $user['username']
            ];
            
        }

        return $this->getResponse($data,
        ResponseInterface::HTTP_OK);

    }

} 