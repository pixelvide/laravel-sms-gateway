<?php

namespace Pixelvide\SMSGateway\Tests;

use Pixelvide\SMSGateway\Exceptions\RecipientCountException;
use Pixelvide\SMSGateway\Exceptions\TemplateIdNotSetException;
use Pixelvide\SMSGateway\Exceptions\VariableMissingException;
use Pixelvide\SMSGateway\SMS;
use Pixelvide\SMSGateway\SMSGateway;
use Pixelvide\SMSGateway\Test\TestCase;

class SMSGatewayTest extends TestCase
{
    /**
     * Test when templateId is missing.
     *
     * @throws TemplateIdNotSetException|RecipientCountException|VariableMissingException
     */
    public function testWhenTemplateIdMissing_ThrowException()
    {
        $this->expectException(TemplateIdNotSetException::class);
        $sms = new SMS();

        $smsGw = new SMSGateway();
        $smsGw->send($sms);
    }

    /**
     * Test when recipients are missing.
     *
     * @throws TemplateIdNotSetException|RecipientCountException|VariableMissingException
     */
    public function testWhenRecipientMissing_ThrowException()
    {
        $this->expectException(RecipientCountException::class);

        $sms = new SMS();
        $sms->setTemplateId('1234567890');

        $smsGw = new SMSGateway();
        $smsGw->send($sms);
    }

    /**
     * Test when everything is valid
     */
    public function testWhenEverythingIsOk()
    {
        $sms = new SMS();
        $sms->setTemplateId('123123');
        $sms->addRecipient('1234567890');

        $smsGw = new SMSGateway();
        $smsGw->send($sms);
    }
}