<?php

namespace App\Service;

use Symfony\Component\Translation\DataCollectorTranslator;
use Symfony\Contracts\Translation\TranslatorInterface;

class TranslatorService
{
    public const LOCALES = [
        'en',
        'de',
    ];

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

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
            . $this->translator->trans('title.index');
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
