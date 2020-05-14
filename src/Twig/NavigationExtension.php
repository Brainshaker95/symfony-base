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
     * @var array<string, array>
     */
    protected const ROUTES = [
        'main' => [
            'index',
            [
                'name' => 'profile',
                'role' => 'ROLE_USER',
            ],
            [
                'name' => 'admin',
                'role' => 'ROLE_ADMIN',
            ],
        ],
        'secondary' => [
            [
                'name' => 'login',
                'role' => 'HIDE_ON_AUTH',
            ],
            [
                'name' => 'logout',
                'role' => 'ROLE_USER',
            ],
            [
                'name' => 'register',
                'role' => 'HIDE_ON_AUTH',
            ],
        ],
    ];

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
     * @return array<int, array{navigation_name: string, path: string, is_active: boolean, role: string|null, hide_on_auth: boolean}>
     */
    public function getNavigation(string $type = 'main')
    {
        $request    = $this->requestStack->getCurrentRequest();
        $routes     = isset(self::ROUTES[$type]) ? self::ROUTES[$type] : [];
        $navigation = [];

        foreach ($routes as $route) {
            $role = null;

            if (is_array($route)) {
                $name = $route['name'];
                $role = $route['role'];
            } else {
                $name = $route;
            }

            $navigation[] = [
                'navigation_name' => $this->translator->trans('navigation_name.' . $name),
                'path'            => $this->router->generate('app_' . $name),
                'is_active'       => $request ? $request->get('_route') === 'app_' . $name : false,
                'role'            => $role,
                'hide_on_auth'    => $role === 'HIDE_ON_AUTH',
            ];
        }

        return $navigation;
    }
}
