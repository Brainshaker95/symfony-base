<?php

namespace App\Service;

use Symfony\Contracts\Translation\TranslatorInterface;

class TranslatorService
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function exampleTODO(string $key): string
    {
        return $key;
    }
}
