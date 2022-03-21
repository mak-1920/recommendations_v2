<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Review\Illustration;
use App\Entity\Review\Review;
use Cloudinary\Cloudinary;
use Doctrine\ORM\EntityManager;
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/** @todo */
class FileStorage
{
    private Cloudinary $cloudinary;

    public function __construct(
        string $url,
        private string $directory,
        private EntityManager $entityManager,
    ) {
        $this->cloudinary = new Cloudinary($url);
    }

    private function getPath(): string
    {
        return 'reviews/';
    }

    public function uploadIllustration(UploadedFile $file): string|false
    {
        if ($file) {
            $response = $this->cloudinary->uploadApi()->upload($file->getPathname(), [
                'folder' => $this->getPath(),
                'eager' => [
                    'width' => 500,
                    'height' => 500,
                ],
            ]);

            return $response->offsetGet('public_id');
        }

        return false;
    }

    public function removeIllustration(string $fileName): bool
    {
        $res = false;
        try {
            $response = $this->cloudinary->uploadApi()->destroy($fileName);
            $res = (bool) $response->offsetGet('result');
        } catch (Exception $e) {
        }

        return true;
    }

    public function updateReviewIllustrations(Review $review, ?array $newIllustrations, bool $flush = true): Review
    {
        if (null == $newIllustrations) {
            $newIllustrations = [];
        }
        $oldIllustrations = $review->getIllustrations();

        foreach ($oldIllustrations as $illustraion) {
            $index = array_search($illustraion->getImg(), $newIllustrations);
            if (false !== $index) {
                unset($newIllustrations[$index]);
            } else {
                $this->removeIllustration((string) $review->getId(), $illustraion->getImg());
                $review->removeIllustration($illustraion);
            }
        }

        foreach ($newIllustrations as $name) {
            $newIllustration = new Illustration();
            $newIllustration->setImg($name);

            $review->addIllustration($newIllustration);
        }

        if ($flush) {
            $this->entityManager->flush();
        }

        return $review;
    }

    public function removeFiles(array $illustrations): void
    {
        foreach ($illustrations as $illustration) {
            $this->removeIllustration($illustration);
        }
    }
}
