<?php

namespace Pixelvide\SMSGateway;

use Pixelvide\SMSGateway\Exceptions\RecipientCountException;
use Pixelvide\SMSGateway\Exceptions\TemplateIdNotSetException;

class SMS
{
    /**
     * @var string
     */
    private $templateId = null;
    /**
     * @var array
     */
    private $substitutes = [];
    /**
     * @var array
     */
    private $recipients = [];


    /**
     * @return string
     */
    public function getTemplateId()
    {
        return $this->templateId;
    }

    /**
     * @param  string  $templateId
     * @return SMS
     */
    public function setTemplateId(string $templateId): SMS
    {
        $this->templateId = $templateId;
        return $this;
    }

    /**
     * @return array
     */
    public function getSubstitutes(): array
    {
        return $this->substitutes;
    }

    /**
     * @param  string  $key
     * @param  string  $val
     * @return SMS
     */
    public function addSubstitute(string $key, string $val): SMS
    {
        $this->substitutes[$key] = $val;
        return $this;
    }

    /**
     * @return array
     */
    public function getRecipients(): array
    {
        return $this->recipients;
    }

    /**
     * @params string $recipient
     */
    public function addRecipient(string $recipient): SMS
    {
        $this->recipients[] = $recipient;
        return $this;
    }

    private function isNullOrEmptyString($str): bool
    {
        return ($str === null || trim($str) === '');
    }

    /**
     * Send SMS to sms gateway
     *
     * @throws RecipientCountException
     * @throws TemplateIdNotSetException
     */
    public function validate() {
        if ($this->isNullOrEmptyString($this->templateId)) {
            throw new TemplateIdNotSetException('TemplateId not set');
        }

        if (count($this->recipients) < 1) {
            throw new RecipientCountException('At least one recipient should be specified');
        }
    }

}