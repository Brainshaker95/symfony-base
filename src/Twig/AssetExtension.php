<?php

namespace App\Twig;

use App\Traits\HasAssetService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AssetExtension extends AbstractExtension
{
    use HasAssetService;

    /**
     * @return array<TwigFilter>
     */
    public function getFilters()
    {
        return [
            new TwigFilter('get_liip_preview_config', [$this, 'getLiipPreviewConfig']),
        ];
    }

    /**
     * @return array<mixed>
     */
    public function getLiipPreviewConfig(string $thumbnail)
    {
        return $this->assetService->getLiipPreviewConfig($thumbnail);
    }
}
