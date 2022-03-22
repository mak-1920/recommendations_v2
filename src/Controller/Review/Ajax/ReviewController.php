<?php

/** @noinspection PhpUnused */

declare(strict_types=1);

namespace App\Controller\Review\Ajax;

use App\Entity\Users\User;
use App\Services\Paginator\Reviews\AllReviewsPaginator;
use App\Services\Paginator\Reviews\ReviewsByTagPaginator;
use App\Services\Paginator\Reviews\ReviewsByUser;
use App\Services\Paginator\Reviews\ReviewsPaginator;
use App\Services\Reviews\Exceptions\ReviewException;
use App\Services\Reviews\Score\Like;
use App\Services\Reviews\Score\Rating;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReviewController extends AbstractController
{
    #[Route(
        '/{_locale<%app.locales%>}/ajax/sortable-reviews/page',
        name: 'reviews_with_sort_page',
        options: [
            'expose' => true,
        ],
        methods: ['POST']
    )]
    public function reviewSortablePage(
        Request $request,
        AllReviewsPaginator $paginator,
    ): Response {
        [$page, $lastId, $orderBy] = $this->getValuesFromRequest($request);

        $reviews = $paginator->paginate($page, $lastId, ['orderBy' => (string) $orderBy], ReviewsPaginator::REVIEWS_ON_PAGE);

        return $this->json([
            'html' => $this->render('review/reviews-list-page.html.twig', [
                'reviewsInfo' => $reviews,
            ]),
            'isEnd' => count($reviews) < ReviewsPaginator::REVIEWS_ON_PAGE,
        ]);
    }

    #[Route(
        '/{_locale<%app.locales%>}/ajax/reviews-by-tag/page',
        name: 'reviews_by_tag_page',
        options: [
            'expose' => true,
        ],
        methods: ['POST']
    )]
    public function reviewPageByTag(
        Request $request,
        ReviewsByTagPaginator $paginator,
    ): Response {
        [$page, $lastId, $tag] = $this->getValuesFromRequest($request);
        $tag = mb_substr($tag, 4);

        $reviews = $paginator->paginate($page, $lastId, ['tag' => $tag], ReviewsPaginator::REVIEWS_ON_PAGE);

        return $this->json([
            'html' => $this->render('review/reviews-list-page.html.twig', [
                'reviewsInfo' => $reviews,
            ]),
            'isEnd' => count($reviews) < ReviewsPaginator::REVIEWS_ON_PAGE,
        ]);
    }

    #[Route(
        '/{_locale<%app.locales%>}/ajax/reviews-by-user/page',
        name: 'reviews_by_user_page',
        options: [
            'expose' => true,
        ],
        methods: ['POST'],
    )]
    public function reviewByUser(
        Request $request,
        ReviewsByUser $paginator,
    ): Response {
        [$page, $lastId, $params] = $this->getValuesFromRequest($request);
        [$userId, $sortType] = explode(',', $params);

        $reviews = $paginator->paginate($page, $lastId, ['orderBy' => $sortType, 'userId' => intval($userId)], ReviewsPaginator::REVIEWS_ON_PAGE);

        return $this->json([
            'html' => $this->render('review/reviews-list-page.html.twig', [
                'reviewsInfo' => $reviews,
            ]),
            'isEnd' => count($reviews) < ReviewsPaginator::REVIEWS_ON_PAGE,
        ]);
    }

    #[Route(
        '/ajax/review/like/id{id}',
        name: 'review_like',
        requirements: ['id' => '\d+'],
        options: [
            'expose' => true,
        ],
        methods: ['POST']
    )]
    public function reviewLike(
        int $id,
        Like $like,
    ): Response {
        /** @var ?User $user */
        $user = $this->getUser();

        if (is_null($user)) {
            throw $this->createAccessDeniedException('You must be authorized');
        }

        try {
            $operationResult = $like->set($id, $user);
        } catch (ReviewException $e) {
            throw $this->createAccessDeniedException($e->getMessage(), $e);
        }

        return $this->json([
            'result' => $operationResult['operation'],
            'likesCount' => $operationResult['review']->getLikes()->count(),
        ]);
    }

    #[Route(
        '/ajax/review/set-rating/id{id}',
        name: 'review_set_rating',
        requirements: ['id' => '\d+'],
        options: [
            'expose' => true,
        ],
        methods: ['POST']
    )]
    public function reviewSetRating(
        int $id,
        Request $request,
        Rating $rating,
    ): Response {
        /** @var ?User $user */
        $user = $this->getUser();
        $value = (int) $request->request->get('value');

        if (is_null($user)) {
            throw $this->createAccessDeniedException('You must be authorized');
        }

        try {
            $res = $rating->set($id, $user, $value);
        } catch (ReviewException) {
            $res = false;
        }

        return $this->json([
            'add' => $res,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return array{int, int, mixed}
     */
    private function getValuesFromRequest(Request $request): array
    {
        $lastId = $request->request->getInt('lastId');
        $param = $request->request->get('param');
        $page = $request->request->getInt('page');

        return [$page, $lastId, $param];
    }
}
