<?php

namespace App\Controller\Search;

use App\Services\Paginator\Reviews\ReviewsPaginator;
use App\Services\Searcher\SearcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    public function __construct(
        private SearcherInterface $searcher,
    ) {
    }

    #[Route(
        '/{_locale<%app.locales%>}/search',
        name: 'review_search',
        methods: ['GET'],
    )]
    public function search(
        Request $request,
    ): Response
    {
        $query = (string) ($request->query->get('q') ?? '');

        return $this->render('review/search.html.twig', [
            'query' => $query,
            'count' => $this->searcher->getResultsCount($query),
        ]);
    }

    #[Route(
        '/{_locale<%app.locales%>}/ajax/search/page',
        name: 'review_search_page',
        options: [
            'expose' => true,
        ],
        methods: ['POST'],
    )]
    /** @todo */
    public function page(
        Request $request,
    ): Response
    {
        $query = (string) $request->request->get('param');
        $page = $request->request->getInt('page', 1);

        $reviews = $this->searcher->getResultsByPage($query, $page, ReviewsPaginator::REVIEWS_ON_PAGE);

        return $this->json([
            'html' => $this->render('review/reviews-list-page.html.twig', [
                'reviewsInfo' => $reviews,
            ]),
            'isEnd' => count($reviews) < ReviewsPaginator::REVIEWS_ON_PAGE,
        ]);
    }
}
