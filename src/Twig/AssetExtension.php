<?php

namespace App\Twig;

use Exception;
use Symfony\Component\Yaml\Yaml;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AssetExtension extends AbstractExtension
{
    /**
     * @return array<TwigFilter>
     */
    public function getFilters()
    {
        return [
            new TwigFilter('get_liip_config', [$this, 'getLiipConfig']),
        ];
    }

    /**
     * @return array<mixed>
     */
    public function getLiipConfig(string $thumbnail)
    {
        try {
            $liipConfig = file_get_contents(__DIR__ . '..\\..\\..\\config\\packages\\liip_imagine.yml');

            if (!$liipConfig) {
                return [];
            }

            $config     = Yaml::parse($liipConfig);
            $filterSets = $config['liip_imagine']['filter_sets'];

            if ($filterSets[$thumbnail] && isset($filterSets[$thumbnail]['filters'])) {
                $filters = $filterSets[$thumbnail]['filters'];

                if ($filters && $filters['thumbnail']) {
                    $thumbnailConfig = $filters['thumbnail'];

                    return ['thumbnail' => array_merge($thumbnailConfig, [
                        'size' => [
                            $thumbnailConfig['size'][0] / 10,
                            $thumbnailConfig['size'][1] / 10,
                        ],
                    ])];
                }
            }
        } catch (Exception $e) {
            throw new Exception($e);
        }

        return [];
    }
}
