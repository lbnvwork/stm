<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 10.04.19
 * Time: 14:09
 */

namespace Office\Repository;

use Auth\Entity\User;
use Doctrine\ORM\EntityRepository;
use Office\Entity\LkCompany;
use Office\Entity\LkDb;
use Office\Entity\LkKkt;
use Office\Entity\LkProduct;
use Office\Service\DataPrepare\OfficeDataPrepareService;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Authentication\UserInterface;

/**
 * Class ReferenceRepository
 *
 * @package Office\Repository
 */
class OfficeRepository extends EntityRepository
{

    /**
     * Проверяет компанию на дубль по инн для пользователя
     *
     * @param string $inn
     * @param User $user
     *
     * @return mixed
     */
    public function checkCompanyDuplication(string $inn, User $user)
    {
        return $this->getEntityManager()->getRepository(LkCompany::class)->createQueryBuilder('c')
            ->leftJoin('c.user', 'u')
            ->where('c.inn = :inn and u = :user')
            ->setParameter('inn', $inn)
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * Проверяет компанию на дубль по серии и заводскому номеру
     *
     * @param int $seria
     * @param string $machineNumber
     *
     * @return mixed
     */
    public function checkKktDuplication(int $seria, string $machineNumber)
    {
        return $this->getEntityManager()->getRepository(LkKkt::class)->createQueryBuilder('k')
            //->leftJoin('k.user', 'u')
            ->where('k.seria = :seria and k.machineNumber = :machineNumber and k.isDeleted=false')
            ->setParameter('seria', $seria)
            ->setParameter('machineNumber', $machineNumber)
            //->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * Проверка на уникальный штрихкод
     *
     * @param string $strih
     * @param LkDb $db
     *
     * @return mixed
     */
    public function checkProductDuplication(string $strih, LkDb $db)
    {
        return $this->getEntityManager()->getRepository(LkProduct::class)->createQueryBuilder('p')
            ->where('p.strih = :strih and p.db = :db')
            ->setParameter('strih', $strih)
            ->setParameter('db', $db)
            ->getQuery()
            ->getResult();
    }

    /**
     * Возвращает выборку по сущности для текущего пользователя
     *
     * @param User $user
     * @param string $class
     *
     * @return mixed
     */
    public function getEntitiesByUser(User $user)
    {
        return $this->getEntityManager()->getRepository($this->_class->getName())->createQueryBuilder('c')
            ->leftJoin('c.user', 'u')
            ->where('(c.isDeleted = 0 or c.isDeleted is null) and u = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * Добавляет данные в БД
     *
     * @param array $params
     * @param $entity
     * @param array $persistArr
     *
     * @return mixed
     * @throws \Doctrine\ORM\ORMException
     */
    public function setEntityData(array $params, $entity, array $persistArr = [])
    {
        foreach ($params as $key => $param) {
            if (array_key_exists($key, $persistArr)) {
                if (gettype($persistArr[$key])==='string') {
                    $persistArr[$key] = trim($persistArr[$key]);
                }
                $method = 'set'.ucfirst($key);
                $entity->{$method}($persistArr[$key]);
                continue;
            }
            if (gettype($param)==='string') {
                $param = trim($param);
            }
            $method = 'set'.ucfirst($key);
            $entity->{$method}($param);
        }
        $this->getEntityManager()->persist($entity);
        return $entity;
    }

    /**
     * Возвращает порядковый номер для записи (для поля serialNumber)
     *
     * @param string $class
     * @param User $user
     * @param $joinUserEntity
     *
     * @return int|mixed
     */
    public function getSerialNumber(string $class, User $user, $joinUserEntity = null)
    {
        $id = $this->getEntityManager()->getRepository($class)->createQueryBuilder('e')
            ->select('MAX(e.serialNumber)');
        if ($joinUserEntity === null) {
            $id
                ->leftJoin('e.user', 'u');
        } else {
            $id
                ->leftJoin('e.'.$joinUserEntity, 'j')
                ->leftJoin('j.user', 'u');
        }
        $id = $id
            ->where('u = :user')
            ->setParameter('user', $user)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
        $id = current(current($id));
        return is_null($id) ? 0 : ++$id;
    }

    /**
     * Получает порядковый номер для продукта (для поля serialNumber)
     *
     * @param LkDb $db
     *
     * @return int|mixed
     */
    public function getProductSerialNumber(LkDb $db)
    {
        $id = $this->getEntityManager()->getRepository(LkProduct::class)->createQueryBuilder('e')
            ->select('MAX(e.serialNumber)')
            ->where('e.db = :db')
            ->setParameter('db', $db)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
        $id = current(current($id));
        return is_null($id) ? 0 : ++$id;
    }

    /**
     * Удаляет сущность мягким удалением
     *
     * @param ServerRequestInterface $request
     * @param OfficeDataPrepareService $dataPrepareService
     *
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteEntity(ServerRequestInterface $request, OfficeDataPrepareService $dataPrepareService, $joinUserEntity = null)
    {

        $user = $request->getAttribute(UserInterface::class);
        $id = $request->getAttribute('id');
        $entity = null;
        $entity = $this->getEntityManager()->getRepository($this->_class->getName())->findOneBy(['id' => $id]);
        if (!$dataPrepareService->checkForUser(
            $user,
            is_null($joinUserEntity) ? $entity : $entity->{'get'.ucfirst($joinUserEntity)}()
        )
        ) {
            return false;
        };
        $entity->setIsDeleted(true);
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        return true;
    }

    /**
     * Удаляет продукт из базы
     *
     * @param ServerRequestInterface $request
     * @param OfficeDataPrepareService $dataPrepareService
     *
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteProduct(ServerRequestInterface $request, OfficeDataPrepareService $dataPrepareService)
    {
        define('FORMULA_MULTIPLIER', 1000000);
        $user = $request->getAttribute(UserInterface::class);
        $pid = $request->getAttribute('pid');
        $product = null;
        /** @var LkProduct $product */
        $product = $this->getEntityManager()->getRepository($this->_class->getName())->findOneBy(['id' => $pid]);

        $db = $product->getDb();
        if (!$dataPrepareService->checkForUser($user, $db)) {
            return false;
        };
        $currSerialNumber = $product->getSerialNumber();
        if ($currSerialNumber == $this->getProductSerialNumber($db) - 1) {
            $this->getEntityManager()->remove($product);
        } else {
            $this->getEntityManager()->remove($product);
            $updateProducts = $this->getEntityManager()->getRepository(LkProduct::class)->createQueryBuilder('p')
                ->where('p.db = :db AND p.serialNumber > :currSerialNumber')
                ->setParameter('db', $db)
                ->setParameter('currSerialNumber', $currSerialNumber)
                ->getQuery()
                ->getResult();
            /** @var LkProduct $updateProduct */
            foreach ($updateProducts as $updateProduct) {
                $updateProduct->setSerialNumber($updateProduct->getSerialNumber() - 1);
                $updateProduct->setIdByFormula(
                    $user->getId() * FORMULA_MULTIPLIER + $db->getSerialNumber() * FORMULA_MULTIPLIER + $updateProduct->getSerialNumber()
                );
                $this->getEntityManager()->persist($updateProduct);
            }
        }
        $this->getEntityManager()->flush();

        return true;
    }

    /**
     * Удаляет все продукты из базы
     * @param ServerRequestInterface $request
     * @param OfficeDataPrepareService $dataPrepareService
     *
     * @return int|mixed
     */
    public function clearProducts(ServerRequestInterface $request, OfficeDataPrepareService $dataPrepareService)
    {
        $user = $request->getAttribute(UserInterface::class);
        $id = $request->getAttribute('id');
        $db = null;
        /** @var LkDb $db */
        $db = $this->getEntityManager()->getRepository(LkDb::class)->findOneBy(['id' => $id]);
        if (!$dataPrepareService->checkForUser($user, $db)) {
            return -1;
        };
        try {
            $res = $this->getEntityManager()->getRepository(LkProduct::class)->createQueryBuilder('p')
            ->delete()
            ->where('p.db = :db')
                ->setParameter('db', $db)
                ->getQuery()
                ->getResult();
            $this->getEntityManager()->flush();
        } catch (\Exception $e) {
            return -1;
        }
        return $res;
    }
    /**
     * @param ServerRequestInterface $request
     *
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteCustomer(ServerRequestInterface $request)
    {
        /** @var User $user */
        $user = $request->getAttribute(UserInterface::class);
        $companies = $user->getCompany();
        $id = $request->getAttribute('id');
        /** @var User $customer */
        $customer = $this->getEntityManager()->getRepository(User::class)->findOneBy(['id' => $id]);
        $customer->removeCtoUser($user);
        /** @var LkCompany $company */
        foreach ($companies as $company) {
            $customer->removeCompany($company);
        }
        $this->getEntityManager()->persist($customer);
        $this->getEntityManager()->flush();

        return true;
    }

    /**
     * Возвращает пользователя по email
     *
     * @param string $email
     *
     * @return mixed
     */
    public function getUserByEmail(string $email)
    {
        return $this->getEntityManager()->getRepository(User::class)->createQueryBuilder('u')
            ->where('u.email = :email')->setParameter('email', $email)->setMaxResults(1)
            ->getQuery()->getResult();
    }

    public function getProductsWithOrderBuilder(array $queryParams, LkDb $db)
    {
        $data = [];
        $qb = $this->getEntityManager()->getRepository(LkProduct::class)->createQueryBuilder('p')
            ->where('p.db = :db')->setParameter('db', $db);
        if (isset($queryParams['strih']) && $queryParams['strih'] != null) {
            $qb->andWhere('p.strih LIKE :strih')->setParameter('strih', '%'.$queryParams['strih'].'%');
        } else {
            $queryParams['strih'] = '';
        }

        if (isset($queryParams['name']) && $queryParams['name'] != null) {
            $qb->andWhere('p.name LIKE :name')->setParameter('name', '%'.$queryParams['name'].'%');
        } else {
            $queryParams['name'] = '';
        }
        if (isset($queryParams['section']) && $queryParams['section'] != null) {
            $qb->andWhere('p.section LIKE :section')->setParameter('section', '%'.$queryParams['section'].'%');
        } else {
            $queryParams['section'] = '';
        }
        $data['qb'] = $qb;
        $data['queryParams'] = $queryParams;
        return $data;
    }
}
