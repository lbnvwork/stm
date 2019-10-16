<?php
/**
 * Created by PhpStorm.
 * User: m-lobanov
 * Date: 06.06.19
 * Time: 13:01
 */

namespace Cms\Repository;

use Auth\Entity\User;
use Doctrine\ORM\EntityRepository;

class CmsRepository extends EntityRepository
{
    public function checkEmailDuplication(string $email, int $userId = null)
    {
        if ($userId == null) {
            if ($this->getEntityManager()->getRepository(User::class)->count(
                [
                    'email' => $email,
                ]
            )) {
                return false;
            }
        } else {
            /** @var User $user */
            $user = $this->getEntityManager()->find(User::class, $userId);
            if ($this->getEntityManager()->getRepository(User::class)->findOneBy(
                [
                        'email' => $email,
                    ]
            )
                && $email != $user->getEmail()) {
                return false;
            }
        }
        return true;
    }
}
