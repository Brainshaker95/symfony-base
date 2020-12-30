<?php

namespace App\Entity\Asset;

use App\Entity\AbstractEntity;
use App\Repository\AssetRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AssetRepository::class)
 */
class Asset extends AbstractEntity
{
    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @var Image|null
     * @ORM\OneToOne(targetEntity=Image::class, cascade={"persist", "remove"})
     */
    private $image;

    /**
     * @var Video|null
     * @ORM\OneToOne(targetEntity=Video::class, cascade={"persist", "remove"})
     */
    private $video;

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?AbstractAsset $image): self
    {
        if ($image instanceof Image) {
            $this->image = $image;

            $this->setType('image');
        }

        return $this;
    }

    public function getVideo(): ?Video
    {
        return $this->video;
    }

    public function setVideo(?AbstractAsset $video): self
    {
        if ($video instanceof Video) {
            $this->video = $video;

            $this->setType('video');
        }

        return $this;
    }

    public function getAsset(): ?AbstractAsset
    {
        if ($this->type === 'image') {
            return $this->getImage();
        }

        return $this->getVideo();
    }

    public function setAsset(?AbstractAsset $asset): self
    {
        if ($this->type === 'image') {
            $this->setImage($asset);
        } else {
            $this->setVideo($asset);
        }

        return $this;
    }
}
