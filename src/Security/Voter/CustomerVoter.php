<?php
/**
 * Created by PhpStorm.
 * User: developer
 * Date: 18.05.18
 * Time: 12:58
 */

namespace App\Security\Voter;

use App\Entity\Customer;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CustomerVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, ['ROLE_ADMIN_CUSTOMER_EDIT', 'ROLE_ADMIN_CUSTOMER_DELETE'])) {
            return false;
        }

        if (!$subject instanceof Customer) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        foreach ($token->getRoles() as $role) {
            if (in_array($role->getRole(), ['ROLE_ADMIN'])) {
                return true;
            }
        }
        if ($subject->getReseller() == null) {
            return false;
        }

        /** @var $subject Customer */
        return $subject->getReseller()->getName() === $token->getUsername();
    }
}