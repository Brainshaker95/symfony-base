<?php

namespace App\Entity;

use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class AbstractEntity
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
     * @ORM\PrePersist
     * @ORM\PreUpdate
    */
    public function updateTimestamps(): void
    {
        $now = Carbon::now();

        $this->updatedAt = $now;

        if (!$this->createdAt instanceof Carbon) {
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
}
