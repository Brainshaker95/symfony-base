<?php

namespace App\Twig;

use App\Service\HashService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Yaml\Yaml;

class HelperExtension extends AbstractExtension
{
    /**
     * @var string
     */
    private $environment;

    /**
     * @var HashService
     */
    protected $hashService;

    public function __construct(KernelInterface $kernel, HashService $hashService)
    {
        $this->environment = $kernel->getEnvironment();
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
            new TwigFilter('strip_spaces', [$this, 'stripSpaces'], ['is_safe' => ['html']]),
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

    public function stripSpaces (string $html): string
    {
        if ($this->environment === 'dev') {
            return $html;
        }

        $html = preg_replace('/\s\s/', '', $html);

        if (!$html) {
            $html = '';
        }

        return $html;
    }
}
