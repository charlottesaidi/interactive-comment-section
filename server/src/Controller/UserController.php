<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\ApiQueryGenerator;
use Symfony\Component\HttpFoundation\Response;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserController extends AbstractController 
{
    private $jwtManager;
    private $tokenStorageInterface;

    public function __construct(TokenStorageInterface $tokenStorageInterface, JWTTokenManagerInterface $jwtManager)
    {
        $this->jwtManager = $jwtManager;
        $this->tokenStorageInterface = $tokenStorageInterface;
    }

    public function index(UserRepository $userRepo, ApiQueryGenerator $queryGenerator): Response
    {
        $response = new Response;
        $user = $this->getUser();

        try {
            if(null == $user) {
                $response->setStatusCode(Response::HTTP_NOT_FOUND);
                $response->setContent('Not found');
            } else {
                $jsonContent = $queryGenerator->handleCircularReference($user);
        
                $response->setStatusCode(Response::HTTP_OK);
                $response->setContent($jsonContent);
            }
        } catch(\Error $e) {
            $response->setContent($e->getMessage());
        }
        return $response;
    }

    public function getToken(ApiQueryGenerator $queryGenerator): Response 
    {
        $response = new Response;
        $user = $this->getUser();

        try {
            if(null == $user) {
                $response->setStatusCode(Response::HTTP_NOT_FOUND);
                $response->setContent('Not found');

                return $response;
            } else {
                $jsonContent = $queryGenerator->handleCircularReference($user);
                $decodedJwtToken = $this->jwtManager->decode($this->tokenStorageInterface->getToken());
                return $this->json([$decodedJwtToken, json_decode($jsonContent)]);
            }
        } catch(\Error $e) {
            return $response->setContent($e->getMessage());
        }
    }
}