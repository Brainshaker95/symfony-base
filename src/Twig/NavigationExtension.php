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
     * @return array<int|string, array>
     */
    public function getNavigation(string $type = 'main')
    {
        $routeCollection = $this->router->getRouteCollection();
        $routes          = $routeCollection->all();
        $navigation      = [];

        foreach ($routes as $key => $route) {
            $options        = $route->getOptions();
            $navigationType = isset($options['navigation']) ? $options['navigation'] : '';

            if (strpos($key, 'app_') !== 0 || $navigationType !== $type) {
                continue;
            }

            $order    = isset($options['order']) ? $options['order'] : 0;
            $subpages = isset($options['subpages']) ? $options['subpages'] : [];

            $navigation[$order] = $this->getParsedNavigationItem(
                $this->getName($key),
                isset($options['role']) ? $options['role'] : null
            );

            if ($subpages) {
                foreach ($subpages as $subpage) {
                    $subpageRoute = $routeCollection->get($subpage);

                    if (!$subpageRoute) {
                        continue;
                    }

                    $subpageRouteOptions = $subpageRoute->getOptions();

                    $navigation[$order]['subpages'][] = $this->getParsedNavigationItem(
                        $this->getName($subpage),
                        isset($subpageRouteOptions['role']) ? $subpageRouteOptions['role'] : null
                    );
                }
            }
        }

        ksort($navigation);

        return $navigation;
    }

    private function getName(string $key): string
    {
        return substr($key, 4, strlen($key));
    }

    /**
     * @return array<mixed>
     */
    private function getParsedNavigationItem(string $name, ?string $role)
    {
        $request = $this->requestStack->getCurrentRequest();

        return [
            'navigation_name' => $this->translator->trans('navigation_name.' . $name),
            'path'            => $this->router->generate('app_' . $name),
            'is_active'       => $request ? $request->get('_route') === 'app_' . $name : false,
            'role'            => $role,
            'hide_on_auth'    => $role === 'HIDE_ON_AUTH',
            'show_on_auth'    => $role === 'SHOW_ON_AUTH',
        ];
    }
}
