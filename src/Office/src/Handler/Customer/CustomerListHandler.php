<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 08.05.19
 * Time: 11:55
 */

namespace Office\Handler\Customer;

use App\Helper\UrlHelper;
use Auth\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Office\Service\DataPrepare\OfficeDataPrepareService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Authentication\UserRepositoryInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\HtmlResponse;

/**
 * Class CustomerListHandler
 * Выводит список клиентов
 *
 * @package Office\Handler\Customer
 */
class CustomerListHandler implements MiddlewareInterface
{
    public const TEMPLATE_NAME = 'office::/customer/list';

    public const COUNT_ITEMS = 20;

    private $template;

    private $entityManager;

    private $urlHelper;

    private $dataPrepareService;

    private $auth;

    /**
     * CustomerListHandler constructor.
     *
     * @param EntityManager $entityManager
     * @param TemplateRendererInterface|null $template
     * @param UrlHelper $urlHelper
     * @param OfficeDataPrepareService $officeDataPrepare
     * @param UserRepositoryInterface $auth
     */
    public function __construct(
        EntityManager $entityManager,
        TemplateRendererInterface $template = null,
        UrlHelper $urlHelper,
        OfficeDataPrepareService $officeDataPrepare,
        UserRepositoryInterface $auth
    ) {
        $this->template = $template;
        $this->entityManager = $entityManager;
        $this->dataPrepareService = $officeDataPrepare;
        $this->urlHelper = $urlHelper;
        $this->auth = $auth;
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

        $qb->innerJoin('u.customer', 'cus')
            ->andWhere('cus.id = :user')->setParameter('user', $user);

        $data = $this->dataPrepareService->getUsers(
            $queryParams,
            $this->urlHelper->generate('office.customer.list'),
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
