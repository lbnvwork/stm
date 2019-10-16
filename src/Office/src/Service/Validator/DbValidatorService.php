<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 30.05.19
 * Time: 16:23
 */

namespace Office\Service\Validator;

use App\Service\Validator\Parameter;
use App\Service\Validator\ValidatorService;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ServerRequestInterface;

class DbValidatorService extends ValidatorService
{
    public function __construct(EntityManager $entityManager)
    {
        $this->params = [
            (new Parameter())
                ->setName('name')
                ->setTitle('Название базы товаров')
                ->setNullable(false)
            ,
            (new Parameter())
                ->setName('maxCount')
                ->setTitle('Максимальное количество товаров')
                ->setNullable(false)
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
     * Проверяет название БД
     *
     * @param Parameter $param
     *
     * @return bool
     */
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

    /**
     * Проверяет максимальное количество товаров
     *
     * @param Parameter $param
     *
     * @return bool
     */
    protected function checkMaxCount(Parameter $param)
    {

        if (!$this->isValidNotSpecialChars($param)) {
            return false;
        }
        if (!$this->isValidNumberSize($param, 1, 'min')) {
            return false;
        }
        return $this->isValidNumberSize($param, 10000, 'max');
    }
}
