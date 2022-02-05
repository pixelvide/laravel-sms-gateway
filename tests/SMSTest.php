<?php

namespace Pixelvide\SMSGateway\Tests;

use Pixelvide\SMSGateway\Exceptions\RecipientCountException;
use Pixelvide\SMSGateway\Exceptions\TemplateIdNotSetException;
use Pixelvide\SMSGateway\SMS;
use Pixelvide\SMSGateway\Test\TestCase;

class SMSTest extends TestCase
{
    /**
     * Test when templateId is missing.
     *
     * @throws TemplateIdNotSetException|RecipientCountException
     */
    public function testWhenTemplateIdMissing_ThrowException()
    {
        $this->expectException(TemplateIdNotSetException::class);
        $sms = new SMS();
        $sms->validate();
    }

    /**
     * Test when recipients are missing.
     *
     * @throws TemplateIdNotSetException|RecipientCountException
     */
    public function testWhenRecipientMissing_ThrowException()
    {
        $this->expectException(RecipientCountException::class);
        $sms = new SMS();
        $sms->setTemplateId('1234567890');
        $sms->validate();
    }

    /**
     * Test when everything is valid
     */
    public function testWhenEverythingIsOk()
    {
        $sms = new SMS();
        $sms->setTemplateId('123123');
        $sms->addRecipient('1234567890');
        $sms->validate();
    }
}