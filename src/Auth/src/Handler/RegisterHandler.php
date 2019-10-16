<?php
/**
 * Created by PhpStorm.
 * User: afinogen
 * Date: 13.03.19
 * Time: 14:33
 */

namespace Auth\Handler;

use App\Helper\UrlHelper;
use App\Service\FlashMessage;
use Auth\Entity\User;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Authentication\UserRepositoryInterface;
use Zend\Expressive\Session\LazySession;
use Zend\Expressive\Session\SessionMiddleware;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * Class RegisterHandler
 *
 * @package Auth\Handler
 */
class RegisterHandler implements RequestHandlerInterface
{
    private $auth;

    private $template;

    private $entityManager;

    private $urlHelper;

    /**
     * Описание полей формы
     */
    private const FIELDS = [
//        'lastName'   => ['title' => 'Фамилия'],
//        'firstName'  => ['title' => 'Имя'],
//        'middleName' => ['title' => 'Отчество'],
        'email' => ['title' => 'Email'],
//        'phone'      => ['title' => 'Телефон'],
    ];

    /**
     * RegisterHandler constructor.
     *
     * @param TemplateRendererInterface $template
     * @param UserRepositoryInterface $auth
     * @param EntityManager $entityManager
     * @param UrlHelper $urlHelper
     */
    public function __construct(TemplateRendererInterface $template, UserRepositoryInterface $auth, EntityManager $entityManager, UrlHelper $urlHelper)
    {
        $this->template = $template;
        $this->auth = $auth;
        $this->entityManager = $entityManager;
        $this->urlHelper = $urlHelper;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface|HtmlResponse|RedirectResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
//        if (isset($queryParams['referral'])) {
//            /** @var User|null $refUser */
//            $refUser = $this->entityManager->getRepository(User::class)->find($queryParams['referral']);
//            if ($refUser) {
//                /** @var LazySession $session */
//                $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
//                $session->set('referral', $refUser->getId());
//            }
//        }

        if ($request->getMethod() === 'POST') {
            return $this->register($request);
        }

        return new HtmlResponse($this->template->render('auth::register', ['layout' => 'layout::auth']));
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return HtmlResponse|RedirectResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function register(ServerRequestInterface $request)
    {
        $params = $request->getParsedBody();

        $allowedParams = array_intersect_key($params, self::FIELDS);
        $messages = [];
        /** @var FlashMessage $flashMessage */
        $flashMessage = $request->getAttribute(FlashMessage::class);

        foreach (self::FIELDS as $key => $field) {
            if ($allowedParams[$key] == null) {
                $messages[] = 'Не заполнено поле `'.$field['title'].'`';
            }
        }

        if (\count($messages)
            || $this->entityManager->getRepository(User::class)->count(
                [
                    'email' => $allowedParams['email'],
                ]
            )) {
            if (\count($messages)) {
                foreach ($messages as $message) {
                    $flashMessage->addErrorMessage($message);
                }
            } else {
                $flashMessage->addErrorMessage('Пользователь с таким email уже зарегистрирован');
            }

            return new RedirectResponse($this->urlHelper->generate('register'));
        }

        /** @var User $user */
        $user = $this->auth->register($allowedParams);
//        if ($request->getAttribute('needPromo')) {
//            $user->setRoboPromo(1);
//        }

        /** @var LazySession $session */
        //$session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
//        if ($session->has('referral')) {
//            /** @var User|null $refUser */
//            $refUser = $this->entityManager->getRepository(User::class)->find($session->get('referral'));
//            $user->setReferral($refUser);
//        }
        $this->entityManager->flush();

        $flashMessage->addSuccessMessage('На Вашу почту было отправлено письмо для подтверждения почтового ящика и паролем для входа');

        return new RedirectResponse($this->urlHelper->generate('login'));
    }
}
