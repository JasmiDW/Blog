<?php

namespace App\Security\Voter;


use App\Entity\Article;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ArticleVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool{
        return $subject instanceof Article && in_array($attribute, ['view', 'edit_article']);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool{
        if('view' == $attribute && $subject instanceof Article && $subject->isPublished()){
            return true;
        }

        $user = $token->getUser();

        $owner = $subject->getUserId();

        if('edit' == $attribute && ($owner instanceof User) && ($user instanceof User ) && $user->getId() == $owner->getId()){
            return true;
        }

        return false;
    }

}