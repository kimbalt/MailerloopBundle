MailerloopBundle
================

Symfony2 bundle to implement mailerloop.com service as SwiftMailer transport

Configuration
-------------

in config.yml

    swiftmailer:
        transport:  mailerloop

    notrix_mailerloop:
        api_key: [MailerLoop API key]

Usage
-----

    $message = MailerloopMessage::newInstance()
        ->setSubject('Useful to mark message in code (will not be sent)')
        ->setTo('recipient1@example.com', 'Recipient Name')
        ->addTo('recipient2@example.com')
        ->setTemplateId(00000);
    
    $this->get('mailer')->send($message);

