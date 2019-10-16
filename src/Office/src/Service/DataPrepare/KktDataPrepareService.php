<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 29.05.19
 * Time: 9:37
 */

namespace Office\Service\DataPrepare;

use Auth\Entity\User;
use Doctrine\ORM\EntityManager;
use Office\Entity\LkCompany;
use Office\Entity\LkDb;
use Office\Entity\LkKkt;
use Office\Entity\RefKktAdvancedMode;
use Office\Entity\RefKktFfdVersion;
use Office\Entity\RefKktMode;
use Office\Entity\RefPaymentAgentType;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class KktDataPrepareService
 * Подготавливает данные о ККТ
 *
 * @package Office\Service\DataPrepare
 */
class KktDataPrepareService extends OfficeDataPrepareService
{
    /**
     * KktDataPrepareService constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager);
        $this->persistRefArr = [
            [
                'name' => 'company',
                'class' => LkCompany::class,
                'id' => null
            ],
            [
                'name' => 'db',
                'class' => LkDb::class,
                'id' => null
            ],
//            [
//                'name' => 'firm',
//                'class' => LkFirm::class,
//                'id' => isset($params['firm']) ?? $params['firm']
//            ],
            [
                'name' => 'ffdKktVersion',
                'class' => RefKktFfdVersion::class,
                'id' => null
            ],
        ];
        $this->persistFormulaArr = [
            [
                'name' => 'kktMode',
                'arr' => null
            ],
            [
                'name' => 'kktAdvancedMode',
                'arr' => null
            ],
            [
                'name' => 'paymentAgentType',
                'arr' => null
            ],
        ];

        $this->prepareRefArr = [
            [
                'name' => 'kktFfdVersions',
                'class' => RefKktFfdVersion::class
            ],
            [
                'name' => 'kktMode',
                'class' => RefKktMode::class
            ],
            [
                'name' => 'kktAdvancedMode',
                'class' => RefKktAdvancedMode::class
            ],
            [
                'name' => 'paymentAgentType',
                'class' => RefPaymentAgentType::class
            ]
        ];

        $this->prepareLkArr = [
            [
                'name' => 'companies',
                'class' => LkCompany::class
            ],
            [
                'name' => 'productRanges',
                'class' => LkDb::class,
            ],
//            [
//                'name' => 'firmwares',
//                'class' => LkFirm::class,
//            ]
        ];
        $this->prepareFormulaArr = [
            [
                'name' => 'kktMode',
                'id' => null
            ],
            [
                'name' => 'kktAdvancedMode',
                'id' => null
            ],
            [
                'name' => 'paymentAgentType',
                'id' => null
            ]
        ];
    }

    /**
     * Возвращает массив данных для добавления в БД
     *
     * @param array $params
     *
     * @return array
     * @throws \Exception
     */
    public function getPersistArr(array $params)
    {
        parent::getPersistArr($params);
        $this->persistArr += [
            'datetime' => !$params['datetime'] == null ? new \DateTime($params['datetime']) : $params['datetime']
        ];
        $this->persistArr += [
            'synchronization' => isset($params['synchronization']) ? true : false
        ];
        return $this->persistArr;
    }

    /**
     * Возвращает массив данных для вывода в шаблоне
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
     * Возвращает массив данных, преобразованных по формуле, для вывода в шаблоне
     *
     * @param ServerRequestInterface $request
     *
     * @return array
     */
    public function getFormulaData(ServerRequestInterface $request)
    {
        $id = $request->getAttribute('id');
        /** @var LkKkt $kkt */
        $kkt = $this->entityManager->getRepository(LkKkt::class)->findOneBy(['id' => $id]);
        foreach ($this->prepareFormulaArr as $key => $item) {
            switch ($item['name']) {
                case 'kktMode':
                    $this->prepareFormulaArr[$key]['id'] = $kkt->getKktMode();
                    break;
                case 'kktAdvancedMode':
                    $this->prepareFormulaArr[$key]['id'] = $kkt->getKktAdvancedMode();
                    break;
                case 'paymentAgentType':
                    $this->prepareFormulaArr[$key]['id'] = $kkt->getPaymentAgentType();
                    break;
                default:
                    continue;
            }
        }
        return parent::getFormulaData($request);
    }

    public function unsetCtoUsersCompanies(array $companies, User $currUser): array
    {
        /** @var LkCompany $company */
        foreach ($companies as $key => $company) {
            $isPropertyCtoUser = false;
            $companyUsers = $company->getUser();
            foreach ($companyUsers as $companyUser) {
                if ($companyUser->getUserRoleManager()->offsetExists('office_cto_user') && $companyUser != $currUser) {
                    $isPropertyCtoUser = true;
                    break;
                }
            }
            if ($isPropertyCtoUser) {
                unset($companies[$key]);
            }
        }
        return $companies;
    }
}
