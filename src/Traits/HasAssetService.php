<?php

namespace App\Traits;

use App\Service\AssetService;

trait HasAssetService
{
    /**
     * @var AssetService;
     */
    private $assetService;

    /**
     * @required
     */
    public function setAssetService(AssetService $assetService): void
    {
        $this->assetService = $assetService;
    }
}
