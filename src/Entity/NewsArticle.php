<?php

namespace App\Entity;

use App\Repository\NewsArticleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NewsArticleRepository::class)
 */
class NewsArticle extends AbstractEntity
{
    /**
     * @var string
     * @ORM\Column(type="string", unique=true)
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @var User|null
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }
}
