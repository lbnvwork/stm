<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 16.05.19
 * Time: 16:57
 */

namespace App\Service\Validator;

use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ValidatorService
 * Реализация функционала валидатора для дочерних классов
 *
 * @package App\Service\Validator
 */
abstract class ValidatorService implements ValidatorInterface
{
    /** @var EntityManager $entityManager */
    protected $entityManager;

    /** @var ServerRequestInterface $request */
    protected $request;

    /** @var ValidatorMessages $messages */
    protected $messages;

    protected $params = [];

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getParameters()
    {
        return $this->params;
    }

    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param ServerRequestInterface $request
     * Добавляет объект Request в валидатор
     * Добавляет объект ValidatorMessages в валидатор
     * Выполняет проверку всех параметров валидатора
     * Определяет сообщения по результатам проверки
     *
     * @return mixed|void
     */
    public function check(ServerRequestInterface $request, array $paramArr = [])
    {
        $this->request = $request;
        $requestValues = empty($paramArr) ? $this->setNullParams($this->request->getParsedBody()) : $paramArr;
        $this->messages = new ValidatorMessages($this);
        $requestValues = $this->setNullParams($requestValues);
        /** @var Parameter $parameter */
        foreach ($this->params as $parameter) {
            if (array_key_exists($parameter->getName(), $requestValues)) {
                $parameter->setValue($requestValues[$parameter->getName()]);
                $parameter->setValid($this->isValidNull($parameter));
                if ($parameter->getValid()) {
                    if (!($parameter->getValue()===null || $parameter->getValue()==='')) {
                        $method = 'check'.ucfirst($parameter->getName());
                        $parameter->setValid($this->{$method}($parameter));
                    }
                }
                $parameter->setChecked(true);
            }
        }
        $this->messages->prepareMessages();
    }

    /**
     * Устанавливает нулевые значения для параметров, не переданных из формы
     *
     * @param array $params
     *
     * @return array
     */
    public function setNullParams(array $params)
    {
        /** @var Parameter $validatorParam */
        foreach ($this->params as $validatorParam) {
            if (!isset($params[$validatorParam->getName()])) {
                $params[$validatorParam->getName()] = null;
            }
        }
        return $params;
    }

    /**
     * Определяет валидность на основании всех параметров валидатора
     *
     * @return bool
     */
    public function isValid()
    {
        /** @var Parameter $parameter */
        foreach ($this->params as $parameter) {
            if (!$parameter->getValid()) {
                return false;
            }
        }
        return true;
    }

    public function findParameterByName(string $parameterName)
    {
        /** @var Parameter $parameter */
        foreach ($this->getParameters() as $parameter) {
            if ($parameter->getName() == $parameterName) {
                return $parameter;
            }
        }
    }

    /**
     * Определяет валидность ИНН
     *
     * @param Parameter $param
     *
     * @return bool
     */
    protected function isValidInn(Parameter $param)
    {
        if (!preg_match('/^[\d+]{10,12}$/', $param->getValue())) {
            $param->setMessage('Значение поля '.$param->getTitle().' должно содержать 10 или 12 цифр!');
            return false;
        }
        return true;
    }

    /**
     * Определяет валидность эл. почты
     *
     * @param Parameter $param
     *
     * @return bool
     */
    protected function isValidEmail(Parameter $param)
    {
        if (!filter_var($param->getValue(), FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        if (!preg_match('/^[\w\-\.\_]+@([\w\-]+\.)+[a-z]+$/si', $param->getValue())) {
            return false;
        };
        return true;
    }

    /**
     * Определяет валидность пустого поля на основании статуса nullable параметра
     *
     * @param Parameter $param
     *
     * @return bool
     */
    protected function isValidNull(Parameter $param)
    {
        if (!$param->getNullable() && ($param->getValue() === null || $param->getValue() === '')) {
            $param->setMessage('Значение поля '.$param->getTitle().' не может быть пустым!');
            return false;
        }
        return true;
    }

    /**
     * Определяет содержит ли поле данные
     *
     * @param Parameter $param
     *
     * @return bool
     */
    protected function isValidNotEmty(Parameter $param)
    {
        if (empty(trim((string)$param->getValue()))) {
            $param->setMessage('Значение поля '.$param->getTitle().' не может быть пустым!');
            return false;
        }
        return true;
    }

    /**
     * Определяет валидность строки с только русским текстом, пробельными символами, знаком подчеркивания и тире
     *
     * @param Parameter $param
     *
     * @return bool
     */
    protected function isValidRusStr(Parameter $param)
    {
        if (!preg_match('/^[а-яА-Я0-9\s\_\-]+$/iu', $param->getValue())) {
            $param->setMessage('Значение поля '.$param->getTitle().' может содержать только русские буквы, а также знаки _,-!');
            return false;
        };
        return true;
    }

    protected function isValidRusEnStr(Parameter $param)
    {
        if (!$this->isValidNotEmty($param)) {
            return false;
        }
        if (!preg_match('/^[a-zA-Zа-яА-Я0-9\s\_\-]+$/iu', $param->getValue())) {
            $param->setMessage('Значение поля '.$param->getTitle().' может содержать только русские, английские буквы, а также знаки _,-!');
            return false;
        };
        return true;
    }

    /**
     * Определяет валидность натурального числа
     *
     * @param Parameter $param
     *
     * @return bool
     */
    protected function isValidNaturalNumber(Parameter $param)
    {
        $val = (int)$param->getValue();
        if (!preg_match('/^[1-9][0-9]*\d*$/', $val)) {
            $param->setMessage('Значение поля '.$param->getTitle().' должно быть натуральным числом!');
            return false;
        };
        return true;
    }

    /**
     * Определяет валидность неотрицательного действительного числа
     *
     * @param Parameter $param
     *
     * @return bool
     */
    protected function isValidNonnegativeNumber(Parameter $param)
    {
        if (!(is_numeric($param->getValue()) && (float)$param->getValue() >= 0)) {
            $param->setMessage('Значение поля '.$param->getTitle().' должно быть действительным неотрицательным числом!');
            return false;
        }
//        if (!preg_match('/0|^(0*[1-9][0-9]*(\.[0-9]+)?|0+\.[0-9]*[1-9][0-9]*)$/', $param->getValue())) {
//            $param->setMessage('Значение поля '.$param->getTitle().' должно быть действительным неотрицательным числом!');
//            return false;
//        };
        return true;
    }

    /**
     * Проверка номера телефона
     *
     * @param Parameter $param
     *
     * @return bool
     */
    protected function isValidPhone(Parameter $param)
    {
        if (!preg_match('/^(\+7|8)[\.\-\s]*\(?\d{3}\)?[\.\-\s]*\d{3}[\.\-\s]*\d{2}[\.\-\s]*\d{2}$/si', $param->getValue())) {
            return false;
        };
        return true;
    }

    /**
     * Проверяет длину строки
     *
     * @param Parameter $parameter
     * @param int $lettersCount длина строки
     * @param string $option max - верхний предел, min - нижний предел, fixed - строгое соответствие
     *
     * @return bool
     */
    protected function isValidStrLen(Parameter $parameter, int $lettersCount, string $option = 'fixed')
    {
        switch ($option) {
            case 'min':
                if (mb_strlen((string)$parameter->getValue()) < $lettersCount) {
                    $parameter->setMessage('В параметре "'.$parameter->getTitle().'" длина строки не должна быть меньше '.$lettersCount.'!');
                    return false;
                } else {
                    return true;
                }
                break;
            case 'max':
                if (mb_strlen((string)$parameter->getValue()) > $lettersCount) {
                    $parameter->setMessage('В параметре "'.$parameter->getTitle().'" длина строки не должна быть больше '.$lettersCount.'!');
                    return false;
                } else {
                    return true;
                }
                break;
            case 'fixed':
                if (!(mb_strlen((string)$parameter->getValue()) === $lettersCount)) {
                    $parameter->setMessage('В параметре "'.$parameter->getTitle().'" длина строки должна быть равна '.$lettersCount.'!');
                    return false;
                } else {
                    return true;
                }
                break;
            default:
                return true;
        }
    }

    /**
     * Проверяет величину числа
     *
     * @param Parameter $parameter
     * @param int $number
     * @param string $option max - верхний предел, min - нижний предел, fixed - строгое соответствие
     *
     * @return bool
     */
    protected function isValidNumberSize(Parameter $parameter, int $number, string $option = 'fixed')
    {
        switch ($option) {
            case 'min':
                if ($parameter->getValue() < $number) {
                    $parameter->setMessage('В параметре "'.$parameter->getTitle().'" данное значение не может быть меньше '.$number.'!');
                    return false;
                } else {
                    return true;
                }
                break;
            case 'max':
                if ($parameter->getValue() > $number) {
                    $parameter->setMessage('В параметре "'.$parameter->getTitle().'" данное значение не может быть больше '.$number.'!');
                    return false;
                } else {
                    return true;
                }
                break;
            case 'fixed':
                if ($parameter->getValue() === $number) {
                    $parameter->setMessage('В параметре "'.$parameter->getTitle().'" данное значение должно быть равно '.$number.'!');
                    return false;
                } else {
                    return true;
                }
                break;
            default:
                return true;
        }
    }

    protected function isValidNotSpecialChars(Parameter $parameter)
    {
        if ($parameter->getValue() != htmlspecialchars($parameter->getValue())) {
            $parameter->setMessage(
                'Спецсимволы запрещены для поля '.$parameter->getTitle().'!'
            );
            return false;
        }
        return true;
    }

    /**
     * Определяет валидность на пустое значение, если метод для параметра не определен
     *
     * @param $name
     * @param $arguments
     *
     * @return bool
     */
    public function __call($name, $arguments)
    {
        return $this->isValidNull($arguments[0]) ?? true;
    }
}
