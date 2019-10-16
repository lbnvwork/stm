<?php
/**
 * Created by PhpStorm.
 * User: m-lobanov
 * Date: 14.06.19
 * Time: 10:17
 */

namespace Office\Service\DataPrepare;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

class ProductDataPrepareService extends OfficeDataPrepareService
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager);
    }

    public function getProducts(array $queryParams, string $url, QueryBuilder $productsQueryBuilder, int $countItems = 20)
    {
        $prepareData = [];

        $sortType = $queryParams['sort'] ?? 'id';
        $order = $queryParams['order'] ?? 'ASC';
        $orderCheck = in_array(
            $order,
            [
                'ASC',
                'DESC'
            ]
        ) ? $order : 'ASC';
        $orderType = $orderCheck === 'ASC' ? 'DESC' : 'ASC';
        $chevron = $orderType === 'ASC' ? 'down' : 'up';

        $page = isset($queryParams['page']) && $queryParams['page'] > 0 ? $queryParams['page'] : 1;

        $totalRows = (new \Doctrine\ORM\Tools\Pagination\Paginator($productsQueryBuilder->getQuery()))->count();

        $paginator = [
            'countItems' => $totalRows,
            'query' => $queryParams,
            'currentPage' => $page,
            'itemsPerPage' => $countItems,
            'url' => $url
        ];

        $params = [
            'serialNumber',
            'strih',
            'name',
            'count',
            'unitMeasure',
            'section',
            'price',
        ];

        $field = in_array($sortType, $params) ? $sortType : 'id';
        $products = $productsQueryBuilder
            ->setMaxResults($countItems)
            ->orderBy('p.'.$field, $orderCheck)
            ->setFirstResult($countItems * ($page - 1))
            ->getQuery()->getResult();

        $prepareData['sortType'] = $field;
        $prepareData['order'] = $orderType;
        $prepareData['chevron'] = $chevron;
        $prepareData['products'] = $products;
        $prepareData['paginator'] = $paginator;

        return $prepareData;
    }
}
