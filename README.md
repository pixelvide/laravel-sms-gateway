## Pixelvide SMS Gateway

This package allows us to send sms to SMS Gateway which will process and send message using sms service providers.

## Installation

requires this package with composer:

```shell
composer require pixelvide/laravel-sms-gateway
```

Add following keys in .env file
| key | value |
|-----|-------|
| SMS_GATEWAY_ENDPOINT | sms gateway url |

After installing SMS Gateway, you can use it to send sms.

1. Using SMS Gateway.
```shell
$sms = new \Pixelvide\SMSGateway\SMS();
$sms->setTemplateId('01234567890');
$sms->addRecipient('1234567890');
$sms->addSubstitute('var1', 'Team'); # Optional

$smsGw = new \Pixelvide\SMSGateway\SMSGateway();
$smsGw->send($sms)
```

```shell
$sms = new \Pixelvide\SMSGateway\SMS();
$sms
  ->setTemplateId('01234567890')
  ->addRecipient('1234567890');

$smsGw = new \Pixelvide\SMSGateway\SMSGateway();
$smsGw->send($sms)
```

Send SMS to multiple recipient.
```shell
$sms = new \Pixelvide\SMSGateway\SMS();
$sms
  ->setTemplateId('01234567890')
  ->addRecipient('1234567890')
  ->addRecipient('2345678901');

$smsGw = new \Pixelvide\SMSGateway\SMSGateway();
$smsGw->send($sms)
```