<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 05.04.19
 * Time: 10:57
 */

namespace Office\Handler\Db;

use App\Helper\UrlHelper;
use App\Service\FlashMessage;
use Doctrine\ORM\EntityManager;
use Office\Entity\LkDb;
use Office\Service\DataPrepare\OfficeDataPrepareService;
use Office\Service\Validator\DbValidatorService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;

/**
 * Class DbAddHandler
 * Добавляет Базу товаров
 *
 * @package Office\Handler\Db
 */
class DbAddHandler implements MiddlewareInterface
{
    public const TEMPLATE_NAME = 'office::/db/list';

    private $template;

    private $entityManager;

    private $urlHelper;

    private $validator;

    /**
     * DbAddHandler constructor.
     *
     * @param EntityManager $entityManager
     * @param TemplateRendererInterface|null $template
     * @param UrlHelper $urlHelper
     */
    public function __construct(
        EntityManager $entityManager,
        TemplateRendererInterface $template = null,
        UrlHelper $urlHelper,
        OfficeDataPrepareService $dataPrepareService,
        DbValidatorService $validator
    ) {

        $this->template = $template;
        $this->entityManager = $entityManager;
        $this->urlHelper = $urlHelper;
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
        $flashMessage = $request->getAttribute(FlashMessage::class);
        $user = $request->getAttribute(UserInterface::class);
        $db = new LkDb();
        $db->setUser($user);
        $params = $request->getParsedBody();
        $validator = $this->validator->check($request);
        $params = $validator->setNullParams($params);
        if (!$validator->isValid()) {
            $allErrorMessages = $validator->getMessages()->getAllErrorMessagesArr();
            return new Response\JsonResponse(['error' => $allErrorMessages]);
        }
        $this->entityManager->getRepository(LkDb::class)->setEntityData($params, $db);
        $serialNumber = $this->entityManager->getRepository(LkDb::class)->getSerialNumber(LkDb::class, $user);
        $db->setSerialNumber($serialNumber);
        $this->entityManager->flush();
        $flashMessage->addSuccessMessage('Данные сохранены');
        return new Response\JsonResponse(['url' => $this->urlHelper->generate('office.db.list')]);
    }
}
