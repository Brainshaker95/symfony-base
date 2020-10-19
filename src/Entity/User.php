<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class User implements UserInterface
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
     * @ORM\Column(type="string", unique=true)
     */
    private $username;

    /**
     * @var array<string>
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $theme;

    /**
     * @var Image|null
     * @ORM\OneToOne(targetEntity=Image::class, cascade={"persist", "remove"})
     */
    private $image;

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

    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getRoles(): array
    {
        return array_unique($this->roles);
    }

    /**
     * @param array<string> $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getTheme(): string
    {
        return (string) $this->theme;
    }

    public function setTheme(string $theme): self
    {
        $this->theme = $theme;

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials(): void
    {
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addConstraint(new UniqueEntity([
            'fields'  => 'username',
            'message' => 'app.error.form.username.unique',
        ]));
    }
}
