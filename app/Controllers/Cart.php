<?php

namespace App\Controllers;

use App\Models\CartModel;
use App\Models\BookModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use ReflectionException;
use Config\Services;
use Firebase\JWT\JWT;

class Cart extends BaseController
{
    protected $cartModel;
    protected $bookModel;
    protected $userID;

    public function __construct() {

        helper('jwt');
        
        $this->cartModel = new CartModel();
        $this->bookModel = new BookModel();
        $this->userID = getUserID();

    }

    public function cartIslem() {

        /** Default Values */
        $data = [
            'sepet' => [
                'status' => false,
                'total_books'  => null
            ]
        ];

        if(($this->userID) !== false) {

            $input = $this->getRequestInput($this->request);
            $cart = $this->cartModel->cartDetail($this->userID);

             /**sepette kitap var mı? */
            if(is_null($cart)) {

                /**Ürün sepette yoksa insert */
                $cart_id = $this->cartModel->insert([
                    'user_id' => $this->userID,
                    'books'   => serialize($input),
                    'status'  => 1
                ]);

                $data['sepet'] = [
                    'status' => true,
                    'total_books' => count($input)
                ];

            } else {

                /**Ürün sepette varsa güncelleme */
                $books = unserialize($cart['books']);

                foreach($input as $in_book) {

                    $key = array_search($in_book['book_id'], array_column($books, 'book_id')); 

                    if($key !== false) {
                        $books[$key]= $in_book;
                    } else {
                        array_push($books, $in_book);
                    }

                }

                settype($cart['sepet_id'], 'int');
                $this->cartModel->update($cart['sepet_id'], [
                    'books' => serialize($books)
                ]);

                $data['sepet'] = [
                    'status' => true,
                    'total_books_type' => count($input) //adet gönderilecek
                ];
                
            }   
            
            return $this->getResponse($data,
            ResponseInterface::HTTP_OK);

        } 

        return $this->getResponse($data,
        ResponseInterface::HTTP_BAD_REQUEST);

    }

    public function cartDetail() {

        /** Default Values */
        $data = [
            'kitaplar' => [
                'status' => false,
                'data'   => null
            ],
            'sepet' => [
                'status' => false,
                'data'   => null
            ]
        ];

        if($this->userID !== false) {

            $cart = $this->cartModel->cartDetail($this->userID);
            $books_in_cart = unserialize($cart['books']);    
            $total_books = count($books_in_cart);
            $cart_total_price = (float) 0;

            foreach($books_in_cart as $key => $book) {
                
                $total_price = (float) 0;
                $cart_books[$key] = $this->bookModel->getCartDetail($book['book_id']);

                $total_price = ((float) $cart_books[$key]['price']) * ((int) $book['adet']);
                //$cart_total_price = ((float) $total_price)+((float) $cart_total_price); 

                /**kitap data kısmına eklenecekler */
                $cart_books[$key]['adet'] = (int) $book['adet'];
                $cart_books[$key]['total_price'] = $total_price;

                /**Formatting id and price */
                settype($cart_books[$key]['book_id'], 'int');
                $cart_books[$key]['price'] = number_to_currency($cart_books[$key]['price'], 'TRY', 'tr_TR', 2);
                $cart_books[$key]['total_price'] = number_to_currency($cart_books[$key]['total_price'], 'TRY', 'tr_TR', 2);
                ////$cart_total_price = number_to_currency($cart_total_price, 'TRY', 'tr_TR', 2);

            }

            /**Detayların sepete eklenmesi */
            $data['kitaplar']['data'] = $cart_books;
            $data['kitaplar']['status'] = true;
            $data['sepet']['data']['cart_total'] = $cart_total_price;
            $data['sepet']['data']['total_books'] = $total_books;

            return $this->getResponse($data,
            ResponseInterface::HTTP_OK);

        } 

        return $this->getResponse($data,
        ResponseInterface::HTTP_BAD_REQUEST);

    }

    public function deleteBookOnCart() {

        $data = [
            'success' => false
        ];

        $input = $this->getRequestInput($this->request);

        if(is_null($input) || (int) $input['book_id'] == 0){
          return $this->getResponse($data);
        }

        if($this->userID !== false) { 

            $cart = $this->cartModel->cartDetail($this->userID);
            settype($cart['sepet_id'], 'int');
            $books_in_cart = unserialize($cart['books']);

            if(is_array($books_in_cart)) {

                $key = array_search($input['book_id'],array_column($books_in_cart, 'book_id'));

                if($key !== false ) {//var_dump($key); die;

                    array_splice($books_in_cart,$key, 1); 
                    $this->cartModel->update($cart['sepet_id'], [
                        'books' => serialize($books_in_cart)
                    ]);

                    $data['success'] = true;

                    return $this->getResponse($data,
                    ResponseInterface::HTTP_OK);

                } else {

                    /* Sepeti Sil */
                    $this->cartModel->delete($cart['sepet_id']);

                }

            }
            
        }

        return $this->getResponse($data,
        ResponseInterface::HTTP_BAD_REQUEST);

    }

}