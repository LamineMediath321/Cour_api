<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PostRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ApiResource(operations: [
    new Get(normalizationContext: ['groups' => 'post:item']),
    new GetCollection(normalizationContext: ['groups' => 'post:list'])
])]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    // #[Groups('post:read')]
    #[Groups(['post:list', 'post:item'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['post:list', 'post:item'])]
    // #[Groups('post:read')]
    #[Assert\NotBlank([], "Le titre ne doit pas etre null")]
    #[Assert\Length(min: 3)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    // #[Groups('post:read')]
    #[Groups(['post:list', 'post:item'])]
    #[Assert\NotBlank([], "Le contenu ne doit pas etre null")]
    #[Assert\Length(min: 3)]
    private ?string $content = null;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Comment::class)]
    // #[Groups('post:read')]
    private Collection $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }
}
