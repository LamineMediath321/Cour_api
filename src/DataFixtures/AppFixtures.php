<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use Faker\Factory;
use Faker\Generator;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;
    private Generator $faker;


    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        $users = [];
        for ($i = 0; $i < 2; $i++) {
            $users[$i] = new User;
            $users[$i]->setEmail($this->faker->email());
            $password = $this->hasher->hashPassword($users[$i], 'pass_1234');
            $users[$i]->setPassword($password);
            $manager->persist(($users[$i]));
        }
        for ($i = 0; $i < 2; $i++) {
            $post = new Post;
            $post->setTitle($this->faker->text());
            $post->setContent($this->faker->sentence());
            $manager->persist($post);
            for ($i = 0; $i < 2; $i++) {
                $comment = new Comment;
                $comment->setContent($this->faker->sentence());
                $comment->setUserComment($users[$i]);
                $comment->setPost($post);
                $manager->persist($comment);
            }
        }

        $manager->flush();
    }
}
