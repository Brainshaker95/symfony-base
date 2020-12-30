<?php

namespace App\Entity\Asset;

use App\Repository\VideoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VideoRepository::class)
 */
class Video extends AbstractAsset
{
    /**
     * @var Asset|null
     * @ORM\OneToOne(targetEntity=Asset::class, mappedBy="video", cascade={"persist", "remove"})
     */
    private $asset;

    public function getAsset(): ?Asset
    {
        return $this->asset;
    }

    public function setAsset(?Asset $asset): self
    {
        if ($asset === null && $this->asset !== null) {
            $this->asset->setVideo(null);
        }

        if ($asset !== null && $asset->getVideo() !== $this) {
            $asset->setVideo($this);
        }

        $this->asset = $asset;

        return $this;
    }
}
