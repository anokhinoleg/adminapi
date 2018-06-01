<?php
/**
 * Created by PhpStorm.
 * User: developer
 * Date: 04.04.18
 * Time: 12:01
 */

namespace App\Service;

use FOS\UserBundle\Model\UserManagerInterface;

class FosUserRegister
{
    private $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    public function generateForUserFromType($type, $name, $email, $password)
    {
        $user = $this->userManager->createUser();
        $user->setUsername($name);
        $user->setEmail($email);
        $user->setPlainPassword($password);
        if ($type === 'customer') {
            $user->setRoles([
                'ROLE_CUSTOMER',
                'ROLE_ADMIN_CUSTOMER_VIEW',
                'ROLE_ADMIN_CUSTOMER_LIST',
                'ROLE_ADMIN_SERVICE_VIEW',
                'ROLE_ADMIN_SERVICE_LIST'
            ]);
        } elseif ($type === 'reseller') {
            $user->setRoles([
                'ROLE_RESELLER',
                'ROLE_ADMIN_RESELLER_VIEW',
                'ROLE_ADMIN_RESELLER_LIST',
                'ROLE_ADMIN_SERVICE_VIEW',
                'ROLE_ADMIN_SERVICE_LIST',
                'ROLE_ADMIN_CUSTOMER_VIEW',
                'ROLE_ADMIN_CUSTOMER_LIST'
            ]);
        }
        $this->userManager->updateUser($user);
        return $user;
    }
}