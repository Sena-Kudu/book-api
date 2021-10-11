<?php

use App\Models\UserModel;
use Config\Services;
use Firebase\JWT\JWT;

function getJWTFromRequest($authenticationHeader): string {
    if (is_null($authenticationHeader)) { //JWT is absent
        throw new Exception('Missing or invalid JWT in request');
    }
    //JWT is sent from client in the format Bearer XXXXXXXXX
    return explode(' ', $authenticationHeader)[1];
}

function validateJWTFromRequest(string $encodedToken){
    $key = Services::getSecretKey();
    $decodedToken = JWT::decode($encodedToken, $key, ['HS256']);
    $userModel = new UserModel();
    $userModel->userIDKontrol($decodedToken->user_id);
}

function getUserID(){

  $key = Services::getSecretKey();

  try {
    $token_decode = JWT::decode(str_replace('Bearer ', '', Services::request()->getHeaderLine('Authorization')), $key, ['HS256']);
   
  } catch (\Exception $e) {

    return false;

  }

  return (int) $token_decode->user_id;
}

function getSignedJWTForUser(int $user_id) {
    $issuedAtTime = time();
    $tokenTimeToLive = Services::getSecretTime();
    $tokenExpiration = $issuedAtTime + $tokenTimeToLive;
    $payload = [
        'user_id' => $user_id,
        'exp' => $tokenExpiration,
    ];

    $jwt = JWT::encode($payload, Services::getSecretKey());
    return $jwt;
}
