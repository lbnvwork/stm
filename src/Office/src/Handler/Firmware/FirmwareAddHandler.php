<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 05.04.19
 * Time: 11:14
 */

namespace Office\Handler\Firmware;

use App\Helper\UrlHelper;
use App\Service\FlashMessage;
use Auth\Entity\User;
use Doctrine\ORM\EntityManager;
use Office\Entity\LkFirm;
use Office\Entity\RefFirmVid;
use Office\Service\DataPrepareService;
use Office\Service\SaveFileService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Expressive\Authentication\UserInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;

/**
 * Class FirmwareAddHandler
 * Добавляет прошивку
 *
 * @package Office\Handler\Firmware
 */
class FirmwareAddHandler implements MiddlewareInterface
{
    private $entityManager;

    private $urlHelper;

    private $dataPrepareService;

    private $saveFileService;

    /**
     * FirmwareAddHandler constructor.
     *
     * @param EntityManager $entityManager
     * @param UrlHelper $urlHelper
     * @param DataPrepareService $dataPrepareService
     * @param SaveFileService $saveFileService
     */
    public function __construct(EntityManager $entityManager, UrlHelper $urlHelper, DataPrepareService $dataPrepareService, SaveFileService $saveFileService)
    {
        $this->entityManager = $entityManager;
        $this->urlHelper = $urlHelper;
        $this->dataPrepareService = $dataPrepareService;
        $this->saveFileService = $saveFileService;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $flashMessage = $request->getAttribute(FlashMessage::class);
        $params = $request->getParsedBody();
        /** @var User $user */
        $user = $request->getAttribute(UserInterface::class);
        $firmware = new LkFirm();
        $persistArr = [];
        $prepareRefArr = [
            [
                'name' => 'vid',
                'class' => RefFirmVid::class,
                'id' => $params['vid']
            ],
        ];
        $persistArr += $this->dataPrepareService->getPersistRefArr($prepareRefArr);
        $this->entityManager->getRepository(LkFirm::class)->setEntityData($params, $firmware, $persistArr);

        $serialNumber = $this->entityManager->getRepository(LkFirm::class)->getSerialNumber(LkFirm::class, $user);
        $firmware->setSerialNumber($serialNumber);
        $firmware->setIdByFormula($user->getId() * 100 + $serialNumber);
        $firmware->setUser($user);

        $res = $this->saveFileService->saveFile($request, $firmware);
        if ($res) {
            $this->entityManager->persist($firmware);
            $this->entityManager->flush();
            $flashMessage->addSuccessMessage('Файл загружен');
            return new Response\RedirectResponse($this->urlHelper->generate('office.firmware.list'));
        } else {
            return new Response\RedirectResponse($this->urlHelper->generate('office.firmware.new'));
        }
    }
}
