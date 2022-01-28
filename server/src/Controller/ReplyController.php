<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Comment;
use App\Repository\CommentRepository;
use App\Entity\Reply;
use App\Repository\ReplyRepository;
use App\Repository\UserRepository;
use App\Service\ApiQueryGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ReplyController extends AbstractController 
{
    private $queryGenerator;
    private $userRepo;
    private $commentRepo;
    private $replyRepo;

    public function __construct(ApiQueryGenerator $queryGenerator, UserRepository $userRepo, CommentRepository $commentRepo, ReplyRepository $replyRepo) 
    {
        $this->queryGenerator = $queryGenerator;
        $this->userRepo = $userRepo;
        $this->commentRepo = $commentRepo;
        $this->replyRepo = $replyRepo;
    }

    public function index(): Response
    {
        $response = new Response;

        try {
            $replies = $this->replyRepo->findAll();
    
            $jsonContent = $this->queryGenerator->handleCircularReference($replies);
    
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
        $reply = $this->replyRepo->find($id);
        try {
            $jsonContent = $this->queryGenerator->handleCircularReference($reply);
    
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

    public function new(Request $request): Response 
    {
        $user = $this->getUser();  

        $response = new Response();
        $data = json_decode($request->getContent(), true);
        
        try {
            $commentId = $data['commentId'];
            $content = $data['content'];
            $replyingTo = $data['replyingto'];
            if(is_null($content) || empty($content)) {
                return $this->json([
                    'error' => [                 
                        'message' => "Your feedback cannot be empty."
                    ]
                ]);
            } else {
                $comment = $this->commentRepo->find($commentId);
        
                $this->queryGenerator->createReply($content, $user, $comment, $replyingTo);
        
                $response->setStatusCode(Response::HTTP_CREATED);
                $response->headers->set('Content-Type', 'application/json');
        
                $response->setContent('Thank you for the feedback !');
        
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

    public function delete($id) 
    {
        $response = new Response();
        $reply = $this->replyRepo->find($id);

        try {
            $this->queryGenerator->deleteElement($reply);

            $response->setStatusCode(Response::HTTP_OK);
            $response->setContent('Reply deleted');

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
        $reply = $this->replyRepo->find($id);

        $data = json_decode($request->getContent(), true);
        try {
            $content = $data['content'];
    
            $this->queryGenerator->update($reply, $content);
    
            $response->setStatusCode(Response::HTTP_CREATED);
            $response->headers->set('Content-Type', 'application/json');
    
            $response->setContent('Reply updated');
    
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