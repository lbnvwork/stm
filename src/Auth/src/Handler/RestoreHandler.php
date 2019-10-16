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
use Auth\UserRepository\Database;
use Doctrine\ORM\EntityManager;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\RedirectResponse;

/**
 * Class RestoreHandler
 *
 * @package Auth\Handler
 */
class RestoreHandler implements RequestHandlerInterface
{
    private $entityManager;
    private $database;
    private $sendMail;
    private $urlHelper;

    /**
     * ConfirmHandler constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager, Database $database, SendMail $sendMail, UrlHelper $urlHelper)
    {
        $this->entityManager = $entityManager;
        $this->database = $database;
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
        /** @var FlashMessage $flashMeaasge */
        $flashMeaasge = $request->getAttribute(FlashMessage::class);
        $hash = $request->getAttribute('hash');
        if ($hash !== null) {
            /** @var User $user */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['hashKey' => $hash]);
            if ($user !== null) {
                $user->setHashKey(null);

                $pass = Database::generateStrongPassword();
                $user->setNewPassword($pass);
                $user->setIsConfirmed(true);
                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $this->sendMail->sendNewPassword($user, $pass);

                $flashMeaasge->addSuccessMessage('На Ваш e-mail выслан новый пароль');
            } else {
                $flashMeaasge->addErrorMessage('Неверный код');
            }
        }

        return new RedirectResponse($this->urlHelper->generate('login'));
    }
}
