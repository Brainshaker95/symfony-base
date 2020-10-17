<?php

namespace App\Service;

use App\Traits\HasTranslator;
use Symfony\Component\Translation\DataCollectorTranslator;

class TranslatorService
{
    use HasTranslator;

    public const LOCALES = [
        'en',
        'de',
    ];

    /**
     * @return array<string>
     */
    public function getTranslations(string $key = null)
    {
        if (!$this->translator instanceof DataCollectorTranslator) {
            return [];
        }

        $catalogue = $this->translator->getCatalogue();

        return $key ? $catalogue->all($key) : $catalogue->all();
    }

    public function getPageTitle(string $key): string
    {
        return $this->translator->trans($key)
            . $this->translator->trans('title.separator')
            . $this->translator->trans('title.suffix');
    }

    public function getRoleName(string $role): string
    {
        $role = preg_replace(['/ROLE/', '/_/'], ['', ''], $role);

        if (!$role) {
            $role = 'inactive';
        }

        return $this->translator->trans('role.' . strtolower($role));
    }
}
