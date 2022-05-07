<?php

namespace Pixelvide\SMSGateway;

use Aws\Lambda\LambdaClient;
use Aws\Sqs\SqsClient;
use Pixelvide\SMSGateway\Exceptions\RecipientCountException;
use Pixelvide\SMSGateway\Exceptions\TemplateIdNotSetException;
use Pixelvide\SMSGateway\Exceptions\VariableMissingException;

class SMSGateway
{
    /**
     * @var mixed
     */
    protected $smsGatewayEndpoint;
    /**
     * @var LambdaClient
     */
    protected $lambdaClient;

    /**
     * AWSGateway constructor.
     */
    public function __construct()
    {
        $this->smsGatewayEndpoint = env('SMS_GATEWAY_ENDPOINT');
        $awsConfig = [
            'region'  => env('SMS_GATEWAY_REGION', 'ap-south-1'),
            'version' => '2015-03-31',
        ];
        if (env('AWS_PROFILE')) {
            $awsConfig['profile'] = env('AWS_PROFILE');
        }
        $this->lambdaClient = new LambdaClient($awsConfig);
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
            'FunctionName' => $this->smsGatewayEndpoint,
            'Payload'      => json_encode([
                'templateId'  => $sms->getTemplateId(),
                'substitutes' => $sms->getSubstitutes(),
                'recipients'  => $sms->getRecipients(),
            ]),
        ];
    }

    /**
     * @throws RecipientCountException
     * @throws TemplateIdNotSetException
     * @throws VariableMissingException
     */
    public function send(SMS $sms)
    {
        $sms->validate();
        $response = $this->lambdaClient->invoke($this->gatewayPayload($sms));
        $result = $response->get('Payload')
            ->getContents();

        return json_decode($result, true);
    }
}