<?php

namespace App\Service;


use Symfony\Component\Security\Core\Security;

class UserAccess
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function verifyUserId(int $id): bool
    {
        return $this->security->getUser()->getId() === $id;
    }
}