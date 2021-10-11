<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use ReflectionException;
use Config\Services;
use Firebase\JWT\JWT;

class Auth extends BaseController
{
    /**
     * Register a new user
     * @return Response
     * @throws ReflectionException
     */

    public function login()
    {
        $rules = [

            'email' => 'required|min_length[6]|max_length[50]|valid_email',
            'password' => 'required|min_length[8]|max_length[255]'
        ];

        $errors = [
            'email' => [
                'min_length' => 'Email en az 6 karakterden oluşmalıdır.',
                'max_length' => 'Email en fazla 50 karakterden oluşmalıdır.',
                'valid_email'=> 'Geçerli email adresi giriniz.'
            ],
            'password' => [

                'min_length' => 'Şifre en az 8 karakterden oluşmalıdır.',
                'max_length' => 'Şifre en fazla 50 karakterden oluşmalıdır.'
            ]
        ];

        $input = $this->getRequestInput($this->request);
        if (!$this->validateRequest($input, $rules, $errors)) {
            return $this->getResponse(
                $this->validator->getErrors(),
                ResponseInterface::HTTP_BAD_REQUEST
            );
        }

        $userModel = new UserModel();
        $user = $userModel->findUserByEmailAddress($input['email']); 

         /* user kayıtlı */
        if (!is_null($user)) {

            if($user['password'] === $input['password']) {

                $user_status = true; 
                settype($user['id'], 'int');
                return $this->getJWTForUser($user['id'],$user_status,
                    ResponseInterface::HTTP_CREATED
                );

            } else {

                $user_status = false; 
                return $this->getResponse(
                    [
                        'error' => 'Kullanıcı Şifresi hatalı',
                        'user_status' => $user_status

                    ],
                    ResponseInterface::HTTP_BAD_REQUEST
                );

            } 

        } else {

            $user_status = false;
            return $this->getResponse(
                [
                    'error' => 'Kullanıcı kayıtlı değil',
                    'user_status' => $user_status
                ],
                ResponseInterface::HTTP_BAD_REQUEST
            );
        }
    }

    public function register()
    {
        $rules = [

            'email' => 'required|min_length[6]|max_length[50]|valid_email',
            'password' => 'required|min_length[8]|max_length[255]',
            'username' => 'required|min_length[8]|max_length[30]',
        ];

        $errors = [
            'email' => [
                'min_length' => 'Email en az 6 karakterden oluşmalıdır.',
                'max_length' => 'Email en fazla 50 karakterden oluşmalıdır.',
                'valid_email'=> 'Geçerli email adresi giriniz.'
            ],
            'password' => [

                'min_length' => 'Şifre en az 8 karakterden oluşmalıdır.',
                'max_length' => 'Şifre en fazla 50 karakterden oluşmalıdır.'
            ],
            'username' => [

                'min_length' => 'Kullanıcı adı en az 8 karakterden oluşmalıdır.',
                'max_length' => 'Kullanıcı adı en fazla 30 karakterden oluşmalıdır.'
            ]
        ];

        $input = $this->getRequestInput($this->request);
        if (!$this->validateRequest($input, $rules, $errors)) {

            return $this->getResponse($this->validator->getErrors(),
                    ResponseInterface::HTTP_BAD_REQUEST
                );

        }

        $userModel = new UserModel();
        $user = $userModel->findUserByEmailAddress($input['email']);

        if(!is_null($user)) {

            $user_status = false;
            return $this->getResponse(
                [
                    'error' => 'Girmiş olduğunuz eposta adresi başka kullanıcı tarafından kullanılmaktadır.',
                    'user_status' => $user_status
                ],
                ResponseInterface::HTTP_BAD_REQUEST
            );

        } else {

            $user_id = $userModel->insert([
                'email' => $input['email'],
                'password' => $input['password'],
                'username' => $input['username']
            ]);
            $user_status = true;
    
            return $this->getJWTForUser($user_id,$user_status,
                    ResponseInterface::HTTP_CREATED
                );

        }
  
    }

    /* Kullanıcı için jwt token oluşturulur */
    private function getJWTForUser(int $id,bool $user_status,
        int $responseCode = ResponseInterface::HTTP_OK) 
        {

        try {
            $model = new UserModel();
            $user = $model->userInformation($id);
            unset($user['password']);

            helper('jwt');

            return $this->getResponse(
                    [
                        'user_status' => $user_status,
                        'username' => $user['username'],
                        'token' => getSignedJWTForUser($user['id'])
                    ],
                    $responseCode
                );

        } catch (Exception $exception) {

            return $this->getResponse(
                    [
                        'error' => $exception->getMessage(),
                    ],
                    $responseCode
                );
        }
    }
}
