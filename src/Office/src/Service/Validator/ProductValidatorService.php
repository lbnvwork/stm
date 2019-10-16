<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 17.05.19
 * Time: 16:58
 */

namespace Office\Service\Validator;

use Doctrine\ORM\EntityManager;
use App\Service\Validator\Parameter;
use App\Service\Validator\ValidatorService;
use Office\Entity\LkDb;
use Office\Entity\LkProduct;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ProductValidatorService
 * Валидация входных данных для Товара
 *
 * @package Office\Service\Validator
 */
class ProductValidatorService extends ValidatorService
{
    /**
     * ProductValidatorService constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->params = [
            (new Parameter())
                ->setName('strih')
                ->setTitle('Штрихкод')
                ->setNullable(false)
            ,
            (new Parameter())
                ->setName('name')
                ->setTitle('Наименование товара')
                ->setNullable(true)
            ,
            (new Parameter())
                ->setName('count')
                ->setTitle('Количество товаров')
                ->setNullable(true)
            ,
            (new Parameter())
                ->setName('unitMeasure')
                ->setTitle('Единица измерения')
                ->setNullable(true)
            ,
            (new Parameter())
                ->setName('section')
                ->setTitle('Секция')
                ->setNullable(true)
            ,
            (new Parameter())
                ->setName('price')
                ->setTitle('Цена')
                ->setNullable(false)
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
        parent::check($request, $paramArr);

        return $this;
    }

    /**
     * Проверяет штрихкод
     *
     * @param Parameter $param
     *
     * @return bool
     */
    protected function checkStrih(Parameter $param)
    {

        if (!$this->isValidNotSpecialChars($param)) {
            return false;
        }
        if (!$this->isValidNotEmty($param)) {
            return false;
        }
        if (!$this->isValidNaturalNumber($param)) {
            return false;
        }
        if (!$this->isValidStrLen($param, 13, 'max')) {
            return false;
        }
        //begin проверка на уникальный штрихкод
        $id = $this->request->getAttribute('id');
        $db = $this->entityManager->getRepository(LkDb::class)->findOneBy(['id' => $id]);
        $duplicateProducts = $this->entityManager->getRepository(LkProduct::class)->checkProductDuplication($param->getValue(), $db);
        if (!empty($duplicateProducts)) {
            /** @var LkProduct $duplicateProduct */
            foreach ($duplicateProducts as $duplicateProduct) {
                if ($duplicateProduct->getId() != $this->request->getAttribute('pid')) {
                    $param->setMessage('Продукт с таким штрихкодом уже зарегистрирован в этом списке товаров');
                    return false;
                }
            }
        }
        //end проверка на уникальный штрихкод
        return true;
    }

    /**
     * Проверяет количство товаров
     *
     * @param Parameter $param
     *
     * @return bool
     */
    protected function checkCount(Parameter $param)
    {

        if (!$this->isValidNotSpecialChars($param)) {
            return false;
        }
        if (!$this->isValidNumberSize($param, 32767, 'max')) {
            return false;
        }
        return $this->isValidNonnegativeNumber($param);
    }

    /**
     * Проверяет секцию
     *
     * @param Parameter $param
     *
     * @return bool
     */
    protected function checkSection(Parameter $param)
    {

        if (!$this->isValidNotSpecialChars($param)) {
            return false;
        }
        if (!$this->isValidNaturalNumber($param)) {
            return false;
        }
        return $this->isValidNumberSize($param, 32767, 'max');
    }

    /**
     * Проверяет цену
     *
     * @param Parameter $param
     *
     * @return bool
     */
    protected function checkPrice(Parameter $param)
    {

        if (!$this->isValidNotSpecialChars($param)) {
            return false;
        }
        return $this->isValidNonnegativeNumber($param);
    }

    protected function checkName(Parameter $param)
    {
        return $this->isValidStrLen($param, 96, 'max');
    }

    protected function checkUnitMeasure(Parameter $param)
    {

        if (!$this->isValidNotSpecialChars($param)) {
            return false;
        }
        if (!$this->isValidNotEmty($param)) {
            return false;
        }
        return $this->isValidStrLen($param, 20, 'max');
    }
}
