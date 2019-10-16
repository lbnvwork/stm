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
use Doctrine\ORM\EntityManager;
use Office\Entity\LkFirm;
use Office\Entity\RefFirmVid;
use App\Service\DataPrepareService;
use Office\Service\SaveFileService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Expressive\Authentication\UserInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;

/**
 * Class FirmwareEditHandler
 * Редактирует прошивку
 *
 * @package Office\Handler\Firmware
 */
class FirmwareEditHandler implements MiddlewareInterface
{
    public const TEMPLATE_NAME = 'office::/firmware/list';

    private $entityManager;

    private $urlHelper;

    private $dataPrepareService;

    private $saveFileService;

    /**
     * FirmwareEditHandler constructor.
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
        $user = $request->getAttribute(UserInterface::class);
        $id = $request->getAttribute('id');

        /** @var LkFirm $firmware */
        $firmware = $this->entityManager->getRepository(LkFirm::class)->findOneBy(['id' => $id]);

        if (!$this->dataPrepareService->checkForUser($user, $firmware)) {
            return new Response\HtmlResponse($this->template->render('error::404'), 404);
        };

        $params = $request->getParsedBody();
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
        if ($firmware->getFilename() != null && is_file(ROOT_PATH.'public/'.$firmware->getFilename())) {
            unlink(ROOT_PATH.'public/'.$firmware->getFilename());
        }
        $this->saveFileService->saveFile($request, $firmware);
        $this->entityManager->persist($firmware);
        $this->entityManager->flush();
        $flashMessage->addSuccessMessage('Данные успешно изменены');
        return new Response\RedirectResponse($this->urlHelper->generate('office.firmware.item', ['id' => $firmware->getId()]));
    }
}
