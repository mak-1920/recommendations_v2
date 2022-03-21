<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Services\ThemeManager\Cookie\TMViaCookie;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class ThemeManagerSubscriber implements EventSubscriberInterface
{
    public function onKernelRequest(RequestEvent $event): void
    {
        $tm = new TMViaCookie();
        $request = $event->getRequest();

        $request->attributes->add(['theme' => $tm->getTheme($request)]);
        $request->attributes->add(['themes' => $tm->getThemes()]);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.request' => 'onKernelRequest',
        ];
    }
}
