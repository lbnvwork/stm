<?php
/**
 * Created by PhpStorm.
 * User: afinogen
 * Date: 13.03.19
 * Time: 12:01
 */

namespace Auth\Handler;

use App\Helper\UrlHelper;
use App\Service\FlashMessage;
use Auth\Entity\User;
use Auth\UserRepository\Database;
use Doctrine\ORM\EntityManager;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Template;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ChangePasswordHandler
 *
 * @package Cms\Handler\User
 */
class ChangePasswordHandler implements RequestHandlerInterface
{
    public const TEMPLATE_NAME = 'auth::change-password';

    private $urlHelper;

    private $template;

    private $entityManager;

    private $database;

    /**
     * ChangePasswordHandler constructor.
     *
     * @param EntityManager $entityManager
     * @param Database $database
     * @param Template\TemplateRendererInterface|null $template
     * @param UrlHelper $urlHelper
     */
    public function __construct(EntityManager $entityManager, Database $database, Template\TemplateRendererInterface $template = null, UrlHelper $urlHelper)
    {
        $this->entityManager = $entityManager;
        $this->template = $template;
        $this->urlHelper = $urlHelper;
        $this->database = $database;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var FlashMessage $flashMessage */
        $flashMessage = $request->getAttribute(FlashMessage::class);
        if ($request->getMethod() === 'POST') {
            $params = $request->getParsedBody();
            if ($params['oldPassword'] != null && $params['newPassword'] != null && $params['newPassword'] === $params['confirmPassword']) {
                /** @var User $user */
                $user = $request->getAttribute(UserInterface::class);
                if ($user != null) {
                    if ($this->database->verifyPassword($user, $params['oldPassword'])) {
                        $user->setNewPassword($params['newPassword']);
                        $this->entityManager->persist($user);
                        $this->entityManager->flush();
                        $flashMessage->addSuccessMessage('Данные обновлены');
                    } else {
                        $flashMessage->addErrorMessage('Не верный пароль');
                    }
                } else {
                    $flashMessage->addErrorMessage('Пользователь не авторизован');
                }
            } else {
                $flashMessage->addErrorMessage('Не корректно заполнены поля');
            }

            return new RedirectResponse($this->urlHelper->generate('user.changePassword'));
        }

        return new HtmlResponse($this->template->render(self::TEMPLATE_NAME, ['layout' => 'office::office']));
    }
}
