<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 03.06.19
 * Time: 10:22
 */

namespace Cms\Service\Validator;

use App\Service\Validator\Parameter;
use App\Service\Validator\ValidatorService;
use Auth\Entity\User;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class UserValidatorService
 * Валидатор пользователей
 *
 * @package Cms\Service\Validator
 */
class UserValidatorService extends ValidatorService
{
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
                ->setName('email')
                ->setTitle('Email')
                ->setNullable(false)
            ,
            (new Parameter())
                ->setName('password')
                ->setTitle('Пароль')
                ->setNullable(true)
            ,
            (new Parameter())
                ->setName('password2')
                ->setTitle('Повтор пароля')
                ->setNullable(true)
            ,
            (new Parameter())
                ->setName('isConfirmed')
                ->setTitle('Подтвержен')
                ->setNullable(true)
            ,
            (new Parameter())
                ->setName('userRoles')
                ->setTitle('Роли')
                ->setNullable(false)
            ,
        ];
        parent::__construct($entityManager);
    }

    public function check(ServerRequestInterface $request, array $paramArr = [])
    {
        parent::check($request, $paramArr);

        return $this;
    }

    protected function checkEmail(Parameter $param)
    {



        if (!$this->isValidNotSpecialChars($param)) {
            return false;
        }

        $id = $this->request->getAttribute('id');
        if (!$this->entityManager->getRepository(User::class)->checkEmailDuplication($param->getValue(), $id)) {
            $param->setMessage('Пользователь с таким email уже зарегистрирован');
            return false;
        }

        if (!$this->isValidNotEmty($param)) {
            return false;
        }

        return $this->isValidEmail($param);
    }

    protected function checkFirstName(Parameter $param)
    {

        if (!$this->isValidNotSpecialChars($param)) {
            return false;
        }
        return $this->isValidRusEnStr($param);
    }

    protected function checkLastName(Parameter $param)
    {

        if (!$this->isValidNotSpecialChars($param)) {
            return false;
        }
        return $this->isValidRusEnStr($param);
    }

    protected function checkMiddleName(Parameter $param)
    {

        if (!$this->isValidNotSpecialChars($param)) {
            return false;
        }
        return $this->isValidRusEnStr($param);
    }

    protected function checkPassword(Parameter $param)
    {

        if (!$this->isValidNotSpecialChars($param)) {
            return false;
        }
        return $this->isValidNotEmty($param);
    }

    protected function checkPassword2(Parameter $param)
    {

        if (!$this->isValidNotSpecialChars($param)) {
            return false;
        }
        return $this->isValidNotEmty($param);
    }
}
