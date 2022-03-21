<?php

declare(strict_types=1);

namespace App\Services\ThemeManager\Exceptions;

use Throwable;

class UndefinedThemeException extends ThemeException
{
    /**
     * @param string $themeName
     * @param Throwable|null $previous
     */
    public function __construct(string $themeName = '', ?Throwable $previous = null)
    {
        parent::__construct('Undefined theme: '.$themeName, 0, $previous);
    }
}
