<?php

declare(strict_types=1);

namespace App\Controller\Users;

use App\Repository\Review\ReviewRepository;
use App\Repository\User\UserRepository;
use App\Services\Paginator\Reviews\ReviewsPaginator;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route(
        '/{_locale<%app.locales%>}/id{id}',
        name: 'user_page',
        methods: ['GET'],
    )]
    public function userPage(
        int $id,
        Request $request,
        UserRepository $repository,
        ReviewRepository $reviewRepository,
    ): Response {
        try {
            $user = $repository->find($id);
        } catch (Exception) {
            throw $this->createNotFoundException('User with id'.$id.' not found!');
        }
        $orderBy = $request->query->get('type', ReviewsPaginator::REVIEW_SORT_TYPES[0]);
        $lastId = $reviewRepository->getLastReviewID();

        return $this->render('user/page.html.twig', [
            'user' => $user,
            'sortedTypes' => ReviewsPaginator::REVIEW_SORT_TYPES,
            'sortType' => $orderBy,
            'lastId' => $lastId,
        ]);
    }
}
