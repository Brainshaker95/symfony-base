<?php

namespace App\Traits;

use Symfony\Component\String\Slugger\SluggerInterface;

trait HasSlugger
{
    /**
     * @var SluggerInterface;
     */
    private $slugger;

    /**
     * @required
     */
    public function setSlugger(SluggerInterface $slugger): void
    {
        $this->slugger = $slugger;
    }
}
