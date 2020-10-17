<?php

namespace App\Traits;

use Symfony\Contracts\Translation\TranslatorInterface;

trait HasTranslator
{
    /**
     * @var TranslatorInterface;
     */
    private $translator;

    /**
     * @required
     */
    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }
}
