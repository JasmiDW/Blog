<?php

namespace App\Security\Voter;

use App\Entity\Article;
use App\Entity\User;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ArticleVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {

        return $subject instanceof Article && in_array($attribute, ['edit', 'view']);
    }

    protected function voteOnAttribute(string $attribute, $article, TokenInterface $token): bool {

        //Seuls les articles publiés doivent pouvoir être consultés
        if ('view' === $attribute && $article instanceof Article && $article->isPublished()) {
            return true;
        }

        //Un article ne peut être édité que par celui qui l’a créé
        $userId = $token->getUser()->getId();
        $owner = $article->getUser();

        if('edit' === $attribute && ($owner instanceof User) && $userId === $owner->getId()) {
            return true;
        }
        return false;
    }


}