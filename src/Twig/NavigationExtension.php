<?php

namespace App\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NavigationExtension extends AbstractExtension
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct(
        RequestStack $requestStack,
        RouterInterface $router,
        TranslatorInterface $translator
    ) {
        $this->requestStack = $requestStack;
        $this->router       = $router;
        $this->translator   = $translator;
    }

    /**
     * @return array<TwigFunction>
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('navigation', [$this, 'getNavigation']),
        ];
    }

    /**
     * @return array<int|string, array{navigation_name: string, path: string, is_active: bool, role: string|null, hide_on_auth: bool}>
     */
    public function getNavigation(string $type = 'main')
    {
        $request    = $this->requestStack->getCurrentRequest();
        $routes     = $this->router->getRouteCollection()->all();
        $navigation = [];

        foreach ($routes as $key => $route) {
            $options        = $route->getOptions();
            $navigationType = isset($options['navigation']) ? $options['navigation'] : '';

            if (strpos($key, 'app_') !== 0 || $navigationType !== $type) {
                continue;
            }

            $name  = substr($key, 4, strlen($key));
            $role  = isset($options['role']) ? $options['role'] : null;
            $order = isset($options['order']) ? $options['order'] : 0;

            $navigation[$order] = [
                'navigation_name' => $this->translator->trans('navigation_name.' . $name),
                'path'            => $this->router->generate('app_' . $name),
                'is_active'       => $request ? $request->get('_route') === 'app_' . $name : false,
                'role'            => $role,
                'hide_on_auth'    => $role === 'HIDE_ON_AUTH',
                'show_on_auth'    => $role === 'SHOW_ON_AUTH',
            ];
        }

        ksort($navigation);

        return $navigation;
    }
}
