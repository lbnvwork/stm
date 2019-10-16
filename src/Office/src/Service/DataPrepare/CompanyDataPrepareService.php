<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 29.05.19
 * Time: 11:15
 */

namespace Office\Service\DataPrepare;

use Auth\Entity\User;
use Doctrine\ORM\EntityManager;
use Office\Entity\LkCompany;
use Office\Entity\RefTaxationType;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class CompanyDataPrepareService
 * Подготавливает данные компании
 *
 * @package Office\Service\DataPrepare
 */
class CompanyDataPrepareService extends OfficeDataPrepareService
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager);
        $this->persistFormulaArr = [
            [
                'name' => 'taxationTypeId',
                'arr' => null
            ],
        ];

        $this->prepareRefArr = [
            [
                'name' => 'taxationTypes',
                'class' => RefTaxationType::class
            ]
        ];

        $this->prepareFormulaArr = [
            [
                'name' => 'taxationTypes',
                'id' => null
            ]
        ];
    }

    /**
     * Возвращает массив с данными для отправки в БД
     *
     * @param array $params
     *
     * @return array
     */
    public function getPersistArr(array $params)
    {
        parent::getPersistArr($params);
        $this->persistArr += [
            'taxationTypeId' => !is_null($params['taxationTypeId']) ? $this->getReferenceIdSum($params['taxationTypeId']) : null
        ];
        return $this->persistArr;
    }

    /**
     * Возвращает массив с данными для вывода в шаблоне
     *
     * @param ServerRequestInterface $request
     *
     * @return array
     */
    public function getData(ServerRequestInterface $request)
    {
        return parent::getData($request);
    }

    /**
     * Возвращает массив с данными, преобразованными по формуле
     *
     * @param ServerRequestInterface $request
     *
     * @return array
     */
    public function getFormulaData(ServerRequestInterface $request)
    {
        $id = $request->getAttribute('id');
        $company = $this->entityManager->getRepository(LkCompany::class)->findOneBy(['id' => $id]);
        if ($company instanceof LkCompany) {
            foreach ($this->prepareFormulaArr as $key => $item) {
                if ($item['name'] == 'taxationTypes') {
                    $this->prepareFormulaArr[$key]['id'] = $company->getTaxationTypeId();
                }
            }
        }
        return parent::getFormulaData($request);
    }
}
