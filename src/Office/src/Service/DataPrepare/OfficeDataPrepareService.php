<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 16.04.19
 * Time: 10:08
 */

namespace Office\Service\DataPrepare;

use Auth\Entity\User;
use Doctrine\ORM\EntityManager;
use App\Service\DataPrepare\DataPrepareService;
use Doctrine\ORM\PersistentCollection;
use Office\Entity\LkCompany;
use Office\Entity\LkDb;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Expressive\Authentication\UserInterface;

/**
 * Class DataPrepareService
 * Методы подготовки данных для контроллеров
 *
 * @package Office\Service
 */
class OfficeDataPrepareService extends DataPrepareService
{
    /**
     * @var array
     */
    protected $persistRefArr;

    /**
     * @var array
     */
    protected $persistFormulaArr;

    /**
     * @var array
     */
    protected $persistArr;

    /**
     * @var array
     */
    protected $prepareRefArr;

    /**
     * @var array
     */
    protected $prepareLkArr;

    /**
     * @var array
     */
    protected $prepareFormulaArr;

    /**
     * DataPrepareService constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->persistFormulaArr = [];
        $this->persistRefArr = [];
        $this->persistArr = [];
        $this->prepareRefArr = [];
        $this->prepareLkArr = [];
        $this->prepareFormulaArr = [];
        parent::__construct($entityManager);
    }

    /**
     * Подготовка сущностей по их id для добавления в БД
     *
     * @param array $prepareArr
     *
     * @return array
     */
    protected function getPersistRefArr(array $prepareArr)
    {
        $data = [];
        foreach ($prepareArr as $prepare) {
            $data +=
                [
                    $prepare['name'] => $prepare['id'] != null ? $this->entityManager->getRepository($prepare['class'])->findOneBy(['id' => $prepare['id']]) : null
                ];
        }
        return $data;
    }

    /**
     * Подготовка справочников с суммами id по формуле (сумма id из набора select2) для добавления в БД
     *
     * @param array $prepareArr
     *
     * @return array
     */
    protected function getPersistFormulaArr(array $prepareArr)
    {
        $data = [];
        foreach ($prepareArr as $prepare) {
            $data +=
                [
                    $prepare['name'] => $prepare['arr'] != null ? $this->getReferenceIdSum($prepare['arr']) : null
                ];
        }
        return $data;
    }

    /**
     * Подготовка сущностей для текущего пользователя для вывода в форме
     *
     * @param $prepareArr [['class'=><полное имя класса>,'name'=>'<заголовок>']...];
     * @param $user
     *
     * @return array
     */
    protected function prepareLkData($prepareArr, User $user)
    {
        $data = [];
        foreach ($prepareArr as $prepare) {
            $data +=
                [
                    $prepare['name'] => $this->entityManager->getRepository($prepare['class'])->getEntitiesByUser($user)
                ];
        }
        return $data;
    }

    /**
     * Подготовка справочников для вывода в форме
     *
     * @param $prepareArr [['class'=><полное имя класса>,'name'=>'<заголовок>']];
     *
     * @return array
     */
    protected function prepareRefDada($prepareArr)
    {
        $data = [];
        foreach ($prepareArr as $prepare) {
            $class = $prepare["class"];
            $data += [
                $prepare['name'] => $this->entityManager->getRepository($class)->findAll()
            ];
        }
        return $data;
    }

    /**
     * Подготовка массивов id, которые создаются из суммы id по формуле (сумма id из набора select2) для вывода в форме
     * $this->prepareFormulaArr ['name' => 'taxationTypes','id' => $company->getTaxationTypeId();]
     *
     * @return array
     */
    protected function prepareFormulaData()
    {
        $data = [];
        foreach ($this->prepareFormulaArr as $prepare) {
            if (!is_null($prepare['id'])) {
                $data +=
                    [
                        $prepare['name'] => $this->getReferenceArr($prepare['id'])
                    ];
            }
        }
        return $data;
    }

    /**
     * Проверяет сущность на принадлежность пользователю
     *
     * @param User $currentUser
     * @param $entity
     *
     * @return bool|Response
     */
    public function checkForUser(User $currentUser, $entity)
    {
        $entityClassArr = [
            LkCompany::class,
            LkDb::class,
        ];
        $validEntity = false;
        foreach ($entityClassArr as $class) {
            if ($entity instanceof $class) {
                $validEntity = true;
            }
        }
        if (!$validEntity) {
            return false;
        }
        $deleted = $entity->getIsDeleted();
        if ($deleted) {
            return false;
        }
        /** @var PersistentCollection $entityUser */
        $entityUsers = $entity->getUser();

        if ($entityUsers instanceof User) {
            if ($entityUsers == $currentUser) {
                return true;
            }
        } elseif ($entityUsers instanceof PersistentCollection) {
            if ($entityUsers->offsetExists($currentUser->getId())) {
                return true;
            }
        }
        return false;
    }

    /**
     * Получает массив id из суммы id по формуле: каждый id внутри суммы равен 2^(n-1) - круглое число в двоичной системе
     *
     * @param int $idSum
     *
     * @return array
     */
    protected function getReferenceArr(int $idSum): array
    {
        $resArr = [];
        if ($idSum > 0) {
            if ($idSum % 2 == 1) {
                $resArr[] = 1;
                $idSum--;
            }
            $test = $idSum;
            $remant = 0;
            do {
                $testBin = decbin($test);
                $testBin = rtrim($testBin, '0');
                if ($testBin === '1') {
                    $resArr[] = $test;
                    $test = $remant;
                    $remant = 0;
                } else {
                    $remant += 2;
                    $test -= 2;
                }
            } while ($test > 0);
        }
        return $resArr;
    }

    /**
     * Получает сумму id из массива id @todo добавить проверку на соответствие id формуле: каждый id внутри суммы равен 2^(n-1) - круглое число в двоичной системе
     *
     * @param array $ids
     *
     * @return int
     */
    protected function getReferenceIdSum(array $ids): ?int
    {
        $sum = array_sum($ids);
        if ($sum != null) {
            return $sum;
        } else {
            return null;
        }
    }

    /**
     * Подготавливает массив данных для добавления в БД (методом setEntityData)
     *
     * @param array $params
     *
     * @return array
     */
    public function getPersistArr(array $params)
    {
        foreach ($this->persistFormulaArr as $key => $item) {
            if (isset($params[$item['name']])) {
                $this->persistFormulaArr[$key]['arr'] = $params[$item['name']];
            }
        }
        foreach ($this->persistRefArr as $k => $item) {
            if (isset($params[$item['name']])) {
                $this->persistRefArr[$k]['id'] = $params[$item['name']];
            }
        }
        $persistArr = [];
        $persistArr += $this->getPersistRefArr($this->persistRefArr);
        $persistArr += $this->getPersistFormulaArr($this->persistFormulaArr);
        $this->persistArr = $persistArr;
        return $this->persistArr;
    }

    /**
     * Возвращает данные для вывода в шаблоне
     *
     * @param ServerRequestInterface $request
     *
     * @return array
     */
    public function getData(ServerRequestInterface $request)
    {
        /** @var User $user */
        $user = $request->getAttribute(UserInterface::class);
        $refData = $this->prepareRefDada($this->prepareRefArr);
        $lkData = $this->prepareLkData($this->prepareLkArr, $user);
        return $refData + $lkData;
    }

    /**
     * Возвращает данные для вывода в шаблоне, преобразованные по формуле
     *
     * @param ServerRequestInterface $request
     *
     * @return array
     */
    public function getFormulaData(ServerRequestInterface $request)
    {
        return $this->prepareFormulaData();
    }
}
