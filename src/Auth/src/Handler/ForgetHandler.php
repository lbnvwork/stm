<?php
/**
 * Created by PhpStorm.
 * User: afinogen
 * Date: 13.03.19
 * Time: 16:42
 */

namespace Auth\Handler;

use App\Helper\UrlHelper;
use App\Service\FlashMessage;
use Auth\Entity\User;
use Auth\Service\SendMail;
use Doctrine\ORM\EntityManager;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ForgetHandler
 *
 * @package Auth\Handler
 */
class ForgetHandler implements RequestHandlerInterface
{
    private $entityManager;
    private $template;
    private $sendMail;
    private $urlHelper;

    /**
     * ForgetHandler constructor.
     *
     * @param TemplateRendererInterface $template
     * @param EntityManager $entityManager
     * @param SendMail $sendMail
     * @param UrlHelper $urlHelper
     */
    public function __construct(TemplateRendererInterface $template, EntityManager $entityManager, SendMail $sendMail, UrlHelper $urlHelper)
    {
        $this->entityManager = $entityManager;
        $this->template = $template;
        $this->sendMail = $sendMail;
        $this->urlHelper = $urlHelper;
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
        /** @var FlashMessage $flash */
        $flash = $request->getAttribute(FlashMessage::class);
        $params = $request->getParsedBody();
        if (isset($params['email']) && $params['email'] != null) {
            /** @var User $user */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $params['email']]);
            if ($user !== null) {
                $user->setHashKey(str_replace('.', '', uniqid(time(), true)));
                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $this->sendMail->restorePassword($user);
                $flash->addSuccessMessage('На Ваш e-mail была отправлена инструкция по восстановленю пароля');
            } else {
                $flash->addErrorMessage('E-mail не найден');
            }

            return new RedirectResponse($this->urlHelper->generate('user.forget'));
        }

        return new HtmlResponse($this->template->render('auth::forget', ['layout' => 'layout::auth']));
    }
}
