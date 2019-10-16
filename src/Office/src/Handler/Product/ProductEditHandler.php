<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 24.04.19
 * Time: 11:16
 */

namespace Office\Handler\Product;

use App\Helper\UrlHelper;
use App\Service\FlashMessage;
use Doctrine\ORM\EntityManager;
use Office\Entity\LkDb;
use Office\Entity\LkProduct;
use Office\Service\DataPrepare\ProductDataPrepareService;
use Office\Service\Validator\ProductValidatorService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response;
use Zend\Expressive\Authentication\UserInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * Class ProductEditHandler
 * Изменяет продукт
 *
 * @package Office\Handler\Product
 */
class ProductEditHandler implements MiddlewareInterface
{
    private $entityManager;

    private $urlHelper;

    private $dataPrepareService;

    private $validator;

    /**
     * ProductEditHandler constructor.
     *
     * @param EntityManager $entityManager
     * @param UrlHelper $urlHelper
     * @param ProductDataPrepareService $dataPrepareService
     * @param ProductValidatorService $validator
     */
    public function __construct(
        EntityManager $entityManager,
        TemplateRendererInterface $template = null,
        UrlHelper $urlHelper,
        ProductDataPrepareService $dataPrepareService,
        ProductValidatorService $validator
    ) {
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
        //begin сбор
        /** @var LkProduct $product */
        $product = $this->entityManager->getRepository(LkProduct::class)->findOneBy(['id' => $request->getAttribute('pid')]);
        $flashMessage = $request->getAttribute(FlashMessage::class);
        $user = $request->getAttribute(UserInterface::class);
        $id = $request->getAttribute('id');
        $params = $request->getParsedBody();
        /** @var LkDb $db */
        $db = $this->entityManager->getRepository(LkDb::class)->findOneBy(['id' => $id]);
        //end сбор

        //begin проверка
        if (!$this->dataPrepareService->checkForUser($user, $db)) {
            return new Response\HtmlResponse($this->template->render('error::404'), 404);
        };
        $validator = $this->validator->check($request);
        if (!$validator->isValid()) {
            $allErrorMessages = $validator->getMessages()->getAllErrorMessagesArr();
            return new Response\JsonResponse(['error' => $allErrorMessages]);
        }
        //end проверка
        //begin обработка
        $persistArr = [
            'price' => round($params['price'], 2),
            'count' => round($params['count'], 3),
        ];
        $this->entityManager->getRepository(LkProduct::class)->setEntityData($params, $product, $persistArr);
        $this->entityManager->persist($product);
        $this->entityManager->flush();
        $flashMessage->addSuccessMessage('Данные сохранены');
        //end обработка

        return new Response\JsonResponse(['url' => $this->urlHelper->generate('office.product.list', ['id' => $id])]);
    }
}
