<?php

/** @noinspection PhpUnused */

declare(strict_types=1);

namespace App\Controller\Auth;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OAuthController extends AbstractController
{
    #[Route(
        '/connect/google',
        name: 'connect_google_start',
        methods: ['GET'],
    )]
    public function redirectToGoogleConnect(ClientRegistry $clientRegistry): Response
    {
        return $clientRegistry
            ->getClient('google')
            ->redirect(['email', 'profile'], []);
    }

    #[Route(
        '/google/auth',
        name: 'google_auth',
        methods: ['GET'],
    )]
    public function connectGoogleCheck(): Response
    {
        if (!$this->getUser()) {
            return new JsonResponse(['status' => false, 'message' => 'User not found!']);
        } else {
            return $this->redirectToRoute('reviews');
        }
    }

    #[Route(
        '/connect/yandex',
        name: 'connect_yandex_start',
        methods: ['GET'],
    )]
    public function redirectToYandexConnect(ClientRegistry $clientRegistry): Response
    {
        return $clientRegistry
            ->getClient('yandex')
            ->redirect([], []);
    }

    #[Route(
        '/yandex/auth',
        name: 'yandex_auth',
        methods: ['GET'],
    )]
    public function connectYandexCheck(): Response
    {
        if (!$this->getUser()) {
            return new JsonResponse(['status' => false, 'message' => 'User not found!']);
        } else {
            return $this->redirectToRoute('reviews');
        }
    }
}
