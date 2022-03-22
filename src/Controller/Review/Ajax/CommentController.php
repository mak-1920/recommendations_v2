<?php

declare(strict_types=1);

namespace App\Controller\Review\Ajax;

use App\Entity\Users\User;
use App\Services\Paginator\Comments\CommentPaginator;
use App\Services\Reviews\Comments\CommentOperations;
use App\Services\Reviews\Comments\Exceptions\CommentsException;
use App\Services\Reviews\Exceptions\ReviewException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    #[Route(
        '/{_locale<%app.locales%>}/ajax/comment/page',
        name: 'comment_page',
        options: [
            'expose' => true,
        ],
        methods: ['POST'],
    )]
    public function index(
        Request $request,
        CommentPaginator $paginator,
    ): Response {
        $reviewId = $request->request->getInt('param');
        $lastId = $request->request->getInt('lastId');
        $page = $request->request->getInt('page');

        $comments = $paginator->paginate($page, $lastId, ['reviewId' => $reviewId], CommentPaginator::COMMENTS_ON_PAGE);

        return $this->json([
            'html' => $this->render('comment/comment-page.html.twig', [
                'commentsInfo' => $comments,
            ]),
            'isEnd' => count($comments) < CommentPaginator::COMMENTS_ON_PAGE,
        ]);
    }

    #[Route(
        '/ajax/comment/create',
        name: 'comment_create',
        options: [
            'expose' => true,
        ],
        methods: ['POST'],
    )]
    public function create(
        Request $request,
        CommentOperations $operations,
    ): Response {
        $reviewId = $request->request->getInt('reviewId');
        $text = (string) $request->request->get('text');
        /** @var ?User $user */
        $user = $this->getUser();

        if (is_null($user)) {
            throw $this->createAccessDeniedException();
        }

        try {
            $operations->create($reviewId, $user, $text);
        } catch (ReviewException $e) {
            throw $this->createAccessDeniedException($e->getMessage(), $e);
        }

        return $this->json(['result' => Response::HTTP_CREATED], Response::HTTP_CREATED);
    }

    #[Route(
        '/ajax/comment/remove',
        name: 'comment_remove',
        options: [
            'expose' => true,
        ],
        methods: ['POST'],
    )]
    public function remove(
        Request $request,
        CommentOperations $operations,
    ): Response {
        /** @var User $user */
        $user = $this->getUser();
        $commentId = (int) $request->request->get('id');

        try {
            $operations->remove($commentId, $user);
        } catch (CommentsException $e) {
            throw $this->createAccessDeniedException($e->getMessage(), $e);
        }

        return $this->json(['result' => true]);
    }
}
