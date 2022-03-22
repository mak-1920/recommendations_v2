<?php

declare(strict_types=1);

namespace App\Controller\Settings;

use App\Services\ThemeManager\Exceptions\UndefinedThemeException;
use App\Services\ThemeManager\ThemeManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ThemeController extends AbstractController
{
    public function __construct(
        private ThemeManagerInterface $themeManager,
    )
    {
    }

    #[Route(
        '{_locale<%app.locales%>}/settings/theme/{themeName}',
        name: 'settings_theme',
        methods: ['GET'],
    )]
    public function setTheme(
        string $themeName,
        Request $request,
    ): Response {
        $prevPage = $request->headers->get('referer');
        if (is_null($prevPage)) {
            $prevPage = $this->generateUrl('reviews');
        }

        $response = new RedirectResponse($prevPage);

        try {
            $this->themeManager->setTheme($themeName, $response);
        } catch (UndefinedThemeException $e) {
            $response->setContent($e->getMessage());
        }

        return $response;
    }
}
