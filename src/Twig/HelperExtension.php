<?php

namespace App\Twig;

use App\Service\TranslatorService;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class HelperExtension extends AbstractExtension
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return array<TwigFunction>
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('page_title', [$this, 'getPageTitle']),
        ];
    }

    public function getPageTitle(string $key): string
    {

        return $this->translator->trans($key)
            . $this->translator->trans('title.separator')
            . $this->translator->trans('title.index');
    }
}
