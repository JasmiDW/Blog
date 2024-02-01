<?php

namespace App\Security\Voter;

use App\Entity\Article;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ArticleVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';

    protected function supports($attribute, $subject): bool
    {
        if (!in_array($attribute, [self::VIEW, self::EDIT])) {
            return false;
        }

        if (!$subject instanceof Article) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Article $article */
        $article = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($article, $user);
            case self::EDIT:
                return $this->canEdit($article, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(Article $article, UserInterface $user)
    {
        // if they can edit, they can view
        if ($this->canEdit($article, $user)) {
            return true;
        }

        // the Article object could have, for example, a method isPublished()
        return $article->isPublished();
    }

    private function canEdit(Article $article, UserInterface $user)
    {
        // this assumes that the Article object has a `getUser()` method
        return $user === $article->getUser();
    }
}