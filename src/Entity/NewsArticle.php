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
}
