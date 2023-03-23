<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CommentRepository;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('post:read')]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[Groups('post:read')]
    private ?User $user_comment = null;

    #[ORM\ManyToOne(inversedBy: 'comments', cascade: ['remove'])]
    #[ORM\JoinColumn(onDelete: "CASCADE")]
    private ?Post $post = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups('post:read')]
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
