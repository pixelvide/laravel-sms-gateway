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
     */
    private function buildSMSPayload (SMS $sms): array
    {
        return [
            'templateId' => $sms->getTemplateId(),
            'substitutes' => $sms->getSubstitutes(),
            'recipients' => $sms->getRecipients(),
        ];
    }

    /**
     * @param  array  $payload
     * @return array
     *
     * @throws VariableMissingException
     */
    private function gatewayPayload(array $payload): array
    {
        if ($this->isNullOrEmptyString($this->smsGatewayEndpoint)) {
            throw new VariableMissingException('SMS_GATEWAY_ENDPOINT variable is either unspecified or empty');
        }
        return [
            'FunctionName' => $this->smsGatewayEndpoint,
            'Payload' => json_encode($payload),
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

        $response = $this->lambdaClient->invoke($this->gatewayPayload($this->buildSMSPayload($sms)));
        $result = $response->get('Payload')
            ->getContents();

        return json_decode($result, true);
    }

    /**
     * @param  array  $smsArr
     * @return mixed
     * @throws VariableMissingException
     */
    public function sendBulk(array $smsArr) {
        $gwPayload = array();
        foreach ($smsArr as $sms) {
            $sms->validate();

            $gwPayload[] = $this->buildSMSPayload($sms);
        }

        $response = $this->lambdaClient->invoke($this->gatewayPayload($gwPayload));
        $result = $response->get('Payload')
            ->getContents();

        return json_decode($result, true);
    }
}