<?php
namespace App\Jwt;

use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTDecodedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class JwtEventListener
{
    public function __construct(
        private RefreshTokenService $refreshTokenService,
        private UserRepository $userRepository
    ){}

    public function onJWTDecoded(JWTDecodedEvent $event)
    {
        $payload = $event->getPayload();

        if(!isset($payload['email'])){
            $event->markAsInvalid();
        }
        $email = $payload['email'];
        $user = $this->userRepository->findByEmail($email);
        if($user->getIsEmailVerified() === false){
            $event->markAsInvalid();
        }
    }


    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {

        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }
        $userEntity = $this->userRepository->findByEmail($user->getUserIdentifier());

        $data['refreshToken'] = $this->refreshTokenService->generateRefreshToken($userEntity);

        $event->setData($data);

    }

}
?>