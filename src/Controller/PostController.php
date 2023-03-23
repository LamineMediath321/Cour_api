<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/post')]
class PostController extends AbstractController
{
    #[Route('/', name: 'app_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository): Response
    {
        return $this->json(
            $postRepository->findAll(),
            200,
            [],
            ['groups' => 'post:read']
        );
    }

    #[Route('/new', name: 'app_post_new', methods: ['POST'])]
    public function new(
        Request $request,
        PostRepository $postRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): Response {
        try {
            $post = $serializer->deserialize($request->getContent(), Post::class, 'json');

            $errors = $validator->validate($post);
            if (count($errors) > 0) {
                return $this->json($errors, 400);
            }

            $postRepository->save($post, true);

            return $this->json($post, 201, [], ['groups' => 'post:read']);
        } catch (NotEncodableValueException $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    #[Route('/{id<\d+>}', name: 'app_post_show', methods: ['GET'])]
    public function show($id, PostRepository $postRepository): Response
    {
        $post = $postRepository->findOneBy(["id" => $id]);
        if ($post) {
            return $this->json($post, 200, [], ['groups' => 'post:read']);
        } else {
            return $this->json(null);
        }
    }

    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['PUT'])]
    public function edit(
        Request $request,
        $id,
        PostRepository $postRepository,
        ValidatorInterface $validator,
        SerializerInterface $serializer
    ): Response {
        try {
            $post = $serializer->deserialize($request->getContent(), Post::class, 'json');

            // $errors = $validator->validate($post);
            // if (count($errors) > 0) {
            //     return $this->json($errors, 400);
            // }
            $postUpdate = $postRepository->findOneBy(["id" => $id]);
            if ($postUpdate) {
                if ($post->getTitle()) {
                    $postUpdate->setTitle($post->getTitle());
                }
                if ($post->getContent()) {
                    $postUpdate->setContent($post->getContent());
                }
                $postRepository->save($postUpdate, true);
                return $this->json($postUpdate, 201, [], ['groups' => 'post:read']);
            } else {
                return $this->json(null);
            }
        } catch (NotEncodableValueException $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    #[Route('/{id<\d+>}', name: 'app_post_delete', methods: ['DELETE'])]
    public function delete($id, PostRepository $postRepository): Response
    {
        $post = $postRepository->findOneBy(['id' => $id]);
        if ($post) {
            $postRepository->remove($post, true);
            return $this->json([
                'status' => 200,
                'message' => "Le post a ete bien supprime"
            ]);
        } else {
            return $this->json(null);
        }
    }
}
