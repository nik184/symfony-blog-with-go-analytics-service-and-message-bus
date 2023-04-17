<?php

namespace App\EventListener;

use App\Entity\Post;
use App\Message\AnalyticsServiceMessage;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Messenger\MessageBusInterface;

class PostEntitySubscriber implements EventSubscriberInterface
{
    const CREATE_ACTION = 'create';
    const UPDATE_ACTION = 'update';
    const REMOVE_ACTION = 'remove';

    const ACTIONS = [
        self::CREATE_ACTION => self::CREATE_ACTION,
        self::UPDATE_ACTION => self::UPDATE_ACTION,
        self::REMOVE_ACTION => self::REMOVE_ACTION
    ];

    public function __construct(
        private readonly MessageBusInterface $bus
    )
    {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postRemove,
            Events::postUpdate,
        ];
    }

    public function postPersist(PostPersistEventArgs $args): void
    {
        $this->processEvent($args->getObject(), self::CREATE_ACTION);
    }

    public function postUpdate(PostUpdateEventArgs $args): void
    {
        $this->processEvent($args->getObject(), self::UPDATE_ACTION);
    }

    public function postRemove(PostRemoveEventArgs $args): void
    {
        $this->processEvent($args->getObject(), self::REMOVE_ACTION);
    }

    private function processEvent(mixed $entity, string $action): void
    {
        if (!$entity instanceof Post) {
            return;
        }

        if (!in_array($action, self::ACTIONS)) {
            return;
        }

        $this->sendMessage($entity, $action);
    }


    public function sendMessage(Post $post, string $action): void
    {
        $requestBody = [
            "authorId" => $post->getAuthor()->getId(),
            "postId" => $post->getId(),
            "postTitle" => $post->getTitle(),
            "action" => $action,
        ];

        $data = json_encode($requestBody);

        $this->bus->dispatch(new AnalyticsServiceMessage($data));
    }
}
