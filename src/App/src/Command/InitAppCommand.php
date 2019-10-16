<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 04.04.19
 * Time: 11:14
 */

namespace App\Command;

use Auth\Entity\User;
use Auth\Entity\UserHasRole;
use Office\Entity\RefFirmVid;
use Office\Entity\RefKktAdvancedMode;
use Office\Entity\RefKktFfdVersion;
use Office\Entity\RefKktMode;
use Office\Entity\RefPaymentAgentType;
use Office\Entity\RefTaxationType;
use Doctrine\ORM\EntityManager;
use Permission\Entity\Role;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Monolog\Logger;

/**
 * Class InitAppCommand
 * Инициализация приложения
 *
 * @package App\Command
 */
class InitAppCommand extends Command
{
    private $logger;

    private $entityManager;

    /**
     * InitAppCommand constructor.
     *
     * @param Logger $logger
     * @param EntityManager $entityManager
     */
    public function __construct(Logger $logger, EntityManager $entityManager)
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    /**
     * Configures the command
     */
    protected function configure(): void
    {
        $this
            ->setName('app:init')
            ->setDescription('Инициализация приложения');
    }

    /**
     * Выполнение инициализации приложения
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->logger->info('Инициализация приложения');


        $rolesCount = $this->entityManager->getRepository(Role::class)->count([]);
        if (!$rolesCount) {
            $roles = [
                [
                    'id' => '1',
                    'parent_id' => null,
                    'role_name' => 'admin',
                    'title' => 'Админ'
                ],
                [
                    'id' => '2',
                    'parent_id' => null,
                    'role_name' => 'office_manager',
                    'title' => 'Менеджер'
                ],
                [
                    'id' => '3',
                    'parent_id' => null,
                    'role_name' => 'office_cto_user',
                    'title' => 'Пользователь ЦТО'
                ],
                [
                    'id' => '4',
                    'parent_id' => null,
                    'role_name' => 'office_user',
                    'title' => 'Пользователь'
                ],
            ];
            foreach ($roles as $role) {
                $entity = new Role();
                $entity->setRoleName($role['role_name'])
                    ->setTitle($role['title']);
                $this->entityManager->persist($entity);
            }
            $this->logger->info('Роли добавлены');


            $usersCount = $this->entityManager->getRepository(User::class)->count([]);
            if (!$usersCount) {
                $data = [
                    //            'id'             => '1',
                    //            'referral_id'    => null,
                    'email' => 'admin@schetmash.test',
                    //            'password'       => '$2y$10$rBy94JS4SZ7MW6KGnyy9ge4sWt.nt.hE1jsZvBA3bQ8E8QinUJnUe',
                    'firstName' => 'admin',
                    'middleName' => 'admin',
                    'lastName' => 'admin',
                    'phone' => '02',
                ];

                $user = new User();
                foreach ($data as $key => $value) {
                    $method = 'set'.ucfirst($key);
                    $user->{$method}($value);
                }

                $user->getUserRoleManager()->add((new UserHasRole())->setUser($user)->setRoleName('office_manager'));
                $user->getUserRoleManager()->add((new UserHasRole())->setUser($user)->setRoleName('admin'));

                $user->setNewPassword('admin');
                //        $user->setHashKey(str_replace('.', '', uniqid(time(), true)));
                $user->setIsConfirmed(true);

                $this->entityManager->persist($user);

                $this->logger->info('Пользователи добавлены');
            } else {
                $this->logger->warning('Пользователи не добавлены: таблица содержит записи!');
            }
        } else {
            $this->logger->warning('Роли и пользователи не добавлены: таблица ролей содержит записи!');
        }

        //begin посев данных справочника "Система налогообложения"
        $taxationTypeArr = [
            [
                'id' => '1',
                'name' => 'ОСН',
            ],
            [
                'id' => '2',
                'name' => 'УСН доход',
            ],
            [
                'id' => '4',
                'name' => 'УСН доход - расход',
            ],
            [
                'id' => '8',
                'name' => 'ЕНВД',
            ],
            [
                'id' => '16',
                'name' => 'ЕСН',
            ],
            [
                'id' => '32',
                'name' => 'Патентная',
            ]
        ];
        $this->setRefEntity(RefTaxationType::class, $taxationTypeArr);
        //end посев данных справочника "Система налогообложения"

        //begin посев данных справочника "Версия ФФД ККТ"
        $kktFfdVersionArr = [
//            [
//                'name' => '1.0',
//            ],
            [
                'name' => '1.05',
            ],
            [
                'name' => '1.1',
            ]
        ];
        $this->setRefEntity(RefKktFfdVersion::class, $kktFfdVersionArr);
        //end посев данных справочника "Версия ФФД ККТ"

        //begin посев данных справочника "Режим работы"
        $kktModeArr = [
            [
                'id' => '1',
                'name' => 'Шифрование',
            ],
            [
                'id' => '2',
                'name' => 'Автономный режим',
            ],
            [
                'id' => '4',
                'name' => 'Автоматический режим',
            ],
            [
                'id' => '8',
                'name' => 'Применение в сфере услуг',
            ],
            [
                'id' => '16',
                'name' => 'Режим БСО (Режим чеков)',
            ],
        ];
        $this->setRefEntity(RefKktMode::class, $kktModeArr);
        //end посев данных справочника "Режим работы"

        //begin посев данных справочника "Расширенные признаки"
        $KktAdvancedModeArr = [
            [
                'id' => '1',
                'name' => 'Подакцизные товары',
            ],
            [
                'id' => '2',
                'name' => 'Проведение азартной игры',
            ],
            [
                'id' => '4',
                'name' => 'Признак проведения лотереи',
            ],
            [
                'id' => '8',
                'name' => 'Признак установки принтера в автомате',
            ],
        ];
        $this->setRefEntity(RefKktAdvancedMode::class, $KktAdvancedModeArr);
        //end посев данных справочника "Расширенные признаки"

        //begin посев данных справочника "Признак агента"
        $PaymentAgentTypeArr = [
            [
                'id' => '1',
                'name' => 'БАНК. ПЛ. АГЕНТ',
            ],
            [
                'id' => '2',
                'name' => 'БАНК. ПЛ. СУБАГЕНТ',
            ],
            [
                'id' => '4',
                'name' => 'ПЛ. АГЕНТ',
            ],
            [
                'id' => '8',
                'name' => 'ПЛ. СУБАГЕНТ',
            ],
            [
                'id' => '16',
                'name' => 'ПОВЕРЕННЫЙ',
            ],
            [
                'id' => '32',
                'name' => 'КОМИССИОНЕР',
            ],
            [
                'id' => '64',
                'name' => 'АГЕНТ',
            ],
        ];
        $this->setRefEntity(RefPaymentAgentType::class, $PaymentAgentTypeArr);
        //end посев данных справочника "Признак агента"

        //begin посев данных справочника "Тип прошивки"
        $firmVidArr = [
            [
                'name' => 'Универсальная',
            ],
            [
                'name' => 'ЕКР 2102',
            ],
            [
                'name' => 'Миника 1102',
            ],
            [
                'name' => 'Миника 1105',
            ],
        ];
        $this->setRefEntity(RefFirmVid::class, $firmVidArr);
        //end посев данных справочника "Тип прошивки"

        $this->entityManager->flush();
        $this->logger->info('Все данные успешно записаны в БД');
    }

    /**
     * Добавляет справочник
     *
     * @param string $entityClass
     * @param array $rows
     *
     * @throws \Doctrine\ORM\ORMException
     */
    protected function setRefEntity(string $entityClass, array $rows)
    {
        if (class_exists($entityClass)) {
            $persist = false;
            $rowsCount = $this->entityManager->getRepository($entityClass)->count([]);
            if (!$rowsCount) {
                foreach ($rows as $row) {
                    $entity = new $entityClass();
                    foreach ($row as $propertyKey => $property) {
                        if (method_exists($entityClass, 'set'.ucfirst($propertyKey))) {
                            $method = 'set'.ucfirst($propertyKey);
                            $entity->{$method}($property);
                            $persist = true;
                        }
                    }
                    if ($persist) {
                        $this->entityManager->persist($entity);
                    } else {
                        $this->logger->warning('Данные для '.$entityClass.' не добавлены: нет соответствий в массиве данных!');
                    }
                }
                $this->logger->info('Данные для '.$entityClass.' добавлены');
            } else {
                $this->logger->warning('Данные для '.$entityClass.' не добавлены: таблица содержит записи!');
            }
        } else {
            $this->logger->warning('Класс '.$entityClass.' не найден!');
        }
    }
}
