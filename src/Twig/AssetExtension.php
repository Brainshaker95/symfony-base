<?php

namespace App\Twig;

use App\Traits\HasAssetService;
use App\Traits\HasRequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AssetExtension extends AbstractExtension
{
    use HasAssetService;
    use HasRequestStack;

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
     * @return array<TwigFunction>
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('css', [$this, 'extractCssFromPath'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @return array<mixed>
     */
    public function getLiipPreviewConfig(string $thumbnail)
    {
        return $this->assetService->getLiipPreviewConfig($thumbnail);
    }

    public function extractCssFromPath(string $path, string $replacement): string
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request) {
            return '';
        }

        $css = @file_get_contents($request->getSchemeAndHttpHost() . $path);

        if (!$css) {
            $css = '';
        }

        $css = preg_replace('/\n/', '', $css) ?: '';
        $css = str_replace('placeholder', $replacement, $css) ?: '';

        return $css ? '<style>' . $css . '</style>' : '';
    }
}
