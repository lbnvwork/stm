<?php
/**
 * Created by PhpStorm.
 * User: afinogen
 * Date: 16.03.18
 * Time: 23:10
 */

namespace App\Service;

use Doctrine\ORM\EntityManager;

/**
 * Class SendMail
 *
 * @package App\Service
 */
class SendMail
{
    private $config = [];

    private $entityManager;

    /**
     * SendMail constructor.
     *
     * @param array $config
     * @param EntityManager $entityManager
     */
    public function __construct(array $config, EntityManager $entityManager)
    {
        $this->config = $config;
        $this->entityManager = $entityManager;
    }

    /**
     * @param \Swift_Message $message
     *
     * @return int
     */
    private function send(\Swift_Message $message): int
    {
        // Create the Transport
        $transport = (new \Swift_SmtpTransport($this->config['host'], $this->config['port'], null))//$this->config['encryption']))
        ->setUsername($this->config['login'])
            ->setPassword($this->config['password']);

        // Create the Mailer using your created Transport
        $mailer = new \Swift_Mailer($transport);

        // Send the message
        return $mailer->send($message);
    }
}
