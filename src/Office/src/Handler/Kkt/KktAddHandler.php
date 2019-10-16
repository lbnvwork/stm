<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 05.04.19
 * Time: 11:37
 */

namespace Office\Handler\Kkt;

use App\Helper\UrlHelper;
use App\Service\FlashMessage;
use Auth\Entity\User;
use Doctrine\ORM\EntityManager;
use Office\Entity\LkCompany;
use Office\Entity\LkKkt;
use Office\Service\DataPrepare\KktDataPrepareService;
use Office\Service\Validator\KktValidatorService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;

/**
 * Class KktAddHandler
 * Добавляет кассу
 *
 * @package Office\Handler\Kkt
 */
class KktAddHandler implements MiddlewareInterface
{
    public const TEMPLATE_NAME = 'office::/kkt/list';

    private $template;

    private $entityManager;

    private $urlHelper;

    private $dataPrepareService;

    private $validator;

    /**
     * KktAddHandler constructor.
     *
     * @param EntityManager $entityManager
     * @param TemplateRendererInterface|null $template
     * @param UrlHelper $urlHelper
     * @param KktDataPrepareService $dataPrepareService
     * @param KktValidatorService $validator
     */
    public function __construct(
        EntityManager $entityManager,
        TemplateRendererInterface $template = null,
        UrlHelper $urlHelper,
        KktDataPrepareService $dataPrepareService,
        KktValidatorService $validator
    ) {
        $this->template = $template;
        $this->entityManager = $entityManager;
        $this->urlHelper = $urlHelper;
        $this->dataPrepareService = $dataPrepareService;
        $this->validator = $validator;
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
        //begin сбор данных
        $flashMessage = $request->getAttribute(FlashMessage::class);
        $params = $request->getParsedBody();
        /** @var User $user */
        $user = $request->getAttribute(UserInterface::class);
        $kkt = new LkKkt();
        //end сбор данных

        //begin проверка данных
        $validator = $this->validator->check($request);
        //$params = $validator->setNullParams($params);
        if (!$validator->isValid()) {
            $allErrorMessages = $validator->getMessages()->getAllErrorMessagesArr();
            return new Response\JsonResponse(['error' => $allErrorMessages]);
        }
        $res = $this->entityManager->getRepository(LkKkt::class)->checkKktDuplication((int)$params['seria'], substr($params['machineNumber'], 0, 7));
        if ($params['seria'] == null || $params['machineNumber'] == null || $res != null) {
            return new Response\JsonResponse(['error' => ['ККТ с таким заводским номером и серией уже зарегистрирована']]);
        }
        if (!$this->dataPrepareService->checkForUser($user, $this->entityManager->find(LkCompany::class, $params['company']))) {
            return new Response\JsonResponse(['error' => ['Компания не найдена']]);
        };
        //end проверка данных

        //begin обработка данных
        $persistArr = $this->dataPrepareService->getPersistArr($params);
        $this->entityManager->getRepository(LkKkt::class)->setEntityData($params, $kkt, $persistArr);
        $serialNumber = $this->entityManager->getRepository(LkKkt::class)->getSerialNumber(LkKkt::class, $user, 'company');
        $kkt->setSerialNumber($serialNumber);
        $kkt->setIdByFormula($user->getId() * 1000000 + $serialNumber);
        $this->entityManager->persist($kkt);
        $this->entityManager->flush();

        $flashMessage->addSuccessMessage('Данные сохранены');
        //end обработка данных

        return new Response\JsonResponse(['url' => $this->urlHelper->generate('office.kkt.list')]);
    }
}
