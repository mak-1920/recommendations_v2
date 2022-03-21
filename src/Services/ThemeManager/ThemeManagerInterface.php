<?php

declare(strict_types=1);

namespace App\Services\ThemeManager;

use App\Services\ThemeManager\Exceptions\UndefinedThemeException;

/**
 * @template TGetterStorage
 * @template TSetterStorage
 */
interface ThemeManagerInterface
{
    /**
     * @return string[]
     */
    public function getThemes(): array;

    /**
     * @param TGetterStorage $storage
     *
     * @return string
     */
    public function getTheme(mixed $storage): string;

    /**
     * @param string $themeName
     * @param TSetterStorage $storage
     *
     * @return void
     *
     * @throws UndefinedThemeException
     */
    public function setTheme(string $themeName, mixed $storage): void;
}
