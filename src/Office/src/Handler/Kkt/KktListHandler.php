<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 05.04.19
 * Time: 11:36
 */

namespace Office\Handler\Kkt;

use App\Helper\UrlHelper;
use Auth\Entity\User;
use Doctrine\ORM\EntityManager;
use Office\Entity\LkCompany;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class KktListHandler
 * Выводит список касс
 *
 * @package Office\Handler\Kkt
 */
class KktListHandler implements MiddlewareInterface
{
    public const TEMPLATE_NAME = 'office::/kkt/list';

    private $template;

    private $entityManager;

    private $urlHelper;

    /**
     * KktListHandler constructor.
     *
     * @param EntityManager $entityManager
     * @param TemplateRendererInterface|null $template
     * @param UrlHelper $urlHelper
     */
    public function __construct(EntityManager $entityManager, TemplateRendererInterface $template = null, UrlHelper $urlHelper)
    {
        $this->template = $template;
        $this->entityManager = $entityManager;
        $this->urlHelper = $urlHelper;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var User $user */
        $user = $request->getAttribute(UserInterface::class);
        $kktArr = [];
        $companies = $this->entityManager->getRepository(LkCompany::class)->getEntitiesByUser($user);
        /** @var LkCompany $company */
        foreach ($companies as $company) {
            $kkts = [];
            foreach ($company->getKkt()->filter(
                function ($kkt) {
                    return !$kkt->getIsDeleted();
                }
            ) as $kkt) {
                $kkts[] = $kkt;
            }
            $kktArr[$company->getId()] = [
                'company' => $company,
                'kkts' => $kkts
            ];
        }
        return new HtmlResponse(
            $this->template->render(
                self::TEMPLATE_NAME,
                [
                    'layout' => 'office::office',
                    'kktArr' => $kktArr
                ]
            )
        );
    }
}
