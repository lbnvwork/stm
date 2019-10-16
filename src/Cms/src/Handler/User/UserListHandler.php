<?php
/**
 * Created by PhpStorm.
 * User: Maksim
 * Date: 20.05.2019
 * Time: 21:31
 */

namespace Cms\Handler\User;

use App\Helper\UrlHelper;
use Auth\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use App\Service\DataPrepare\DataPrepareService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Authentication\UserRepositoryInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\HtmlResponse;

/**
 * Class UserListHandler
 * Выводит список пользователей
 *
 * @package Cms\Handler\User
 */
class UserListHandler implements MiddlewareInterface
{
    public const TEMPLATE_NAME = 'cms::/user/list';

    public const COUNT_ITEMS = 20;

    private $template;

    private $entityManager;

    private $urlHelper;

    private $dataPrepareService;

    private $auth;

    /**
     * UserListHandler constructor.
     *
     * @param EntityManager $entityManager
     * @param TemplateRendererInterface|null $template
     * @param UrlHelper $urlHelper
     * @param DataPrepareService $dataPrepareService
     * @param UserRepositoryInterface $auth
     */
    public function __construct(
        EntityManager $entityManager,
        TemplateRendererInterface $template = null,
        UrlHelper $urlHelper,
        DataPrepareService $dataPrepareService,
        UserRepositoryInterface $auth
    ) {
        $this->template = $template;
        $this->entityManager = $entityManager;
        $this->urlHelper = $urlHelper;
        $this->auth = $auth;
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
        /** @var User $user */
        $user = $request->getAttribute(UserInterface::class);
        $queryParams = $request->getQueryParams();
        $data = $this->auth->getUsersWithOrderBuilder($queryParams);
        /** @var QueryBuilder $qb */
        $qb = $data['qb'];
        $queryParams = $data['queryParams'];
        $data = $this->dataPrepareService->getUsers(
            $queryParams,
            $this->urlHelper->generate('admin.user.list'),
            $qb,
            self::COUNT_ITEMS
        );
        return new HtmlResponse(
            $this->template->render(
                self::TEMPLATE_NAME,
                [
                    'layout' => 'office::office',
                    'sortType' => $data['sortType'],
                    'order' => $data['order'],
                    'chevron' => $data['chevron'],
                    'users' => $data['users'],
                    'roles' => $data['roles'],
                    'paginator' => $data['paginator']
                ]
            )
        );
    }
}
