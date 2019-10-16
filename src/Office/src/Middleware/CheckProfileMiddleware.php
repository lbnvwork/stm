<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 04.04.18
 * Time: 13:47
 */

namespace Office\Middleware;

use App\Helper\UrlHelper;
use App\Service\FlashMessage;
use Auth\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\PersistentCollection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Router\RouteResult;

/**
 * Class CheckProfileMiddleware
 * Проверяет профиль пользователя и информацию о компании
 *
 * @package Office\Middleware
 */
class CheckProfileMiddleware implements ServerMiddlewareInterface
{
    protected $entityManager;

    protected $urlHelper;

    public function __construct(EntityManager $entityManager, UrlHelper $urlHelper)
    {
        $this->entityManager = $entityManager;
        $this->urlHelper = $urlHelper;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $delegate
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $delegate): ResponseInterface
    {
        /** @var User $user */
        $user = $request->getAttribute(UserInterface::class);

        if (!$user->getIsConfirmed()) {
            /** @var FlashMessage $flashMessage */
            $flashMessage = $request->getAttribute(FlashMessage::class);
            $flashMessage->addInfoMessage('Необходимо заполнить профиль');

            return new RedirectResponse($this->urlHelper->generate('user.profile'));
        }

        /** @var PersistentCollection $userCompanies */
        $userCompaniesCount = $user->getCompany()->filter(
            function ($company) {
                return !$company->getIsDeleted();
            }
        )->count();

        if (!$userCompaniesCount) {
            $route = $request->getAttribute(RouteResult::class);
            if ($route && !($user->getUserRoleManager()->offsetExists('admin') || $user->getUserRoleManager()->offsetExists('office_manager'))) {
                /** @var FlashMessage $flashMessage */
                $flashMessage = $request->getAttribute(FlashMessage::class);
                $flashMessage->addInfoMessage('Заполните информацию о Вашей компании');

                $routeName = $route->getMatchedRoute()->getName();
                if ($routeName !== 'office.company.new') {
                    return new RedirectResponse($this->urlHelper->generate('office.company.new'));
                }
            }
        }
        return $delegate->handle($request);
    }
}
