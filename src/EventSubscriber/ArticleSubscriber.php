<?php

namespace App\EventSubscriber;

use App\Entity\Article;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class ArticleSubscriber implements EventSubscriber
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Article) {
            return;
        }

        $this->updateAuthor($entity);
    }

    private function updateAuthor(Article $article): void
    {
        if (empty($article->getAuthor()) && empty($article->getUser())) {
            $article->setAuthor('anonymous');
        }
    }
}