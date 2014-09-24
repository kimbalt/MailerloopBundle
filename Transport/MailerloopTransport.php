<?php

namespace Notrix\MailerloopBundle\Transport;

use Notrix\MailerloopBundle\Exception\MailerloopServiceException;
use Notrix\MailerloopBundle\Exception\MailerloopTransportException;
use Notrix\MailerloopBundle\Message\MailerloopMessage;
use Psr\Log\LoggerInterface;

/**
 * Notrix\MailerloopBundle\Transport\MailerloopTransport
 *
 * @author Vaidas Lažauskas <vaidas@notrix.lt>
 */
class MailerloopTransport implements \Swift_Transport
{
    /**
     * @var \Swift_Events_EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @var \MailerLoop
     */
    protected $mailerApi;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Class constructor
     *
     * @param \Swift_Events_EventDispatcher $dispatcher
     * @param \MailerLoop                   $mailerApi
     * @param \Psr\Log\LoggerInterface      $logger
     */
    public function __construct(
        \Swift_Events_EventDispatcher $dispatcher,
        \MailerLoop $mailerApi,
        LoggerInterface $logger
    ) {
        $this->eventDispatcher = $dispatcher;
        $this->mailerApi = $mailerApi;
        $this->logger = $logger;
    }

    /**
     * Test if this Transport mechanism has started.
     *
     * @return boolean
     */
    public function isStarted()
    {
        return true;
    }

    /**
     * Start this Transport mechanism.
     */
    public function start()
    {
        // No need to something
    }

    /**
     * Stop this Transport mechanism.
     */
    public function stop()
    {
        // No need to something
    }

    /**
     * Send the given Message.
     *
     * Recipient/sender data will be retrieved from the Message API.
     * The return value is the number of recipients who were accepted for delivery.
     *
     * @param \Swift_Mime_Message $message
     * @param string[] $failedRecipients An array of failures by-reference
     *
     * @throws \Notrix\MailerloopBundle\Exception\MailerloopServiceException
     * @throws \Notrix\MailerloopBundle\Exception\MailerloopTransportException
     *
     * @return integer
     */
    public function send(\Swift_Mime_Message $message, &$failedRecipients = null)
    {
        $this->logger->debug(__METHOD__, func_get_args());
        if (!$message instanceof MailerloopMessage) {
            throw new MailerloopServiceException('Email messages through MailerLoop must extend MailerloopMessage');
        }

        $this->mailerApi->clearRecipients();

        $failedRecipients = (array) $failedRecipients;

        if ($event = $this->eventDispatcher->createSendEvent($this, $message)) {
            $this->eventDispatcher->dispatchEvent($event, 'beforeSendPerformed');
            if ($event->bubbleCancelled()) {
                return 0;
            }
        }

        $to = (array) $message->getTo();
        $cc = (array) $message->getCc();
        $bcc = (array) $message->getBcc();

        $this->logger->debug('To recipients', $to);
        $this->logger->debug('CC recipients', $cc);
        $this->logger->debug('BCC recipients', $bcc);

        foreach ($to + $cc + $bcc as $recipient) {
            $this->logger->debug('Processing recipient', (array) $recipient);
            foreach ((array) $recipient as $email => $name) {
                if (is_int($email) && is_string($name)) {
                    $email = $name;
                    $name = '';
                }
                $recipientArray = array(
                    'recipientEmail' => $email,
                    'recipientName'  => $name,
                    'variables'      => $message->getVariables(),
                    'templateId'     => $message->getTemplateId(),
                );
                $this->mailerApi->addRecipient($recipientArray);
            }
        }

        $this->mailerApi->setLanguage($message->getLanguage())
            ->setType($message->getType());

        $from = $message->getFrom();
        if ($from) {
            if (is_array($from)) {
                $email = key($from);
                $name = current($from);

                $this->mailerApi->setFromEmail($email);
                $this->mailerApi->setFromName($name);
            } else {
                $this->mailerApi->setFromEmail((string) $from);
            }
        }

        if ($message->isTest()) {
            $this->mailerApi->setTest();
        }

        $result = $this->mailerApi->send();
        $this->logger->debug('Mail send result from mailerloop', (array) $result);

        if (!$result || !is_array($result)) {
            throw new MailerloopTransportException('Unknown error while sending data to mailerloop');
        }
        $result = array_shift($result);

        if ($event) {
            if (empty($result['code']) || $result['code'] != 200) {
                $event->setResult(\Swift_Events_SendEvent::RESULT_FAILED);
            } else {
                $event->setResult(\Swift_Events_SendEvent::RESULT_SUCCESS);
            }
            $event->setFailedRecipients($failedRecipients);
            $this->eventDispatcher->dispatchEvent($event, 'sendPerformed');
        } else {
            if (empty($result['code']) || $result['code'] != 200) {
                throw new MailerloopTransportException(implode(', ', $result['message']), $result['code']);
            }
        }

        if(isset($result['id'])) {
            $message->setId($result['id']);
        }
        return count($to) + count($cc) + count($bcc);
    }

    /**
     * Register a plugin in the Transport.
     *
     * @param \Swift_Events_EventListener $plugin
     */
    public function registerPlugin(\Swift_Events_EventListener $plugin)
    {
        $this->eventDispatcher->bindEventListener($plugin);
    }
}
