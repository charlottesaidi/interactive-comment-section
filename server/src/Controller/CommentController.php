<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Comment;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use App\Service\ApiQueryGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends AbstractController 
{
    private $queryGenerator;
    private $userRepo;
    private $commentRepo;

    public function __construct(ApiQueryGenerator $queryGenerator, UserRepository $userRepo, CommentRepository $commentRepo) 
    {
        $this->queryGenerator = $queryGenerator;
        $this->userRepo = $userRepo;
        $this->commentRepo = $commentRepo;
    }

    public function index(): Response
    {
        $response = new Response;

        try {
            $comments = $this->commentRepo->findAll();
    
            $jsonContent = $this->queryGenerator->handleCircularReference($comments);
    
            $response->setStatusCode(Response::HTTP_OK);
            $response->setContent($jsonContent);
    
            return $response;
        } catch(\Error $e) {
            return $this->json([
                'error' => [
                    'exception' => $e->getMessage(),                 
                    'message' => "Something went wrong."
                ]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getOne($id): Response 
    {
        $comment = $this->commentRepo->find($id);

        try {
            $jsonContent = $this->queryGenerator->handleCircularReference($comment);
    
            $response->setStatusCode(Response::HTTP_OK);
            $response->setContent($jsonContent);
    
            return $response;

        } catch(\Error $e) {
            return $this->json([
                'error' => [
                    'exception' => $e->getMessage(),                 
                    'message' => "Something went wrong."
                ]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function new(Request $request)
    {
        $response = new Response();
        $user = $this->getUser();  

        $data = json_decode($request->getContent(), true);
        
        try {
            $content = $data['content'];
            if(is_null($content) || empty($content)) {
                return $this->json([
                    'error' => [                
                        'message' => "Your feedback cannot be empty."
                    ]
                ]);
            } else {
                $comment = $this->queryGenerator->createComment($content, $user);
                $jsonContent = $this->queryGenerator->handleCircularReference($comment);

                $response->setStatusCode(Response::HTTP_CREATED);
                $response->setContent($jsonContent);
                
                return $response;
            }
        } catch(\Error $e) {
            return $this->json([
                'error' => [
                    'exception' => $e->getMessage(),                 
                    'message' => "Something went wrong."
                ]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete($id) {
        $response = new Response();
        $comment = $this->commentRepo->find($id);

        try {
            $this->queryGenerator->deleteElement($comment);

            $response->setStatusCode(Response::HTTP_OK);
            $response->setContent('Comment deleted');

            return $response;
        } catch(\Error $e) {
            return $this->json([
                'error' => [
                    'exception' => $e->getMessage(),                 
                    'message' => "Something went wrong."
                ]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function edit($id, Request $request): Response
    {
        $response = new Response;
        $comment = $this->commentRepo->find($id);

        $data = json_decode($request->getContent(), true);
        try {
            $content = $data['content'];
    
            $this->queryGenerator->update($comment, $content);
    
            $response->setStatusCode(Response::HTTP_CREATED);
            $response->headers->set('Content-Type', 'application/json');
    
            $response->setContent('Comment updated');
    
            return $response;

        } catch(\Error $e) {
            return $this->json([
                'error' => [
                    'exception' => $e->getMessage(),                 
                    'message' => "Something went wrong."
                ]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function vote($id, Request $request): Response
    {
        // récupérer le commentaire concerné
        // récupérer $request->getContent()
        // check si c'est un vote moins ou un vote plus
        // éditer le commentaire en mettant a jour son score (+1 ou -1)
        // pas de redirection ni de message, le js fait le reste
    }
}