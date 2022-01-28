<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Reply;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public $oneMonth;
    public $twoWeeks;
    public $oneWeek;
    public $threeDays;

    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher) {
        $this->passwordHasher = $passwordHasher;
        $this->oneMonth = new \DateTime('2021-12-15');
        $this->twoWeeks = new \DateTime('2022-01-01');
        $this->oneWeek = new \DateTime('2022-01-07');
        $this->threeDays = new \DateTime('2022-01-13');
    }

    public function load(ObjectManager $manager): void
    {
        // Users
        $juliusomo = new User();
        $juliusomo->setUsername('juliusomo')
            ->setImage('image-juliusomo.webp')
            ->setPassword($this->passwordHasher->hashPassword($juliusomo, 'azerty258'));
        $manager->persist($juliusomo);

        $amyrobson = new User();
        $amyrobson->setUsername('amyrobson')
            ->setImage('image-amyrobson.webp')
            ->setPassword($this->passwordHasher->hashPassword($amyrobson, 'azerty258'));
        $manager->persist($amyrobson);

        $maxblagun = new User();
        $maxblagun->setUsername('maxblagun')
            ->setImage('image-maxblagun.webp')
            ->setPassword($this->passwordHasher->hashPassword($maxblagun, 'azerty258'));
        $manager->persist($maxblagun);

        $ramsesmiron = new User();
        $ramsesmiron->setUsername('ramsesmiron')
            ->setImage('image-ramsesmiron.webp')
            ->setPassword($this->passwordHasher->hashPassword($ramsesmiron, 'azerty258'));
        $manager->persist($ramsesmiron);

        // Their comments
        $comment1 = new Comment;
        $comment1->setContent("Impressive! Though it seems the drag feature could be improved. But overall it looks incredible. You've nailed the design and the responsiveness at various breakpoints works really well.")
            ->setCreatedAt($this->oneMonth)
            ->setScore(12)
            ->setUser($amyrobson);
        $manager->persist($comment1);

        $comment2 = new Comment;
        $comment2->setContent("Woah, your project looks awesome! How long have you been coding for? I'm still new, but think I want to dive into React as well soon. Perhaps you can give me an insight on where I can learn React? Thanks!")
            ->setCreatedAt($this->twoWeeks)
            ->setScore(5)
            ->setUser($maxblagun);
        $manager->persist($comment2);

        // Their replies
        $reply1 = new Reply;
        $reply1->setContent("If you're still new, I'd recommend focusing on the fundamentals of HTML, CSS, and JS before considering React. It's very tempting to jump ahead but lay a solid foundation first.")
            ->setCreatedAt($this->oneWeek)
            ->setScore(4)
            ->setUser($ramsesmiron)
            ->setReplyingTo($maxblagun->getUsername())
            ->setComment($comment2);
        $manager->persist($reply1);

        $reply2 = new Reply;
        $reply2->setContent("I couldn't agree more with this. Everything moves so fast and it always seems like everyone knows the newest library/framework. But the fundamentals are what stay constant.")
            ->setCreatedAt($this->threeDays)
            ->setScore(5)
            ->setUser($juliusomo)
            ->setReplyingTo($ramsesmiron->getUsername())
            ->setComment($comment2);
        $manager->persist($reply2);

        $manager->flush();
    }
}
