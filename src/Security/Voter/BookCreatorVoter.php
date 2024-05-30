<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Book;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use App\Entity\User;

class BookCreatorVoter extends Voter
{
   /**
    * @inheritDoc
    */
    protected function supports(string $attribute, mixed $subject): bool
    {
        // TODO : Implement supports() method
        return 'book.is_creator' === $attribute && $subject instanceof Book;
    } 

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        // TODO : Implement voteOnAttribute() method
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }
        /**
         * @var Book $subject
         */
        return $user === $subject->getCreatedBy();
    }
}
