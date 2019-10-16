<?php
/**
 * Created by PhpStorm.
 * User: Maksim
 * Date: 20.05.2019
 * Time: 21:31
 */

namespace Cms\Handler\User;

use App\Helper\UrlHelper;
use App\Service\FlashMessage;
use Auth\Entity\User;
use Auth\Entity\UserHasRole;
use Cms\Service\Validator\UserValidatorService;
use Doctrine\ORM\EntityManager;
use App\Service\DataPrepare\DataPrepareService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Expressive\Authentication\UserRepositoryInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;

/**
 * Class UserEditHandler
 * Изменяет данные о пользователе
 *
 * @package Cms\Handler\User
 */
class UserEditHandler implements MiddlewareInterface
{
    private $entityManager;

    private $urlHelper;

    private $dataPrepareService;

    private $validator;

    private $template;

    /**
     * UserEditHandler constructor.
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
        UserRepositoryInterface $auth,
        UserValidatorService $validator
    ) {
        $this->entityManager = $entityManager;
        $this->urlHelper = $urlHelper;
        $this->dataPrepareService = $dataPrepareService;
        $this->validator = $validator;
        $this->template = $template;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $id = $request->getAttribute('id');
        /** @var FlashMessage $flashMessage */
        $flashMessage = $request->getAttribute(FlashMessage::class);
        /** @var User $user */
        $user = $this->entityManager->find(User::class, $id);
        $params = $request->getParsedBody();
        if (!isset($params['userRoles'])) {
            $userRoles = $user->getRoles();
            $params['userRoles'] = [];
            /** @var UserHasRole $userHasRole */
            foreach ($userRoles as $userHasRole) {
                $params['userRoles'][] = $userHasRole->getRoleName();
            }
        }
        //begin проверка
        if ($user === null) {
            return new Response\HtmlResponse($this->template->render('error::404'), 404);
        }

        if ($params['password'] != null) {
            if (trim($params['password']) === trim($params['password2'])) {
                $user->setNewPassword(trim($params['password']));
            } else {
                $flashMessage->addErrorMessage('Пароли не совпадают');
                return new Response\RedirectResponse($this->urlHelper->generate('admin.user.item', ['id' => $user->getId()]));
            }
        }
        $validator = $this->validator->check($request, $params);
        if (!$validator->isValid()) {
            $allErrorMessages = $validator->getMessages()->getAllErrorMessagesArr();
            foreach ($allErrorMessages as $message) {
                $flashMessage->addErrorMessage($message);
            }
            return new Response\RedirectResponse($this->urlHelper->generate('admin.user.item', ['id' => $user->getId()]));
        }
        //end проверка

        $this->dataPrepareService->saveUser($user, $params);

        $flashMessage->addSuccessMessage('Данные пользователя сохранены');

        return new Response\RedirectResponse($this->urlHelper->generate('admin.user.item', ['id' => $user->getId()]));
    }
}
