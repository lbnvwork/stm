<?php
/**
 * Created by PhpStorm.
 * User: Maksim
 * Date: 20.05.2019
 * Time: 21:28
 */

namespace Cms\Handler\User;

use App\Helper\UrlHelper;
use App\Service\FlashMessage;
use Auth\Entity\User;
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
 * Class UserAddHandler
 * Добавляет пользователя
 *
 * @package Cms\Handler\User
 */
class UserAddHandler implements MiddlewareInterface
{
    //public const TEMPLATE_NAME = 'office::/company/list';

    private $entityManager;

    private $urlHelper;

    private $dataPrepareService;

    private $validator;

    /**
     * UserAddHandler constructor.
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
        /** @var FlashMessage $flashMessage */
        $flashMessage = $request->getAttribute(FlashMessage::class);
        $user = new User();
        $params = $request->getParsedBody();
//        /** @var Role $role */
//        $role = $this->entityManager->getRepository(Role::class)->findOneBy(['id' => $params['userRoles']]);
//        $params['userRoles'] = $role->getRoleName();

        //begin проверка
        if ($params['password'] == null) {
            return new Response\JsonResponse(['error' => ['Поле пароль не заполнено']]);
        }
        if ($params['password'] != null) {
            if ($params['password'] === $params['password2']) {
                $user->setNewPassword($params['password']);
            } else {
                return new Response\JsonResponse(['error' => ['Пароли не совпадают']]);
            }
        }
        $validator = $this->validator->check($request);
        if (!$validator->isValid()) {
            $allErrorMessages = $validator->getMessages()->getAllErrorMessagesArr();
            return new Response\JsonResponse(['error' => $allErrorMessages]);
        }
        $email = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $params['email']]);
        if ($email != null) {
            return new Response\JsonResponse(['error' => ['Пользователь с таким email уже зарегистрирован!']]);
        }
        //end проверка

        //begin обработка
        $this->dataPrepareService->saveUser($user, $params);

        $this->dataPrepareService->updateUserRole($params['userRoles'], $user);

        $this->entityManager->flush();

        $flashMessage->addSuccessMessage('Данные пользователя сохранены');
        //end обработка

        return new Response\JsonResponse(['url' => $this->urlHelper->generate('admin.user.list')]);
    }
}
