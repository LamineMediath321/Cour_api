<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\CommentRepository;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ApiResource(operations: [
    new Get(normalizationContext: ['groups' => 'comment:item']),
    new GetCollection(normalizationContext: ['groups' => 'comment:list'])
])]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    // #[Groups('post:read')]
    #[Groups(['comment:list', 'comment:item'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    // #[Groups('post:read')]
    #[Groups(['comment:list', 'comment:item'])]
    private ?User $user_comment = null;

    #[ORM\ManyToOne(inversedBy: 'comments', cascade: ['remove'])]
    #[Groups(['comment:list', 'comment:item'])]
    private ?Post $post = null;

    #[ORM\Column(type: Types::TEXT)]
    // #[Groups('post:read')]
    #[Groups(['comment:list', 'comment:item'])]
    private ?string $content = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserComment(): ?User
    {
        return $this->user_comment;
    }

    public function setUserComment(?User $user_comment): self
    {
        $this->user_comment = $user_comment;

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }
}
