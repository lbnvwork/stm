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
use Office\Entity\LkKkt;
use Office\Entity\RefFirmVid;
use Office\Service\DataPrepareService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class FirmwareItemHandler
 * Выводит форму редактирования прошивки
 *
 * @package Office\Handler\Firmware
 */
class FirmwareItemHandler implements MiddlewareInterface
{
    public const TEMPLATE_NAME = 'office::/firmware/item';

    private $template;

    private $entityManager;

    private $urlHelper;

    private $dataPrepareService;

    /**
     * EditAction constructor.
     *
     * @param EntityManager $entityManager
     * @param TemplateRendererInterface|null $template
     * @param UrlHelper $urlHelper
     */
    public function __construct(
        EntityManager $entityManager,
        TemplateRendererInterface $template = null,
        UrlHelper $urlHelper,
        DataPrepareService $dataPrepareService
    ) {

        $this->template = $template;
        $this->entityManager = $entityManager;
        $this->urlHelper = $urlHelper;
        $this->dataPrepareService = $dataPrepareService;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $flashMessage = $request->getAttribute(FlashMessage::class);
        $params = $request->getParsedBody();
        /** @var User $user */
        $user = $request->getAttribute(UserInterface::class);

        $id = $request->getAttribute('id');

        /** @var LkFirm $firmware */
        $firmware = $this->entityManager->getRepository(LkFirm::class)->findOneBy(['id' => $id]);
        $persistArr = [];
        $prepareRefArr = [
            [
                'name' => 'vid',
                'class' => RefFirmVid::class,
                'id' => $firmware->getVid()->getId()
            ],
        ];
        $data = $this->dataPrepareService->prepareRefDada($prepareRefArr);
        return new HtmlResponse(
            $this->template->render(
                self::TEMPLATE_NAME,
                [
                    'layout' => 'office::office',
                    'firmware' => $firmware,
                    'data' => $data,
                    //                'testFunction'=>$this->testFunc
                ]
            )
        );
    }

    public function testFunction()
    {
        echo 'test';
    }
}
