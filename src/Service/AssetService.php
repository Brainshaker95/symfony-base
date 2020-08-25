<?php

namespace App\Service;

use Exception;
use Liip\ImagineBundle\Service\FilterService;
use Symfony\Component\Yaml\Yaml;

class AssetService
{
    /**
     * @var FilterService
     */
    protected $filterService;

    public function __construct(FilterService $filterService)
    {
        $this->filterService = $filterService;
    }

    /**
     * @throws Exception
     *
     * @return array<mixed>
     */
    public function getLiipPreviewConfig(string $thumbnail)
    {
        try {
            $liipConfig = file_get_contents(__DIR__ . '..\\..\\..\\config\\packages\\liip_imagine.yml');

            if (!$liipConfig) {
                return [];
            }

            $config     = Yaml::parse($liipConfig);
            $filterSets = $config['liip_imagine']['filter_sets'];

            if (isset($filterSets[$thumbnail]) && isset($filterSets[$thumbnail]['filters'])) {
                $filters = $filterSets[$thumbnail]['filters'];

                if (isset($filters['thumbnail'])) {
                    $thumbnailConfig = $filters['thumbnail'];

                    return [
                        'thumbnail' => array_merge($thumbnailConfig, [
                            'size' => [
                                $thumbnailConfig['size'][0] / 10,
                                $thumbnailConfig['size'][1] / 10,
                            ],
                        ]),
                    ];
                }
            }
        } catch (Exception $e) {
            throw new Exception($e);
        }

        return [];
    }

    public function getThumbnailSrc(string $path, string $thumbnail): string
    {
        return $this->filterService->getUrlOfFilteredImage($path, $thumbnail);
    }

    public function getPreviewSrc(string $path, string $thumbnail): string
    {
        return $this->filterService->getUrlOfFilteredImageWithRuntimeFilters(
            $path,
            $thumbnail,
            $this->getLiipPreviewConfig($thumbnail)
        );
    }
}
