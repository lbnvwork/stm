<?php
/**
 * Created by PhpStorm.
 * User: m-lobanov
 * Date: 03.06.19
 * Time: 14:32
 */

namespace Office\Helper;

use Auth\Entity\User;
use Doctrine\ORM\EntityManager;
use Office\Entity\LkCompany;

class IsPropertyCtoUser
{
    private $entityManager;


    /**
     * IsPropertyCtoUser constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param User $currUser
     * @param LkCompany $company
     *
     * @return bool
     */
    public function __invoke(User $currUser, LkCompany $company = null): bool
    {
        if (!$currUser->getUserRoleManager()->offsetExists('office_user')) {
            return false;
        }
        if ($company == null) {
            return false;
        }
        $ctoUsers = $currUser->getCustomer()->toArray();
        /** @var User $ctoUser */
        foreach ($ctoUsers as $ctoUser) {
            if (in_array($company, $ctoUser->getCompany()->toArray())) {
                return true;
            }
        }
        return false;
    }
}
