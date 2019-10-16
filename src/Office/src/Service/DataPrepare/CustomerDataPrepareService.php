<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 31.05.19
 * Time: 11:27
 */

namespace Office\Service\DataPrepare;

use Doctrine\ORM\EntityManager;
use Office\Entity\LkCompany;
use Psr\Http\Message\ServerRequestInterface;

class CustomerDataPrepareService extends OfficeDataPrepareService
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager);
        $this->prepareLkArr = [
            [
                'name' => 'companies',
                'class' => LkCompany::class
            ],
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
}
