<?php

namespace App\Twig;

use App\Service\TranslatorService;
use App\Traits\HasRequestStack;
use App\Traits\HasRouter;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class RequestExtension extends AbstractExtension implements GlobalsInterface
{
    use HasRequestStack;
    use HasRouter;

    /**
     * @return array<mixed>
     */
    public function getGlobals(): array
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request) {
            return [
                'current_locale' => 'en',
                'current_route'  => 'app_index',
            ];
        }

        $pathParts = explode('/', $request->getPathInfo());
        $locale    = $pathParts[1] ?: 'en';

        return [
            'current_locale' => in_array($locale, TranslatorService::LOCALES) ? $locale : 'en',
            'current_route'  => $request->get('_route') ?: 'app_index',
        ];
    }
}
