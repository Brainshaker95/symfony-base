<?php

namespace App\Twig;

use App\Service\TranslatorService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class RequestExtension extends AbstractExtension implements GlobalsInterface
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var RouterInterface
     */
    protected $router;

    public function __construct(RequestStack $requestStack, RouterInterface $router)
    {
        $this->requestStack = $requestStack;
        $this->router       = $router;
    }

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

        $locale = $request->getLocale();

        return [
            'current_locale' => in_array($locale, TranslatorService::LOCALES) ? $locale : 'en',
            'current_route'  => $request->get('_route') ?: 'app_index',
        ];
    }
}
