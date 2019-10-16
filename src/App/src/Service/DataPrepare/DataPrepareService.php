<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 27.05.19
 * Time: 11:09
 */

namespace App\Service\DataPrepare;

use Auth\Entity\User;
use Auth\Entity\UserHasRole;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Permission\Entity\Role;

/**
 * Class DataPrepareService
 * Подготовка данных к отправке в БД... или в шаблон
 *
 * @package App\Service\DataPrepare
 */
class DataPrepareService
{
    protected $entityManager;

    /**
     * DataPrepareService constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Формирование массива с пользователями ("спасибо" автотестам)
     *
     * @param array $queryParams
     * @param string $url
     * @param QueryBuilder $usersQueryBuilder
     * @param int $countItems
     *
     * @return array
     */
    public function getUsers(array $queryParams, string $url, QueryBuilder $usersQueryBuilder, int $countItems = 20)
    {
        $prepareData = [];

        $sortType = $queryParams['sort'] ?? 'id';
        $order = $queryParams['order'] ?? 'ASC';
        $orderCheck = in_array(
            $order,
            [
                'ASC',
                'DESC'
            ]
        ) ? $order : 'ASC';
        $orderType = $orderCheck === 'ASC' ? 'DESC' : 'ASC';
        $chevron = $orderType === 'ASC' ? 'down' : 'up';

        $page = isset($queryParams['page']) && $queryParams['page'] > 0 ? $queryParams['page'] : 1;

        /** @var Role[] $roles */
        $roles = $this->entityManager->getRepository(Role::class)->createQueryBuilder('role', 'role.roleName')->getQuery()->getResult();

        $totalRows = (new \Doctrine\ORM\Tools\Pagination\Paginator($usersQueryBuilder->getQuery()))->count();

        $paginator = [
            'countItems' => $totalRows,
            'query' => $queryParams,
            'currentPage' => $page,
            'itemsPerPage' => $countItems,
            'url' => $url
        ];

        $params = [
            'id',
            'lastName',
            'email',
            //'company',
            'phone',
            'userRole',
            'dateCreate',
            'dateLastAuth',
            'isConfirmed'
        ];

        $field = in_array($sortType, $params) ? $sortType : 'id';

        switch ($field) {
//            case 'company':
//                $users = $usersQueryBuilder
//                    ->leftJoin('u.company', 'c')
//                    ->orderBy('c.name', $orderCheck)
//                    ->setMaxResults($countItems)
//                    ->setFirstResult($countItems * ($page - 1))
//                    ->getQuery()
//                    ->getResult();
//                break;
            case 'userRole':
                $users = $usersQueryBuilder
                    ->innerJoin('u.userRole', 'r')
                    ->orderBy('r.roleName', $orderCheck)
                    ->setMaxResults($countItems)
                    ->setFirstResult($countItems * ($page - 1))
                    ->getQuery()->getResult();
                break;
            default:
                $users = $usersQueryBuilder
                    ->setMaxResults($countItems)
                    ->orderBy('u.'.$field, $orderCheck)
                    ->setFirstResult($countItems * ($page - 1))
                    ->getQuery()->getResult();
        }

        $prepareData['sortType'] = $field;
        $prepareData['order'] = $orderType;
        $prepareData['chevron'] = $chevron;
        $prepareData['users'] = $users;
        $prepareData['roles'] = $roles;
        $prepareData['paginator'] = $paginator;

        return $prepareData;
    }

    /**
     * Сохранение ролей пользователя
     *
     * @param array $roles
     * @param User $user
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateUserRole(array $roles, User $user): void
    {
        $userRoles = $user->getUserRoles();
        foreach ($userRoles as $key => $role) {
            if (!\in_array($role->getRoleName(), $roles, true)) {
                $this->entityManager->remove($role);
            } else {
                unset($roles[array_search($role->getRoleName(), $roles, true)]);
            }
        }

        foreach ($roles as $role) {
            $user->getUserRoleManager()->add((new UserHasRole())->setUser($user)->setRoleName($role));
        }

        $this->entityManager->flush();
    }

    /**
     * Сохранение пользователя
     *
     * @param User $user
     * @param array $params
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveUser(User $user, array $params)
    {
        $user->setLastName(trim($params['lastName']));
        $user->setFirstName(trim($params['firstName']));
        $user->setMiddleName(trim($params['middleName']));
        $user->setEmail(trim($params['email']));
        $user->setIsConfirmed(isset($params['isConfirmed']));
        $this->entityManager->persist($user);
        $this->updateUserRole($params['userRoles'], $user);
        $this->entityManager->flush();
    }
}
