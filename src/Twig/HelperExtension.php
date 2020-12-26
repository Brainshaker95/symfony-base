<?php

namespace App\Twig;

use App\Traits\HasHashService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

class HelperExtension extends AbstractExtension
{
    use HasHashService;

    /**
     * @var string
     */
    private $environment;

    public function __construct(string $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @return array<TwigFilter>
     */
    public function getFilters()
    {
        return [
            new TwigFilter('encode', [$this, 'encode']),
            new TwigFilter('decode', [$this, 'decode']),
            new TwigFilter('strip_spaces', [$this, 'stripSpaces'], ['is_safe' => ['html']]),
            new TwigFilter('unserialize', [$this, 'unserialize']),
        ];
    }

    /**
     * @return array<TwigFunction>
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('uuid', 'uniqid'),
        ];
    }

    /**
     * @return array<TwigTest>
     */
    public function getTests()
    {
        return [
            new TwigTest('instanceof', [$this, 'instanceof']),
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

    public function stripSpaces(string $html): string
    {
        if ($this->environment === 'dev') {
            return $html;
        }

        return preg_replace('/\s\s/', '', $html) ?: '';
    }

    /**
     * @param array<mixed> $options
     *
     * @return array<mixed>
     */
    public function unserialize(string $variable, array $options = []): array
    {
        return unserialize($variable, $options);
    }
}
