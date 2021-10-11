<?php

namespace App\Controllers;

use App\Models\BookModel;
use App\Models\FavoriteModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use ReflectionException;
use Config\Services;
use Firebase\JWT\JWT;

class Book extends BaseController
{
    protected $bookModel;
    protected $favoriteModel;
    protected $userID;

    public function __construct() {

      helper('jwt');

      $this->bookModel = new BookModel();
      $this->favoriteModel = new FavoriteModel();
      $this->userID = getUserID();

    }

    public function bookDetail() {

      /**Kitap Detay */
      $data = [
          'kitap_detay' => [
            'status' => false,
            'data' => []
          ],
          'favori' => [
            'status' => false,
          ],
          'sepet' => [
            'status' => false,
            'data' => [
              'toplam_urun_sayisi' => 0
            ]
          ]
      ];

      $input = $this->getRequestInput($this->request);
      $book_detail = $this->bookModel->getBookDetail($input['book_id']);

      settype($book_detail['id'], 'int');
      $book_detail['price'] = number_to_currency($book_detail['price'], 'TRY', 'tr_TR', 2);

      $data['kitap_detay'] = [
        'status' => true,
        'data'   => $book_detail
      ];

      if(($this->userID) !== false) {

        $is_there_fav = $this->favoriteModel->selectFavId($this->userID,$input['book_id']);
        if($is_there_fav !== null) {
          $data['favori'] ['status'] = true;
        }
        
      }

      return $this->getResponse($data,
      ResponseInterface::HTTP_OK);

    }

}