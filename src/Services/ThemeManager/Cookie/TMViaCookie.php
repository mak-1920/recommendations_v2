<?php

declare(strict_types=1);

namespace App\Services\ThemeManager\Cookie;

use App\Services\ThemeManager\Exceptions\UndefinedThemeException;
use App\Services\ThemeManager\ThemeManagerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @implements ThemeManagerInterface<Request, Response>
 */
class TMViaCookie implements ThemeManagerInterface
{
    private const THEMES = [
        'dark',
        'light',
    ];

    /**
     * {@inheritDoc}
     */
    public function getThemes(): array
    {
        return self::THEMES;
    }

    /**
     * {@inheritDoc}
     */
    public function getTheme(mixed $storage): string
    {
        /** @var string|null $currentTheme */
        $currentTheme = $storage->cookies->get('theme');
        if (!in_array($currentTheme, self::THEMES)) {
            $currentTheme = null;
        }

        return $currentTheme ?? self::THEMES[0];
    }

    /**
     * {@inheritDoc}
     */
    public function setTheme(string $themeName, mixed $storage): void
    {
        if (!in_array($themeName, self::THEMES)) {
            throw new UndefinedThemeException($themeName);
        }

        $cookie = new Cookie('theme', $themeName);
        $storage->headers->setCookie($cookie);
    }
}
