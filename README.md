# Campaignmonitor SMTP Transport for Swiftmailer for transactional emails

## Install

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```bash
$ php composer.phar require --prefer-dist gurucomkz/campaignmonitor-smtp-transport "dev-master"
```

or add

```json
"gurucomkz/campaignmonitor-smtp-transport": "dev-master"
```

to the require section of your `composer.json` file.


## Usage

Once the extension is installed, simply use it in your code:

### In configuration file ###

```php
'components' => [
    'mailer' => [
        'class' => '\yii\swiftmailer\MailerMailer',
        'transport' => [
            'class' => '\gurucomkz\campaignmonitor\SmtpTransport',
            'mailgunDomain' => '<Domain Name>',
            'privateApiKey' => '<Private API Key>',
        ],
    ],
```

### Sending images to Johny ###

```php
\Yii::$app->mailer
    ->compose()
    ->setFrom('me@nomail.com')
    ->setTo('john.doe@nomail.com')
    ->setSubject('Message to Johny')
    ->setTextBody('Hello, Johny! Take a look at our party pics :)')
    ->attach('/image-1.jpeg')
    ->attach('/image-2.jpeg')
    ->send();
```

## License

The BSD License (BSD). Please see [License File](LICENSE.md) for more information.
