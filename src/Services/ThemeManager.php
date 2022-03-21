<?php

declare(strict_types=1);

namespace App\Services;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

/** @todo */
class ThemeManager
{
    private ?Request $request;
    private array $themes;

    public function __construct(
        RequestStack $requestStack,
    ) {
        $this->themes = [
            'light',
            'dark',
        ];
        $this->request = $requestStack->getCurrentRequest();
    }

    public function getTheme(): string
    {
        $theme = $this->request->cookies->get('theme');
        if (!$theme || !in_array($theme, $this->themes)) {
            $theme = 'dark';
            $this->setTheme($theme);
        }

        return $theme;
    }

    public function setTheme(string $theme, Response $response = null): bool
    {
        if (!in_array($theme, $this->themes)) {
            return false;
        }
        if (!$response) {
            $response = new RedirectResponse($this->request->getUri());
            $response->headers->setCookie(Cookie::create('theme', $theme));
            $response->send();
        } else {
            $response->headers->setCookie(Cookie::create('theme', $theme));
        }

        return true;
    }

    public function getThemes(): array
    {
        return $this->themes;
    }
}
