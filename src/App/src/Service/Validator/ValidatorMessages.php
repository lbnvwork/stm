<?php
/**
 * Created by PhpStorm.
 * User: KiisChivarino
 * Date: 19.05.2019
 * Time: 22:28
 */

namespace App\Service\Validator;

class ValidatorMessages
{
    /**
     * Сообщение об ошибке по умолчанию
     *
     * @var string $defaultMessage
     */
    private $defaultMessage;
    /**
     * Отчет о параметрах, не прошедших валидацию
     *
     * @var string $reportMessage
     */
    private $reportMessage;
    /**
     * Массив пользовательских сообщений о параметре, не прошедшем валидацию
     *
     * @var array $parameterMessages
     */
    private $parameterMessages;
    /**
     * Объект валидатора
     *
     * @var ValidatorInterface $validator
     */
    private $validator;
    /**
     * Сообщение об успешной валидации
     *
     * @var $successMessage
     */
    private $successMessage;

    /**
     * ValidatorMessages constructor.
     *
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Устанавливает отчет о параметрах, не прошедших валидацию
     *
     * @return $this
     */
    public function setReportMessage()
    {
        $message = '';
        if ($this->validator->isValid()) {
            $this->reportMessage = $message;
            return $this;
        } else {
            $message .= 'Поля ';
            /** @var Parameter $param */
            foreach ($this->validator->getParameters() as $param) {
                if (!$param->getValid()) {
                    $message .= '"'.$param->getTitle().'", ';
                }
            }
            $message = substr($message, 0, strlen($message) - 2);
            $message .= ' заполнены неверно!';
        }
        $this->reportMessage = $message;
        return $this;
    }

    /**
     * Возвращает отчет о параметрах, не прошедших валидацию
     *
     * @return string
     */
    public function getReportMessage()
    {
        return $this->reportMessage;
    }

    /**
     * Устанавливает массив пользовательских сообщений о параметре, не прошедшем валидацию
     *
     * @return $this
     */
    public function setParameterMessages()
    {
        $messages = [];
        if ($this->validator->isValid()) {
            $this->parameterMessages = $messages;
            return $this;
        } else {
            /** @var Parameter $param */
            foreach ($this->validator->getParameters() as $param) {
                if (!$param->getValid() && $param->getMessage() != null) {
                    $messages[] = $param->getMessage();
                }
            }
            $this->parameterMessages = $messages;
            return $this;
        }
    }

    /**
     * Возвращает массив пользовательских сообщений о параметре, не прошедшем валидацию
     *
     * @return array
     */
    public function getParameterMessages()
    {
        return $this->parameterMessages;
    }

    /**
     * Устанавливает сообщение по умолчанию (типа "одно или несколько полей формы... бла-бла-бла")
     *
     * @param string $message
     */
    public function setDefaultMessage(string $message)
    {
        $this->defaultMessage = $message;
    }

    /**
     * Возвращает сообщение по умолчанию
     *
     * @return string|null
     */
    public function getDefaultMessage()
    {
        return !$this->validator->isValid() ? $this->defaultMessage : null;
    }

    /**
     * Заполняет объект сообщениями по результатам валидации
     *
     * @param string|null $defaultMessage
     * @param string|null $successMessage
     *
     * @return $this
     */
    public function prepareMessages(string $defaultMessage = null, string $successMessage = null)
    {
        $this->setDefaultMessage($defaultMessage ?? 'Одно или несколько полей заполнены неверно!');
        $this->setSuccessMessage($successMessage ?? 'Все данные внесены успешно!');
        $this->setParameterMessages();
        $this->setReportMessage();
        return $this;
    }

    /**
     * Возвращает сообщение об успешной валидации формы
     *
     * @return mixed
     */
    public function getSuccessMessage()
    {
        return $this->successMessage;
    }

    /**
     * Устанавливает сообщение об успешной валидации формы (на всякий случай, вдруг пригодится)
     *
     * @param string $successMessage
     */
    public function setSuccessMessage(string $successMessage)
    {
        $this->successMessage = $successMessage;
    }

    /**
     * Возвращает все сообщения об ошибках, имеющиеся в объекте
     *
     * @return array
     */
    public function getAllErrorMessagesArr()
    {
        $errorMessagesArr = [];
        $errorMessagesArr[] = $this->getReportMessage();
        foreach ($this->getParameterMessages() as $message) {
            $errorMessagesArr[] = $message;
        }
        return $errorMessagesArr;
    }
}
