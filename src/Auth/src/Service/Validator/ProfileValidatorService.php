<?php
/**
 * Created by PhpStorm.
 * User: m-lobanov
 * Date: 07.06.19
 * Time: 10:22
 */

namespace Auth\Service\Validator;

use App\Service\Validator\Parameter;
use App\Service\Validator\ValidatorService;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ServerRequestInterface;

class ProfileValidatorService extends ValidatorService
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
                ->setName('lastName')
                ->setTitle('Фамилия')
                ->setNullable(false)
            ,
            (new Parameter())
                ->setName('firstName')
                ->setTitle('Имя')
                ->setNullable(true)
            ,
            (new Parameter())
                ->setName('middleName')
                ->setTitle('Отчество')
                ->setNullable(true)
            ,
            (new Parameter())
                ->setName('phone')
                ->setTitle('Телефон')
                ->setNullable(true)
            ,
//            (new Parameter())
//                ->setName('email')
//                ->setTitle('Email')
//                ->setNullable(false)
//            ,
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
        parent::check($request, $paramArr);

        return $this;
    }

    protected function checkLastName(Parameter $param): bool
    {

        if (!$this->isValidNotSpecialChars($param)) {
            return false;
        }
        return $this->isValidRusEnStr($param);
    }

    protected function checkFirstName(Parameter $param): bool
    {

        if (!$this->isValidNotSpecialChars($param)) {
            return false;
        }
        return $this->isValidRusEnStr($param);
    }

    protected function checkMiddleName(Parameter $param): bool
    {

        if (!$this->isValidNotSpecialChars($param)) {
            return false;
        }
        return $this->isValidRusEnStr($param);
    }

    protected function checkPhone(Parameter $param): bool
    {

        if (!$this->isValidNotSpecialChars($param)) {
            return false;
        }
        if (!$this->isValidNotEmty($param)) {
            return false;
        }
        return $this->isValidPhone($param);
    }
}
