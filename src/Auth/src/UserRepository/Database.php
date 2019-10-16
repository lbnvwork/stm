<?php
/**
 * Created by PhpStorm.
 * User: afinogen
 * Date: 13.03.19
 * Time: 14:17
 */

namespace Auth\UserRepository;

use Auth\Entity\User;
use Auth\Entity\UserHasRole;
use Auth\Service\SendMail;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Permission\Entity\Role;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Authentication\UserRepositoryInterface;

/**
 * Class Database
 *
 * @package Auth\UserRepository
 */
class Database implements UserRepositoryInterface
{
    private $entityManager;
    private $sendMail;

    /**
     * Database constructor.
     *
     * @param EntityManager $entityManager
     * @param SendMail $sendMail
     */
    public function __construct(EntityManager $entityManager, SendMail $sendMail)
    {
        $this->entityManager = $entityManager;
        $this->sendMail = $sendMail;
    }

    public function authenticate(string $credential, string $password = null): ?UserInterface
    {
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $credential]);

        if ($user === null) {
            return null;
        }

        return password_verify($password, $user->getPassword())
            ? $user//$this->generateUser($credential, $this->getRolesFromUser($credential))
            : null;
    }

    /**
     * @param User $user
     * @param string $password
     *
     * @return bool
     */
    public function verifyPassword(User $user, string $password): bool
    {
        return password_verify($password, $user->getPassword());
    }

    public function getRolesFromUser(string $username): array
    {
        return [];
    }

    /**
     * @param array $data
     *
     * @return User
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function register(array $data): User
    {
        $user = new User();
        foreach ($data as $key => $value) {
            $method = 'set'.ucfirst($key);
            $user->{$method}($value);
        }

        $user->getUserRoleManager()->add((new UserHasRole())->setUser($user)->setRoleName('office_user'));

//        $pass = self::generateStrongPassword();
//        $user->setNewPassword($pass);
        $user->setHashKey(str_replace('.', '', uniqid(time(), true)));
        $user->setIsConfirmed(false);
        //$user->setIsBeginner(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->sendMail->sendNewRegister($user);

        return $user;
    }



    // Generates a strong password of N length containing at least one lower case letter,
    // one uppercase letter, one digit, and one special character. The remaining characters
    // in the password are chosen at random from those four sets.
    //
    // The available characters in each set are user friendly - there are no ambiguous
    // characters such as i, l, 1, o, 0, etc. This, coupled with the $add_dashes option,
    // makes it much easier for users to manually type or speak their passwords.
    //
    // Note: the $add_dashes option will increase the length of the password by
    // floor(sqrt(N)) characters.
    public static function generateStrongPassword($length = 8, $add_dashes = false, $available_sets = 'luds')
    {
        $sets = [];
        if (strpos($available_sets, 'l') !== false) {
            $sets[] = 'abcdefghjkmnpqrstuvwxyz';
        }
        if (strpos($available_sets, 'u') !== false) {
            $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
        }
        if (strpos($available_sets, 'd') !== false) {
            $sets[] = '23456789';
        }
        if (strpos($available_sets, 's') !== false) {
            $sets[] = '!@#$%&*?';
        }
        $all = '';
        $password = '';
        foreach ($sets as $set) {
            $password .= $set[array_rand(str_split($set))];
            $all .= $set;
        }
        $all = str_split($all);
        for ($i = 0; $i < $length - count($sets); $i++) {
            $password .= $all[array_rand($all)];
        }
        $password = str_shuffle($password);
        if (!$add_dashes) {
            return $password;
        }
        $dash_len = floor(sqrt($length));
        $dash_str = '';
        while (strlen($password) > $dash_len) {
            $dash_str .= substr($password, 0, $dash_len).'-';
            $password = substr($password, $dash_len);
        }
        $dash_str .= $password;

        return $dash_str;
    }

    /**
     * Возвращает QueryBuilder и параметры для получения списка пользователей с сортировкой по одному из полей
     *
     * @param array $queryParams
     *
     * @return array
     */
    public function getUsersWithOrderBuilder(array $queryParams)
    {
        $data = [];
        $qb = $this->entityManager->getRepository(User::class)->createQueryBuilder('u');
        if (isset($queryParams['email']) && $queryParams['email'] != null) {
            $qb->andWhere('u.email LIKE :email')->setParameter('email', '%'.$queryParams['email'].'%');
        } else {
            $queryParams['email'] = '';
        }

        if (isset($queryParams['fio']) && $queryParams['fio'] != null) {
            $qb->andWhere('(u.lastName LIKE :fio or u.firstName LIKE :fio or u.middleName LIKE :fio)')->setParameter('fio', '%'.$queryParams['fio'].'%');
        } else {
            $queryParams['fio'] = '';
        }
        if (isset($queryParams['company']) && $queryParams['company'] != null) {
            $qb->innerJoin('u.company', 'c');
            $qb->andWhere('c.name LIKE :title and c.isDeleted = 0')->setParameter('title', '%'.$queryParams['company'].'%');
        } else {
            $queryParams['company'] = '';
        }
        if (isset($queryParams['role']) && $queryParams['role'] != null) {
            /** @var Role[] $tmpR */
            $tmpR = $this->entityManager->getRepository(Role::class)->createQueryBuilder('role', 'role.id')->getQuery()->getResult();
            if (isset($tmpR[$queryParams['role']])) {
                $qb->innerJoin('u.userRole', 'r');
                $qb->andWhere('r.roleName = :role')->setParameter('role', $tmpR[$queryParams['role']]->getTitle());
            }
        } else {
            $queryParams['role'] = '';
        }
        $data['qb'] = $qb;
        $data['queryParams'] = $queryParams;
        return $data;
    }
}
