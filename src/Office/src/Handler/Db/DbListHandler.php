<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 05.04.19
 * Time: 10:56
 */

namespace Office\Handler\Db;

use App\Helper\UrlHelper;
use Auth\Entity\User;
use Doctrine\ORM\EntityManager;
use Office\Entity\LkDb;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class DbListHandler
 * Выводит список товаров
 *
 * @package Office\Handler\Db
 */
class DbListHandler implements MiddlewareInterface
{
    public const TEMPLATE_NAME = 'office::/db/list';

    private $template;

    private $entityManager;

    private $urlHelper;

    /**
     * DbListHandler constructor.
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
        $databases = $this->entityManager->getRepository(LkDb::class)->getEntitiesByUser($user);
        return new HtmlResponse(
            $this->template->render(
                self::TEMPLATE_NAME,
                [
                    'layout' => 'office::office',
                    'databases' => $databases
                ]
            )
        );
    }
}
