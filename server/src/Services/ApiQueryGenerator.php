<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Comment;
use App\Entity\Reply;
use App\Repository\ReplyRepository;
use App\Repository\CommentRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiQueryGenerator 
{
    private $manager;
    private $client;
    
    public function __construct(EntityManagerInterface $manager, HttpClientInterface $client)
    {
        $this->manager = $manager;
        $this->client = $client;
    }   

    /**
     * Method handleCircularReference [convert an object to json handling endless loop between entities when related]
     * 
     * @param $data [objet concerné passé en paramètre pour la manipulation]
     *
     * @return mixed
     */
    public function handleCircularReference($data) {
        $encoder = new JsonEncoder();
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
        ];
        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, $defaultContext);

        $serializer = new Serializer([$normalizer], [$encoder]);

        return $serializer->serialize($data, 'json');
    }
    
    /**
     * Method createReply [create a reply. Its parameters are the entity's attributes]
     * 
     * @return void
     */
    public function createReply(String $content, User $user, Comment $comment, String $replyingTo) {
        $reply = new Reply();
        $reply->setContent($content)
            ->setUser($user)
            ->setReplyingTo($replyingTo)
            ->setComment($comment)
            ->setScore(0)
            ->setCreatedAt(new \DateTime);
        $this->manager->persist($reply);

        $this->manager->flush();

        return $reply;
    }
    
    /**
     * Method createComment [create a comment. Its parameters are the entity's attributes]
     *
     * @return void
     */
    public function createComment(String $content, User $user) {
        $comment = new Comment();
        $comment->setContent($content)
            ->setUser($user)
            ->setScore(0)
            ->setCreatedAt(new \DateTime);
        $this->manager->persist($comment);

        $this->manager->flush();

        return $comment;
    }

    /**
     * Method deleteElement [delete an object. Parameter is the entity to be deleted]
     * 
     * @return void
     */
    public function deleteElement($object) 
    {
        $this->manager->remove($object);
        $this->manager->flush();
    }

    /**
     * Method update [edit an object. Parameters are the entity and its content]
     * 
     * @return void
     */
    public function update($object, String $content) 
    {
        $object->setContent($content);
        $this->manager->flush();
    }
}