<?php

namespace App\Twig;

use App\Service\HashService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class HelperExtension extends AbstractExtension
{
    /**
     * @var HashService
     */
    protected $hashService;

    public function __construct(HashService $hashService)
    {
        $this->hashService = $hashService;
    }

    /**
     * @return array<TwigFilter>
     */
    public function getFilters()
    {
        return [
            new TwigFilter('encode', [$this, 'encode']),
            new TwigFilter('decode', [$this, 'decode']),
            new TwigFilter('instanceof', [$this, 'instanceof']),
        ];
    }

    public function encode(string $string): string
    {
        return $this->hashService->encode($string);
    }

    public function decode(string $hash): ?string
    {
        return $this->hashService->decode($hash);
    }

    /**
     * @param mixed $variable
     */
    public function instanceof($variable, string $class): bool
    {
        return $variable instanceof $class;
    }
}
