<?php

namespace App\Twig;

use App\Traits\HasTranslatorService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TranslatorExtension extends AbstractExtension
{
    use HasTranslatorService;

    /**
     * @return array<TwigFilter>
     */
    public function getFilters()
    {
        return [
            new TwigFilter('role_name', [$this, 'getRoleName']),
        ];
    }

    /**
     * @return array<TwigFunction>
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('translations', [$this, 'getTranslations']),
            new TwigFunction('page_title', [$this, 'getPageTitle']),
        ];
    }

    /**
     * @return array<string>
     */
    public function getTranslations(string $key = null)
    {
        if ($key === 'js_globals') {
            $validators         = $this->translatorService->getTranslations('validators');
            $validationMessages = [];

            foreach ($validators as $key => $validator) {
                if (strpos($key, 'app.', 0) > -1) {
                    $validationMessages[substr($key, 4, strlen($key))] = $validator;
                }
            }

            return array_merge(
                $this->translatorService->getTranslations('js_globals'),
                $validationMessages,
            );
        }

        return $this->translatorService->getTranslations($key);
    }

    public function getPageTitle(string $key): string
    {
        return $this->translatorService->getPageTitle($key);
    }

    public function getRoleName(string $role): string
    {
        return $this->translatorService->getRoleName($role);
    }
}
