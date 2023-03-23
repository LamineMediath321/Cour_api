<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->json($userRepository->findAll(), 200, [], ['groups' => 'post:read']);
    }

    #[Route('/new', name: 'app_user_new', methods: ['POST'])]
    public function new(
        Request $request,
        UserRepository $userRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $hasher
    ): Response {
        try {
            $user = $serializer->deserialize($request->getContent(), User::class, 'json');

            $errors = $validator->validate($user);
            if (count($errors) > 0) {
                return $this->json($errors, 400);
            }
            $password = $hasher->hashPassword($user, $user->getPassword());
            $user->setPassword($password);
            $userRepository->save($user, true);
            return $this->json($user, 201, [], ['groups' => 'post:read']);
        } catch (NotEncodableValueException $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    #[Route('/{id<\d+>}', name: 'app_user_show', methods: ['GET'])]
    public function show($id, UserRepository $userRepository): Response
    {
        $user = $userRepository->findOneBy(['id' => $id]);
        if ($user) {
            return $this->json($user, 200, [], ['groups' => 'post:read']);
        } else {
            return $this->json(null);
        }
    }

    #[Route('/{id<\d+>}/edit', name: 'app_user_edit', methods: ['PUT'])]
    public function edit(
        $id,
        Request $request,
        UserRepository $userRepository,
        SerializerInterface $serializer,
        UserPasswordHasherInterface $hasher
    ): Response {
        try {
            $user = $serializer->deserialize($request->getContent(), User::class, 'json');

            // $errors = $validator->validate($user);
            // $countErrors = count($errors);

            $userUpdate = $userRepository->findOneBy(["id" => $id]);
            if ($userUpdate) {
                if ($user->getEmail()) {

                    $userUpdate->setEmail($user->getEmail());
                }
                if ($user->getPassword()) {

                    $password = $hasher->hashPassword($user, $user->getPassword());
                    $userUpdate->setPassword($password);
                }
                $userRepository->save($userUpdate, true);
                return $this->json($userUpdate, 201, [], ['groups' => 'post:read']);
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

    #[Route('/{id<\d+>}', name: 'app_user_delete', methods: ['DELETE'])]
    public function delete($id, UserRepository $userRepository): Response
    {
        $user = $userRepository->findOneBy(['id' => $id]);
        if ($user) {
            $userRepository->remove($user, true);
            return $this->json([
                'status' => 200,
                "message" => 'Votre compte a ete bien supprimé',
            ]);
        } else {
            return $this->json(null);
        }
    }
}
