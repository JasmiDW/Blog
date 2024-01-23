<?php

namespace App\EventSubscriber;

use App\Entity\Article;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;

class ArticleSubscriber implements EventSubscriberInterface
{
private $security;

public function __construct(Security $security)
{
$this->security = $security;
}

public function prePersist(LifecycleEventArgs $args): void
{
$entity = $args->getObject();

if ($entity instanceof Article) {
$entity->setAuthor($this->security->getUser()->getFirstName());
}
}

public static function getSubscribedEvents(): array
{
return [
Events::prePersist => 'prePersist',
];
}
}