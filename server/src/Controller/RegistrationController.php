<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

class RegistrationController extends AbstractController
{
    public function register(Request $request, UserPasswordHasherInterface $passwordEncoder, EntityManagerInterface $manager): Response
    {
        $data = json_decode($request->getContent(), true);

        try {
            if(is_null($data['username']) || empty($data['username']) || is_null($data['password']) || empty($data['password'])) {
                return $this->json([
                    'error' => 'These fields cannot be empty',
                ]);
            }
            if(is_null($data['confirm_password']) || empty($data['confirm_password'])) {
                return $this->json([
                    'error' => 'You must confirm your password',
                ]);
            } else {
                if($data['confirm_password'] !== $data['password']) {
                    return $this->json([
                        'error' => 'Passwords don\'t match',
                    ]);
                }
            }
            $user = new User();
            $user->setUsername($data['username'])
                ->setPassword(
                    $passwordEncoder->hashPassword(
                        $user,
                        $data['password']
                    )
                );

            $manager->persist($user);
            $manager->flush();

            return $this->json([
                'success' => 'You just signed up, welcome !',
            ]);

        } catch(\Error $e) {
            return $this->json([
                'error' => [
                    'exception' => $e->getMessage(),                 
                    'message' => "Something went wrong."
                ]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
