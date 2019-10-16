<?php
/**
 * Created by PhpStorm.
 * User: afinogen
 * Date: 13.03.18
 * Time: 16:42
 */

namespace Auth\Handler;

use App\Helper\UrlHelper;
use App\Service\FlashMessage;
use Auth\Entity\User;
use Auth\Service\AuthenticationService;
use Doctrine\ORM\EntityManager;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Session\LazySession;
use Zend\Expressive\Session\SessionMiddleware;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ConfirmHandler
 *
 * @package Auth\Handler
 */
class ConfirmHandler implements RequestHandlerInterface
{
    private $entityManager;
    private $urlHelper;

    /**
     * ConfirmHandler constructor.
     *
     * @param EntityManager $entityManager
     * @param UrlHelper $urlHelper
     */
    public function __construct(EntityManager $entityManager, UrlHelper $urlHelper)
    {
        $this->entityManager = $entityManager;
        $this->urlHelper = $urlHelper;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var FlashMessage $flashMeaasge */
        $flashMeaasge = $request->getAttribute(FlashMessage::class);
        $hash = $request->getAttribute('hash');
        if ($hash !== null) {
            /** @var User $user */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['hashKey' => $hash]);
            if ($user !== null) {

                /** @var LazySession $session */
                $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
                $session->set(AuthenticationService::SESSION_AUTH, $user->getId());
                $flashMeaasge->addSuccessMessage('E-mail подтвержден');
            } else {
                $flashMeaasge->addErrorMessage('Неверный код');
            }
        }

        return new RedirectResponse($this->urlHelper->generate('user.profile'));
    }
}
