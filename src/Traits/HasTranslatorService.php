<?php

namespace App\Traits;

use App\Service\TranslatorService;

trait HasTranslatorService
{
    /**
     * @var TranslatorService;
     */
    private $translatorService;

    /**
     * @required
     */
    public function setTranslatorService(TranslatorService $translatorService): void
    {
        $this->translatorService = $translatorService;
    }
}
