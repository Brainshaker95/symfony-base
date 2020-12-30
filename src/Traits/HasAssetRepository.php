<?php

namespace App\Traits;

use App\Repository\AssetRepository;

trait HasAssetRepository
{
    /**
     * @var AssetRepository;
     */
    private $assetRepository;

    /**
     * @required
     */
    public function setAssetRepository(AssetRepository $assetRepository): void
    {
        $this->assetRepository = $assetRepository;
    }
}
