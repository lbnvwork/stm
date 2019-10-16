<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 06.05.19
 * Time: 16:24
 */

namespace Office\Handler\Product;

use App\Helper\UrlHelper;
use App\Service\FlashMessage;
use App\Service\Validator\Parameter;
use Doctrine\ORM\EntityManager;
use Office\Entity\LkDb;
use Office\Entity\LkProduct;
use Office\Service\DataPrepare\OfficeDataPrepareService;
use Office\Service\SaveFileService;
use Office\Service\SprImport\SprImportService;
use Office\Service\Validator\ProductValidatorService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Expressive\Authentication\UserInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;

/**
 * Class ProductImportHandler
 * Импорт списка продуктов из таблицы
 *
 * @package Office\Handler\Product
 */
class ProductImportHandler implements MiddlewareInterface
{
    private const TABLE_RANGE = 'B2:G';
    private const STRIH_INDEX = 0;
    private const NAME_INDEX = 1;
    private const SECTION_INDEX = 2;
    private const COUNT_INDEX = 3;
    private const UNIT_MEASURE_INDEX = 4;
    private const PRICE_INDEX = 5;

    private const USER_MULTIPLIER = 1000000;
    private const SERIAL_NUMBER_MULTIPLIER = 100000;

    private $entityManager;

    private $urlHelper;

    private $dataPrepareService;

    private $saveFileService;

    private $sprImportService;

    private $validator;

    /**
     * ProductImportHandler constructor.
     *
     * @param EntityManager $entityManager
     * @param UrlHelper $urlHelper
     * @param OfficeDataPrepareService $dataPrepareService
     * @param SaveFileService $saveFileService
     * @param SprImportService $sprImportService
     * @param ProductValidatorService $validator
     */
    public function __construct(
        EntityManager $entityManager,
        UrlHelper $urlHelper,
        OfficeDataPrepareService $dataPrepareService,
        SaveFileService $saveFileService,
        SprImportService $sprImportService,
        ProductValidatorService $validator
    ) {

        $this->entityManager = $entityManager;
        $this->urlHelper = $urlHelper;
        $this->dataPrepareService = $dataPrepareService;
        $this->saveFileService = $saveFileService;
        $this->sprImportService = $sprImportService;
        $this->validator = $validator;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        //begin сбор данных

        $flashMessage = $request->getAttribute(FlashMessage::class);
        $user = $request->getAttribute(UserInterface::class);
        $id = $request->getAttribute('id');
        /** @var LkDb $db */
        $db = $this->entityManager->getRepository(LkDb::class)->findOneBy(['id' => $id]);

        //begin проверка на пользователя
        if (!$this->dataPrepareService->checkForUser($user, $db)) {
            return new Response\HtmlResponse($this->template->render('error::404'), 404);
        };
        //end проверка на пользователя

        $allowedFormatDoc = [
//            'xls' => [
//                'application/vnd.ms-excel',
//            ],
            'xlsx' => [
                'application/vnd.ms-office',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/octet-stream'
            ],
        ];
        $file = $this->saveFileService->getFile($request, $allowedFormatDoc, 'file');
        if ($file === null) {
            return new Response\JsonResponse(['error' => ['Неверный формат файла! Пожалуйста, добавьте файл xlsx, как в примере.']]);
        }
        $table = $this->sprImportService->getTableArr($file, self::TABLE_RANGE);
        //begin проверка на пустые строки

        //end проверка на пустые строки

        if ($table === null) {
            return new Response\JsonResponse(['error' => ['Ошибка загрузки данных из таблицы! Пожалуйста, заполните таблицу в соответствии с примером.']]);
        }
        $serialNumber = $this->entityManager->getRepository(LkProduct::class)->getProductSerialNumber($db);

        $products = $this->entityManager->getRepository(LkProduct::class)->findBy(['db' => $db]);

        if ($db->getMaxCount() - count($products) < count($table)) {
            return new Response\JsonResponse(['error' => ['Товары не добавлены! Превышено максимальное количество товаров в базе.']]);
        }

        //end сбор данных

        //begin обработка данных


        //begin проверка данных, запись лога, добавление только если все данные валидные

        $log = [];
        $strihArr = $this->entityManager->getRepository(LkProduct::class)->createQueryBuilder('p', 'p.strih')
            ->select('p.strih')
            ->where('p.db = :db')
            ->setParameter('db', $db)
            ->getQuery()
            ->getResult();
        $strihArrSimple = array_keys($strihArr);
        foreach ($table as $rowKey => $row) {
            $paramsArr = [
                'strih' => $row[self::STRIH_INDEX],
                'name' => $row[self::NAME_INDEX],
                'section' => $row[self::SECTION_INDEX],
                'count' => $row[self::COUNT_INDEX],
                'unitMeasure' => $row[self::UNIT_MEASURE_INDEX],
                'price' => $row[self::PRICE_INDEX],
            ];

            //begin проверка на уникальный штрихкод
            if (in_array($row[self::STRIH_INDEX], $strihArrSimple) || empty($row[self::STRIH_INDEX])) {
                $log[$rowKey + 2] = ['Товар с таким штрихкодом уже присутствует в базе товаров!'];
                continue;
            }
            $strihArrSimple[] = $row[self::STRIH_INDEX];
            //end проверка на уникальный штрихкод
            $this->validator->check($request, $paramsArr);
            if (!$this->validator->isValid()) {
                $log[$rowKey + 2] = $this->validator->getMessages()->getAllErrorMessagesArr();
            }
        }
        if (empty($log)) {
            foreach ($table as $row) {
                /** @var LkProduct $product */
                $product = (new LkProduct())
                    ->setStrih($row[self::STRIH_INDEX] ?? null)
                    ->setName($row[self::NAME_INDEX] ?? null)
                    ->setSection((int)$row[self::SECTION_INDEX] ?? null)
                    ->setUnitMeasure($row[self::UNIT_MEASURE_INDEX] ?? null)
                    ->setPrice(round((float)$row[self::PRICE_INDEX], 2))
                    ->setCount(round((float)$row[self::COUNT_INDEX], 3) ?? null);
                $product->setSerialNumber($serialNumber);
                //(id_user * 1000000) + IDdatabase*100000 + Kode
                $product->setIdByFormula($user->getId() * self::USER_MULTIPLIER + $db->getSerialNumber() * self::SERIAL_NUMBER_MULTIPLIER + $serialNumber);
                $product->setDb($db);
                $this->entityManager->persist($product);
                $serialNumber++;
            }
            $this->entityManager->flush();
            $flashMessage->addSuccessMessage('Данные сохранены');
            return new Response\JsonResponse(['url' => $this->urlHelper->generate('office.product.list', ['id' => $id])]);
        } else {
            //$flashMessage->addErrorMessage('Данные не сохранены! В таблице содержатся ошибки.');
            return new Response\JsonResponse(
                [
                    'log' => $log,
                    'error' => ['Данные не сохранены! В таблице содержатся ошибки.']
                ]
            );
        }
        //end проверка данных, запись лога, добавление только если все данные валидные


        //begin добавление данных, подсчет и исключение невалидных строк
//        $counters = [
//            'strih' => 0,
//            'name' => 0,
//            'section' => 0,
//            'count' => 0,
//            'unitMeasure' => 0,
//            'price' => 0,
//        ];
//        $strihArr = $this->entityManager->getRepository(LkProduct::class)->createQueryBuilder('p', 'p.strih')
//            ->select('p.strih')
//            ->where('p.db = :db')
//            ->setParameter('db', $db)
//            ->getQuery()
//            ->getResult();
//        $strihArrSimple = array_keys($strihArr);
//
//
//        foreach ($table as $row) {
//            $paramsArr = [
//                'strih' => $row[self::STRIH_INDEX],
//                'name' => $row[self::NAME_INDEX],
//                'section' => $row[self::SECTION_INDEX],
//                'count' => $row[self::COUNT_INDEX],
//                'unitMeasure' => $row[self::UNIT_MEASURE_INDEX],
//                'price' => $row[self::PRICE_INDEX],
//            ];
//
//            //begin проверка на уникальный штрихкод
//            if (in_array($row[self::STRIH_INDEX], $strihArrSimple) || empty($row[self::STRIH_INDEX])) {
//                $counters['strih']++;
//                continue;
//            }
//            $strihArrSimple[] = $row[self::STRIH_INDEX];
//            //end проверка на уникальный штрихкод
//            $this->validator->check($request, $paramsArr);
//            if (!$this->validator->isValid()) {
//                $validatorParams = $this->validator->getParameters();
//                /** @var Parameter $parameter */
//                foreach ($validatorParams as $parameter) {
//                    if ($parameter->getValid() == false && $parameter->getName() != 'strih') {
//                        if (array_key_exists($parameter->getName(), $counters)) {
//                            $counters[$parameter->getName()]++;
//                            break;
//                        }
//                    }
//                }
//                continue;
//            }
//
//            /** @var LkProduct $product */
//            $product = (new LkProduct())
//                ->setStrih($row[self::STRIH_INDEX] ?? null)
//                ->setName($row[self::NAME_INDEX] ?? null)
//                ->setSection((int)$row[self::SECTION_INDEX] ?? null)
//                ->setUnitMeasure($row[self::UNIT_MEASURE_INDEX] ?? null)
//                ->setPrice(round((float)$row[self::PRICE_INDEX], 2))
//                ->setCount(round((float)$row[self::COUNT_INDEX], 3) ?? null);
//            $product->setSerialNumber($serialNumber);
//            //(id_user * 1000000) + IDdatabase*100000 + Kode
//            $product->setIdByFormula($user->getId() * self::USER_MULTIPLIER + $db->getSerialNumber() * self::SERIAL_NUMBER_MULTIPLIER + $serialNumber);
//            $product->setDb($db);
//            $this->entityManager->persist($product);
//            $serialNumber++;
//        }
//
//        $this->entityManager->flush();
//        //end обработка данных
//
//        foreach ($counters as $counterKey => $counterValue) {
//            if ($counterValue > 0) {
//                $flashMessage->addWarningMessage(
//                    'Товары с неверным полем '.
//                    $this->validator->findParameterByName($counterKey)->getTitle().
//                    ' в количестве '.$counterValue.' не добавлены в список!'
//                );
//            }
//        }
//        $flashMessage->addSuccessMessage('Данные сохранены');
//        return new Response\JsonResponse(['url' => $this->urlHelper->generate('office.product.list', ['id' => $id])]);
        //end добавление данных, подсчет и исключение невалидных строк
    }
}
