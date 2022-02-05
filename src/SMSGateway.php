<?php

namespace Pixelvide\SMSGateway;

use Aws\Sqs\SqsClient;
use Pixelvide\SMSGateway\Exceptions\RecipientCountException;
use Pixelvide\SMSGateway\Exceptions\TemplateIdNotSetException;
use Pixelvide\SMSGateway\Exceptions\VariableMissingException;

class SMSGateway {
    /**
     * @var mixed
     */
    protected $smsGatewayEndpoint;
    /**
     * @var SqsClient
     */
    protected $sqsClient;

    /**
     * AWSGateway constructor.
     */
    public function __construct() {
        $this->smsGatewayEndpoint = env('SMS_GATEWAY_ENDPOINT');

        $awsConfig = [
            'region' => env('SMS_GATEWAY_REGION', 'ap-south-1'),
            'version' => '2012-11-05',
        ];

        if (env('AWS_PROFILE')) {
            $awsConfig['profile'] = env('AWS_PROFILE');
        }

        $this->sqsClient = new SqsClient($awsConfig);
    }

    private function isNullOrEmptyString($str): bool
    {
        return ($str === null || trim($str) === '');
    }

    /**
     * @param  SMS  $sms
     * @return array
     *
     * @throws VariableMissingException
     */
    private function gatewayPayload(SMS $sms): array
    {

        if ($this->isNullOrEmptyString($this->smsGatewayEndpoint)) {
            throw new VariableMissingException('SMS_GATEWAY_ENDPOINT variable is either unspecified or empty');
        }

        return [
            'QueueUrl' => $this->smsGatewayEndpoint,
            'MessageBody' => json_encode([
                'templateId' => $sms->getTemplateId(),
                'substitutes' => $sms->getSubstitutes(),
                'recipients' => $sms->getRecipients(),
            ]),
        ];
    }

    /**
     * @throws RecipientCountException
     * @throws TemplateIdNotSetException
     * @throws VariableMissingException
     */
    public function send(SMS $sms) {
        $sms->validate();

        $this->sqsClient->sendMessage($this->gatewayPayload($sms));
    }
}