<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 16.05.19
 * Time: 14:51
 */

namespace Office\Service\Validator;

use Doctrine\ORM\EntityManager;
use Office\Entity\LkCompany;
use App\Service\Validator\Parameter;
use App\Service\Validator\ValidatorService;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Authentication\UserInterface;

/**
 * Class CompanyValidatorService
 * Валидация входных данных для Компании
 *
 * @package Office\Service\Validator
 */
class CompanyValidatorService extends ValidatorService
{
    /**
     * CompanyValidatorService constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->params = [
            (new Parameter())
                ->setName('name')
                ->setTitle('Название компании')
                ->setNullable(false)
            ,
            (new Parameter())
                ->setName('inn')
                ->setTitle('ИНН компании')
                ->setNullable(false)
            ,
            (new Parameter())
                ->setName('companyEmail')
                ->setTitle('Email компании')
                ->setNullable(true)
            ,
            (new Parameter())
                ->setName('taxationTypeId')
                ->setTitle('Система налогообложения')
                ->setNullable(true)
            ,
            (new Parameter())
                ->setName('ofdInn')
                ->setTitle('ИНН ОФД')
                ->setNullable(true)
            ,
            (new Parameter())
                ->setName('ofdName')
                ->setTitle('Наименование ОФД')
                ->setNullable(true)
            ,
        ];
        parent::__construct($entityManager);
    }

    /**
     * Выполняет валидацию
     *
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
     * Проверяет ИНН компании
     *
     * @param Parameter $param
     *
     * @return bool
     */
    protected function checkInn(Parameter $param): bool
    {

        if (!$this->isValidNotSpecialChars($param)) {
            return false;
        }
        if (!$this->isValidNotEmty($param)) {
            return false;
        }
        $user = $this->request->getAttribute(UserInterface::class);
        $id = $this->request->getAttribute('id');
        $company = null;
        /** @var LkCompany $company */
        $company = $this->entityManager->getRepository(LkCompany::class)->findOneBy(['id' => $id]);
        if (!is_null($company) && $company->getInn() != $param->getValue()) {
            if ($this->entityManager->getRepository(LkCompany::class)->checkCompanyDuplication($param->getValue(), $user) != null) {
                $param->setMessage('Компания с таким ИНН уже зарегистрирована');
                return false;
            }
        }
        return $this->isValidInn($param);
    }

    /**
     * Проверяет email компании
     *
     * @param Parameter $param
     *
     * @return bool
     */
    protected function checkCompanyEmail(Parameter $param): bool
    {

        if (!$this->isValidNotSpecialChars($param)) {
            return false;
        }
        if (!$this->isValidNotEmty($param)) {
            return false;
        }
        return $this->isValidEmail($param);
    }

    /**
     * Проверяет ИНН ОФД
     *
     * @param Parameter $param
     *
     * @return bool
     */
    protected function checkOfdInn(Parameter $param): bool
    {

        if (!$this->isValidNotSpecialChars($param)) {
            return false;
        }
        if (!$this->isValidNotEmty($param)) {
            return false;
        }
        return $this->isValidInn($param);
    }

    /**
     * Проверяет название ОФД
     *
     * @param Parameter $param
     *
     * @return bool
     */
    protected function checkOfdName(Parameter $param): bool
    {

        if (!$this->isValidNotSpecialChars($param)) {
            return false;
        }
        if (!$this->isValidNotEmty($param)) {
            return false;
        }
        return $this->isValidRusEnStr($param);
    }

    protected function checkName(Parameter $param): bool
    {

        if (!$this->isValidNotSpecialChars($param)) {
            return false;
        }
        if (!$this->isValidNotEmty($param)) {
            return false;
        }
        return $this->isValidRusEnStr($param);
    }
}
