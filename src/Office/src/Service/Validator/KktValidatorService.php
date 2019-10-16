<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 17.05.19
 * Time: 15:43
 */

namespace Office\Service\Validator;

use Doctrine\ORM\EntityManager;
use App\Service\Validator\Parameter;
use App\Service\Validator\ValidatorService;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class KktValidatorService
 * Валидация входных данных для Кассы
 *
 * @package Office\Service\Validator
 */
class KktValidatorService extends ValidatorService
{
    /**
     * KktValidatorService constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->params = [
            (new Parameter())
                ->setName('company')
                ->setTitle('Компания')
                ->setNullable(false)
            ,
            (new Parameter())
                ->setName('seria')
                ->setTitle('Серия ККТ')
                ->setNullable(false)
            ,
            (new Parameter())
                ->setName('machineNumber')
                ->setTitle('Заводской номер')
                ->setNullable(false)
            ,
            (new Parameter())
                ->setName('db')
                ->setTitle('База товаров')
                ->setNullable(true)
            ,
//            (new Parameter())
//                ->setName('firm')
//                ->setTitle('Прошивка')
//                ->setNullable(true)
//            ,
            (new Parameter())
                ->setName('ffdKktVersion')
                ->setTitle('Версия ФФД ККТ')
                ->setNullable(true)
            ,
            (new Parameter())
                ->setName('retailAddress')
                ->setTitle('Адрес рассчетов')
                ->setNullable(true)
            ,
            (new Parameter())
                ->setName('retailPlace')
                ->setTitle('Место рассчетов')
                ->setNullable(true)
            ,
            (new Parameter())
                ->setName('datetime')
                ->setTitle('Дата рассчетов')
                ->setNullable(true)
            ,
            (new Parameter())
                ->setName('kktRegId')
                ->setTitle('Регистрационный номер ККТ')
                ->setNullable(true)
            ,
            (new Parameter())
                ->setName('kktMode')
                ->setTitle('Режим работы')
                ->setNullable(true)
            ,
            (new Parameter())
                ->setName('kktAdvancedMode')
                ->setTitle('Расширенный режим работы')
                ->setNullable(true)
            ,
            (new Parameter())
                ->setName('paymentAgentType')
                ->setTitle('Признак агента')
                ->setNullable(true)
            ,
            (new Parameter())
                ->setName('fps')
                ->setTitle('Фискальный признак')
                ->setNullable(true)
            ,
            (new Parameter())
                ->setName('synchronization')
                ->setTitle('Синхронизация')
                ->setNullable(true)
            ,
        ];
        parent::__construct($entityManager);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return $this
     */
    public function check(ServerRequestInterface $request, array $paramArr = [])
    {
        parent::check($request);

        return $this;
    }

    /**
     * Проверяет серию ККТ
     *
     * @param Parameter $param
     *
     * @return bool
     */
    protected function checkSeria(Parameter $param): bool
    {

        if (!$this->isValidNaturalNumber($param)) {
            return false;
        }
        return $this->isValidNumberSize($param, 32767, 'max');
    }

    /**
     * Проверяет заводской номер ККТ
     *
     * @param Parameter $param
     *
     * @return bool
     */
    protected function checkMachineNumber(Parameter $param): bool
    {

        if (!$this->isValidNaturalNumber($param)) {
            return false;
        }
        return $this->isValidStrLen($param, 7, 'fixed');
    }

    /**
     * @param Parameter $param
     *
     * @return bool
     */
    protected function checkRetailAdress(Parameter $param): bool
    {

        return $this->isValidStrLen($param, 254, 'max');
    }

    /**
     * @param Parameter $param
     *
     * @return bool
     */
    protected function checkKktRegId(Parameter $param): bool
    {

        if (!$this->isValidNaturalNumber($param)) {
            return false;
        }
        if (!$this->isValidStrLen($param, 16, 'fixed')) {
            $param->setMessage('Регистрационный номер ККТ должен содержать 16 символов');
            return false;
        }
        return true;
    }
}
