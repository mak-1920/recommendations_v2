<?php

declare(strict_types=1);

namespace App\Controller\Review;

use App\Repository\Review\TagRepository;
use App\Services\Paginator\Reviews\ReviewsPaginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TagController extends AbstractController
{
    #[Route(
        '/{_locale<%app.locales%>}/review-tag-{tagName}',
        name: 'review_tag',
        requirements: ['name' => '.+'],
        methods: ['GET'],
    )]
    public function byTag(
        TagRepository $repository,
        string $tagName,
    ): Response {
        $tags = $repository->findAllOrderByName();
        $lastId = $repository->getLastReviewIdWithTag($tagName);

        return $this->render('review/tag.html.twig', [
            'sortedType' => ReviewsPaginator::REVIEW_SORT_TYPES,
            'selectedSortType' => '',
            'tags' => $tags,
            'tagName' => $tagName,
            'lastId' => $lastId,
        ]);
    }
}
