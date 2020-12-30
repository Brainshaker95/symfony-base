<?php

namespace App\Entity\Asset;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 */
class Image extends AbstractAsset
{
    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $alt;

    /**
     * @var Asset|null
     * @ORM\OneToOne(targetEntity=Asset::class, mappedBy="image", cascade={"persist", "remove"})
     */
    private $asset;

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(string $alt): self
    {
        $this->alt = $alt;

        return $this;
    }

    public function getAsset(): ?Asset
    {
        return $this->asset;
    }

    public function setAsset(?Asset $asset): self
    {
        if ($asset === null && $this->asset !== null) {
            $this->asset->setImage(null);
        }

        if ($asset !== null && $asset->getImage() !== $this) {
            $asset->setImage($this);
        }

        $this->asset = $asset;

        return $this;
    }
}
