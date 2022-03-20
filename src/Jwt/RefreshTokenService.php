<?php

namespace App\Jwt;


use App\Entity\User;
use App\Repository\UserRepository;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Symfony\Component\HttpKernel\KernelInterface;

class RefreshTokenService
{
    private int $tokenDuration =  3600 * 24 ;

    public function __construct(
        private string $encryptionKey
    ){

    }

    public function generateRefreshToken(User $user):string{
        $payload = array(
             "iss" => "booksWebsite",
             "aud"=> "booksWebsite",
             "exp" => time() + $this->tokenDuration,
             "email"=> $user->getEmail(),
             "roles" => $user->getRoles()
        );

        return JWT::encode($payload, $this->encryptionKey, 'HS256');
    }

     public function getDecodedJWT(string $token ):?JWTPayload{

             $decoded = JWT::decode($token, new Key($this->encryptionKey, 'HS256'));
              return new JWTPayload(
                  email: $decoded->email,roles: $decoded->roles
              );

     }


}