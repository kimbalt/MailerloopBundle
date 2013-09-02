MailerloopBundle
================

Symfony2 bundle to implement mailerloop.com service as SwiftMailer transport

Configuration
-------------

in composer.json

    "require": {
        ...,
        "notrix/mailerloop-bundle": "dev-master"
    },

in AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            ...,
            new Notrix\MailerloopBundle\NotrixMailerloopBundle(),
        );
    }

in config.yml

    swiftmailer:
        transport:  mailerloop

    notrix_mailerloop:
        api_key: [MailerLoop API key]

Usage
-----

    use Notrix\MailerloopBundle\Message\MailerloopMessage;

    $message = MailerloopMessage::newInstance()
        ->setSubject('Useful to mark message in code (will not be sent)')
        ->setTo('recipient1@example.com', 'Recipient Name')
        ->addTo('recipient2@example.com')
        ->setTemplateId(00000);
    
    $this->get('mailer')->send($message);

