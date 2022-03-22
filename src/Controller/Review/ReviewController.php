<?php

declare(strict_types=1);

namespace App\Controller\Review;

use App\Entity\Review\Review;
use App\Entity\Users\User;
use App\Form\Review\ReviewCreatorType;
use App\Repository\Review\CommentRepository;
use App\Repository\Review\ReviewRepository;
use App\Repository\Review\TagRepository;
use App\Services\FileStorage;
use App\Services\Indexer\ElasticSearch\ESIndexer;
use App\Services\Paginator\Reviews\ReviewsPaginator;
use App\Services\Reviews\Score\Like;
use App\Services\Reviews\Score\Rating;
use App\Services\Searcher;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;

class ReviewController extends AbstractController
{
    #[Route(
        '/{_locale<%app.locales%>}/',
        name: 'reviews',
        methods: ['GET'],
    )]
    public function index(
        Request $request,
        TagRepository $tagRepository,
        ReviewRepository $reviewRepository,
        TranslatorInterface $translator,
    ): Response {
        /** @var string $sortedType */
        $sortedType = $request->query->get('type') ?? ReviewsPaginator::REVIEW_SORT_TYPES[0];

        $tags = $tagRepository->findAllOrderByName();
        $lastId = $reviewRepository->getLastReviewID();

        return $this->render('review/reviews-list.html.twig', [
            'sortedType' => ReviewsPaginator::REVIEW_SORT_TYPES,
            'selectedSortType' => $sortedType,
            'selectedSortTypeName' => $translator->trans($sortedType),
            'tags' => $tags,
            'lastId' => $lastId,
        ]);
    }

    #[Route(
        '/{_locale<%app.locales%>}/review/edit/id{id}',
        name: 'review_edit',
        requirements: ['id' => '\d+'],
        defaults: ['id' => '0'],
        methods: ['GET'],
    )]
    #[Route(
        '/{_locale<%app.locales%>}/review/create',
        name: 'review_create',
        methods: ['GET'],
    )]
    /** @TODO */
    public function create(
        Request $request,
        ESIndexer $eSIndexer,
        FileStorage $fileStorage,
        int $id = null,
    ): Response {
        /** @var User $user */
        $user = $this->getUser();
        $iscreating = true;

        $review = $this->reviewRepository->findByID($id);

        if (null == $review) {
            $review = new Review();
        } else {
            if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')
                    && $review->getAuthor() != $user) {
                throw new AccessDeniedException();
            }
            $user = $review->getAuthor();
            $iscreating = false;
        }

        if ($request->request->get('review_creator')) {
            $review->setTags([]);
        }
        $form = $this->createForm(ReviewCreatorType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tags = [];

            /** @var array $formData */
            $formData = $request->request->get('review_creator');

            if (isset($formData['tags'])) {
                $tags = $this->reviewTagRepository->getEntityFromStringArray($formData['tags']);
            }
            $illustrations = [];
            if (isset($formData['illustrations'])) {
                foreach ($formData['illustrations'] as $illustration) {
                    $illustrations[] = $illustration['img'];
                }
            }
            $review = $fileStorage->updateReviewIllustrations($review, $illustrations, false);
            $review = $this->reviewRepository->createOrUpdate($review, $user, $tags);

            if ($iscreating) {
                $eSIndexer->new($review);
            } else {
                $eSIndexer->edit($review);
            }

            return $this->redirect($this->generateUrl(
                'review_id',
                ['id' => $review->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            ));
        }

        return $this->renderForm('review/edit.html.twig', [
            'form' => $form,
            'isCreating' => $iscreating,
            'reviewId' => $iscreating ? 0 : $review->getId(),
        ]);
    }

    #[Route(
        '/{_locale<%app.locales%>}/review-id{id}',
        name: 'review_id',
        requirements: ['id' => '\d+'],
        methods: ['GET'],
    )]
    /**
     * @throws NonUniqueResultException
     */
    public function reviewId(
        int $id,
        CommentRepository $commentRepository,
        ReviewRepository $repository,
        Like $like,
        Rating $rating,
    ): Response {
        $reviewInfo = $repository->findByID($id);
        if (null == $reviewInfo) {
            throw $this->createNotFoundException('Review with id'.$id.' not found!');
        }
        /** @var ?User $user */
        $user = $this->getUser();

        $isLiked = $like->isLiked($reviewInfo['review'], $user);
        $rating = $rating->getRatingByUser($reviewInfo['review'], $user);
        $lastId = $commentRepository->getLastId();

        return $this->render('review/one-review-page.html.twig', [
            'reviewInfo' => $reviewInfo,
            'isLiked' => $isLiked,
            'ratingValue' => $rating,
            'lastId' => $lastId,
        ]);
    }

    #[Route(
        '{_locale<%app.locales%>}/review/remove-id{id}',
        name: 'review_remove',
        requirements: ['id' => '\d+'],
        methods: ['GET'],
    )]
    /** @TODO */
    public function remove(int $id, ESIndexer $eSIndexer, FileStorage $fileStorage): Response
    {
        $review = $this->reviewRepository->findByID($id);
        $user = $this->getUser();

        if (null == $review || !($review->getAuthor() == $user || false !== array_search(User::ROLE_ADMIN, $user->getRoles()))) {
            throw new AccessDeniedException();
        }

        $illustrations = [];
        foreach ($review->getIllustrations() as $illustration) {
            $illustrations[] = $illustration->getImg();
        }

        $eSIndexer->delete($review);
        $fileStorage->removeFiles($illustrations);

        $this->reviewRepository->remove($review);

        return $this->redirectToRoute('reviews');
    }
}
