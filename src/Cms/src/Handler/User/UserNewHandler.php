<?php
/**
 * Created by PhpStorm.
 * User: Maksim
 * Date: 20.05.2019
 * Time: 21:32
 */

namespace Cms\Handler\User;

use App\Helper\UrlHelper;
use Doctrine\ORM\EntityManager;
use App\Service\DataPrepare\DataPrepareService;
use Permission\Entity\Role;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class UserNewHandler
 * Выводит форму добавления пользователя
 *
 * @package Cms\Handler\User
 */
class UserNewHandler implements MiddlewareInterface
{
    public const TEMPLATE_NAME = 'cms::/user/item';

    private $template;

    private $entityManager;

    private $urlHelper;

    private $dataPrepareService;

    /**
     * UserNewHandler constructor.
     *
     * @param EntityManager $entityManager
     * @param TemplateRendererInterface|null $template
     * @param UrlHelper $urlHelper
     * @param DataPrepareService $dataPrepareService
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
        $authUser = $request->getAttribute(UserInterface::class);
        $roles = $this->entityManager->getRepository(Role::class)->findAll();
        $userRoles = [];
        /** @var Role $role */
        foreach ($roles as $key => $role) {
            if ($role->getRoleName() == 'admin' || $role->getRoleName() == 'office_manager') {
                if (!$authUser->getUserRoleManager()->offsetExists('admin')) {
                    unset($roles[$key]);
                }
            }
        }
        return new HtmlResponse(
            $this->template->render(
                self::TEMPLATE_NAME,
                [
                    'layout' => 'cms::cms',
                    'roles' => $roles
                ]
            )
        );
    }
}
