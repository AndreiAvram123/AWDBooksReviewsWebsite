<?php
namespace App\Jwt;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;

class JwtEventListener
{
    public function __construct(
        private RefreshTokenService $refreshTokenService
    ){

    }

    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }
        $data['refreshToken'] = $this->refreshTokenService->generateRefreshToken($user);

        $event->setData($data);
    }

}
?>