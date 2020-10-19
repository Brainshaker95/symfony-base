<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Image
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Carbon
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var Carbon
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $filename;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $path;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $alt;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setTimestamps(): void
    {
        $now = Carbon::now();

        $this->updatedAt = $now;

        if (!$this->createdAt) {
            $this->createdAt = $now;
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCreatedAt(): Carbon
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): Carbon
    {
        return $this->updatedAt;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(string $alt): self
    {
        $this->alt = $alt;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
}
