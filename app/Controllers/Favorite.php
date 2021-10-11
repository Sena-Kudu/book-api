<?php

namespace App\Controllers;

use App\Models\FavoriteModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use ReflectionException;

class Favorite extends BaseController {

    protected $userID;
    protected $favModel;

    public function __construct() {

        helper('jwt');

        $this->favModel = new FavoriteModel();
        $this->userID = getUserID();

    }

    public function favoriteList() {

        /**Favori Ekrani misafir girisi */
        $data = [
            'status' => false,
            'data'   => null
        ];

        if(($this->userID) !== false) { 

            $all_fav = $this->favModel->getUserFavorites($this->userID);

            foreach($all_fav as $key => $fav) {

                settype($all_fav[$key]['book_id'], 'int');

            }
   
            $data['data'] = $all_fav;
            $data['status'] = true;

        }

        return $this->getResponse($data,
        ResponseInterface::HTTP_OK);

    }

    public function addFavorite() {

        $data = [
            'success' => false
        ];

        $input = $this->getRequestInput($this->request);

        if(($this->userID) !== false) {

            $favorite = $this->favModel->insert([
                'book_id' => $input['book_id'],
                'user_id' => $this->userID
            ]);

            if(!$favorite) {

                return $this->getResponse($data,
                ResponseInterface::HTTP_BAD_REQUEST);

            }

            $data['success'] = true;

            return $this->getResponse($data,
            ResponseInterface::HTTP_OK);

        }

        return $this->getResponse($data,
        ResponseInterface::HTTP_BAD_REQUEST);


    }

    public function deleteFavorite() {

        $data = [
            'success' => false
        ];

        $input = $this->getRequestInput($this->request);

        if(($this->userID) !== false) {

            $fav_id = $this->favModel->selectFavId($this->userID,$input['book_id']);

            $delete = $this->favModel->delete($fav_id);

            if(!$delete) {

                return $this->getResponse($data,
                ResponseInterface::HTTP_BAD_REQUEST);

            }

            $data['success'] = true;

            return $this->getResponse($data,
            ResponseInterface::HTTP_OK);

        }

    }

}