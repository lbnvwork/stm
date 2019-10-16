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
use Auth\Service\Validator\ProfileValidatorService;
use Auth\UserRepository\Database;
use Doctrine\ORM\EntityManager;
use Psr\Http\Server\RequestHandlerInterface;

use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Template;
use Zend\Expressive\Session\LazySession;
use Zend\Expressive\Session\SessionMiddleware;
use Psr\Http\Message\ResponseInterface;

/**
 * Class UserProfileHandler
 *
 * @package Auth\Handler
 */
class UserProfileHandler implements RequestHandlerInterface
{
    public const TEMPLATE_NAME = 'auth::user/profile';
    /**
     * Описание полей формы
     */
    private const FIELDS = [
        'lastName' => ['title' => 'Фамилия'],
        'firstName' => ['title' => 'Имя'],
        'middleName' => ['title' => 'Отчество'],
//        'email'      => ['title' => 'Email'],
        'phone' => ['title' => 'Телефон'],
    ];

    private $urlHelper;

    private $template;

    private $entityManager;

    private $database;

    private $validator;

    /**
     * UserProfileHandler constructor.
     *
     * @param EntityManager $entityManager
     * @param Database $database
     * @param Template\TemplateRendererInterface|null $template
     * @param UrlHelper $urlHelper
     */
    public function __construct(
        EntityManager $entityManager,
        Database $database,
        Template\TemplateRendererInterface $template = null,
        UrlHelper $urlHelper,
        ProfileValidatorService $validator
    ) {
        $this->entityManager = $entityManager;
        $this->template = $template;
        $this->urlHelper = $urlHelper;
        $this->database = $database;
        $this->validator = $validator;
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

        /** @var LazySession $session */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);

        /** @var FlashMessage $flashMessage */
        $flashMessage = $request->getAttribute(FlashMessage::class);
        /** @var User $user */
        $user = $request->getAttribute(UserInterface::class);
        if ($request->getMethod() === 'POST') {
            $params = $request->getParsedBody();

            $allowedParams = array_intersect_key($params, self::FIELDS);

            $validator = $this->validator->check($request, $allowedParams);
            $params = $validator->setNullParams($params);
            if (!$validator->isValid()) {
                $allErrorMessages = $validator->getMessages()->getAllErrorMessagesArr();
                foreach ($allErrorMessages as $message) {
                    $flashMessage->addErrorMessage($message);
                }
                return new RedirectResponse($this->urlHelper->generate('user.profile'));
            }
            foreach ($allowedParams as $key => $value) {
                if (gettype($value)==='string') {
                    $value = trim($value);
                }
                $method = 'set'.ucfirst($key);
                $user->{$method}($value);
            }

            if (!$user->getIsConfirmed()) {
                if ($params['newPassword'] != null && trim($params['newPassword']) === trim($params['confirmPassword'])) {
                    $user->setNewPassword(trim($params['newPassword']))
                        ->setHashKey(null)
                        ->setIsConfirmed(true);
                    $session->set('u_first_edit', true);
                } else {
                    $flashMessage->addErrorMessage('Не корректный ввод паролей');
                }
            }

            $this->entityManager->flush();
            $flashMessage->addSuccessMessage('Данные обновлены');
            return new RedirectResponse($this->urlHelper->generate('user.profile'));
        }
        $uFirstEdit = false;
        if ($session->has('u_first_edit')) {
            $uFirstEdit = true;
            $session->unset('u_first_edit');
        }
        return new HtmlResponse(
            $this->template->render(
                'auth::user/profile',
                [
                    'layout' => 'office::office',
                    'uFirstEdit' => $uFirstEdit
                ]
            )
        );
    }
}
