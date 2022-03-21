<?php

declare(strict_types=1);

namespace App\Controller\AP;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminPanelController extends AbstractController
{
    #[Route(
        '/{_locale<%app.locales%>}/ap',
        name: 'apanel',
        methods: ['GET'],
    )]
    /** @todo */
    public function index(): Response
    {
        $users = $this->userRepository->findBy([], ['id' => 'ASC']);

        return $this->render('ap/index.html.twig', [
            'users' => $users,
        ]);
    }
}
