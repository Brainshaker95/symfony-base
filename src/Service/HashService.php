<?php

namespace App\Service;

use Hashids\Hashids;

class HashService
{
    /**
     * @var Hashids;
     */
    protected $hasher;

    public function __construct(string $hashidsSalt, int $hashidsPadding)
    {
        $this->hasher = new Hashids($hashidsSalt, $hashidsPadding);
    }

    /**
     * @param int|string $value
     */
    public function encode($value): string
    {
        return $this->hasher->encode($value);
    }

    public function decode(string $hash): ?string
    {
        $result = $this->hasher->decode($hash);

        return empty($result) ? null : $result[0];
    }
}
