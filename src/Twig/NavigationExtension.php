<?php

namespace App\Twig;

use App\Traits\HasRequestStack;
use App\Traits\HasRouter;
use App\Traits\HasTranslator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NavigationExtension extends AbstractExtension
{
    use HasRequestStack;
    use HasRouter;
    use HasTranslator;

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
            $navigationType = $options['navigation'] ?? '';

            if (strpos($key, 'app_') !== 0 || $navigationType !== $type) {
                continue;
            }

            $order    = $options['order']    ?? 0;
            $subpages = $options['subpages'] ?? [];

            $navigation[$order] = $this->getParsedNavigationItem(
                $this->getName($key),
                $options['role'] ?? null
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
                        $subpageRouteOptions['role'] ?? null
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
        $request         = $this->requestStack->getCurrentRequest();
        $navigationName  = $this->translator->trans('navigation_name.' . $name);
        $navigationTitle = $this->translator->trans('navigation_title.' . $name);

        return [
            'navigation_name'  => $navigationName,
            'navigation_title' => $navigationTitle !== 'navigation_title.' . $name ? $navigationTitle : $navigationName,
            'path'             => $this->router->generate('app_' . $name),
            'is_active'        => $request ? $request->get('_route') === 'app_' . $name : false,
            'role'             => $role,
            'hide_on_auth'     => $role === 'HIDE_ON_AUTH',
            'show_on_auth'     => $role === 'SHOW_ON_AUTH',
        ];
    }
}
