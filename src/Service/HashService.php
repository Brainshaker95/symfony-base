<?php

namespace App\Service;

use Hashids\Hashids;

class HashService
{
    /**
     * @var Hashids;
     */
    private $hashids;

    public function __construct(string $hashidsSalt, int $hashidsPadding)
    {
        $this->hashids = new Hashids($hashidsSalt, $hashidsPadding);
    }

    /**
     * @param int|string $value
     */
    public function encode($value): string
    {
        return $this->hashids->encode($value);
    }

    public function decode(string $hash): ?string
    {
        $result = $this->hashids->decode($hash);

        return empty($result) ? null : $result[0];
    }
}
