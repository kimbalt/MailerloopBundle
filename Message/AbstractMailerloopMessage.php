<?php

namespace Notrix\MailerloopBundle\Message;

/**
 * Abstract class to hold basic methods
 *
 * @author Vaidas Lažauskas <vaidas@notrix.lt>
 */
abstract class AbstractMailerloopMessage extends \Swift_Mime_SimpleMimeEntity
{
    /**
     * @var string
     */
    protected $subject;

    /**
     * @var integer
     */
    protected $date;

    /**
     * @var string
     */
    protected $returnPath;

    /**
     * @var array
     */
    protected $to = array();

    /**
     * @var array
     */
    protected $cc = array();

    /**
     * @var array
     */
    protected $bcc = array();

    /**
     * @var array
     */
    protected $replyTo = array();

    /**
     * @var array
     */
    protected $sender = array();

    /**
     * @var array
     */
    protected $from = array();

    /**
     * Set the subject of the message.
     *
     * @param string $subject
     *
     * @return MailerloopMessage
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get the subject of the message.
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set the origination date of the message as a UNIX timestamp.
     *
     * @param integer $date
     *
     * @return MailerloopMessage
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get the origination date of the message as a UNIX timestamp.
     *
     * @return int
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set the return-path (bounce-detect) address.
     *
     * @param string $address
     *
     * @return MailerloopMessage
     */
    public function setReturnPath($address)
    {
        $this->returnPath = $address;

        return $this;
    }

    /**
     * Get the return-path (bounce-detect) address.
     *
     * @return string
     */
    public function getReturnPath()
    {
        return $this->returnPath;
    }

    /**
     * Set the sender of this message.
     *
     * If multiple addresses are present in the From field, this SHOULD be set.
     *
     * According to RFC 2822 it is a requirement when there are multiple From
     * addresses, but Swift itself does not require it directly.
     *
     * An associative array (with one element!) can be used to provide a display-
     * name: i.e. array('email@address' => 'Real Name').
     *
     * If the second parameter is provided and the first is a string, then $name
     * is associated with the address.
     *
     * @param mixed  $address
     * @param string $name    optional
     *
     * @return MailerloopMessage
     */
    public function setSender($address, $name = null)
    {
        $this->sender = array($address => $name);

        return $this;
    }

    /**
     * Get the sender address for this message.
     *
     * This has a higher significance than the From address.
     *
     * @return string
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set the From address of this message.
     *
     * It is permissible for multiple From addresses to be set using an array.
     *
     * If multiple From addresses are used, you SHOULD set the Sender address and
     * according to RFC 2822, MUST set the sender address.
     *
     * An array can be used if display names are to be provided: i.e.
     * array('email@address.com' => 'Real Name').
     *
     * If the second parameter is provided and the first is a string, then $name
     * is associated with the address.
     *
     * @param mixed  $addresses
     * @param string $name      optional
     *
     * @return MailerloopMessage
     */
    public function setFrom($addresses, $name = null)
    {
        if (!is_array($addresses) && isset($name)) {
            $addresses = array($addresses => $name);
        }
        $this->from = $addresses;

        return $this;
    }

    /**
     * Get the From address(es) of this message.
     *
     * This method always returns an associative array where the keys are the
     * addresses.
     *
     * @return string[]
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set the Reply-To address(es).
     *
     * Any replies from the receiver will be sent to this address.
     *
     * It is permissible for multiple reply-to addresses to be set using an array.
     *
     * This method has the same synopsis as {@link setFrom()} and {@link setTo()}.
     *
     * If the second parameter is provided and the first is a string, then $name
     * is associated with the address.
     *
     * @param mixed  $addresses
     * @param string $name      optional
     *
     * @return MailerloopMessage
     */
    public function setReplyTo($addresses, $name = null)
    {
        if (!is_array($addresses) && isset($name)) {
            $addresses = array($addresses => $name);
        }
        $this->replyTo = $addresses;

        return $this;
    }

    /**
     * Get the Reply-To addresses for this message.
     *
     * This method always returns an associative array where the keys provide the
     * email addresses.
     *
     * @return string[]
     */
    public function getReplyTo()
    {
        return $this->replyTo;
    }

    /**
     * Set the To address(es).
     *
     * Recipients set in this field will receive a copy of this message.
     *
     * This method has the same synopsis as {@link setFrom()} and {@link setCc()}.
     *
     * If the second parameter is provided and the first is a string, then $name
     * is associated with the address.
     *
     * @param mixed  $addresses
     * @param string $name      optional
     *
     * @return MailerloopMessage
     */
    public function setTo($addresses, $name = null)
    {
        if (!is_array($addresses) && isset($name)) {
            $addresses = array(array($addresses => $name));
        }
        $this->to = $addresses;

        return $this;
    }

    /**
     * Ads recipient to list
     *
     * @param string $address
     * @param string $name
     *
     * @return MailerloopMessage
     */
    public function addTo($address, $name = null)
    {
        $this->to[] = array($address => $name);

        return $this;
    }

    /**
     * Get the To addresses for this message.
     *
     * This method always returns an associative array, whereby the keys provide
     * the actual email addresses.
     *
     * @return string[]
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Set the Cc address(es).
     *
     * Recipients set in this field will receive a 'carbon-copy' of this message.
     *
     * This method has the same synopsis as {@link setFrom()} and {@link setTo()}.
     *
     * @param mixed  $addresses
     * @param string $name      optional
     *
     * @return MailerloopMessage
     */
    public function setCc($addresses, $name = null)
    {
        if (!is_array($addresses) && isset($name)) {
            $addresses = array(array($addresses => $name));
        }
        $this->cc = $addresses;

        return $this;
    }

    /**
     * Get the Cc addresses for this message.
     *
     * This method always returns an associative array, whereby the keys provide
     * the actual email addresses.
     *
     * @return string[]
     */
    public function getCc()
    {
        return $this->cc;
    }

    /**
     * Set the Bcc address(es).
     *
     * Recipients set in this field will receive a 'blind-carbon-copy' of this
     * message.
     *
     * In other words, they will get the message, but any other recipients of the
     * message will have no such knowledge of their receipt of it.
     *
     * This method has the same synopsis as {@link setFrom()} and {@link setTo()}.
     *
     * @param mixed  $addresses
     * @param string $name      optional
     *
     * @return MailerloopMessage
     */
    public function setBcc($addresses, $name = null)
    {
        if (!is_array($addresses) && isset($name)) {
            $addresses = array(array($addresses => $name));
        }
        $this->bcc = $addresses;

        return $this;
    }

    /**
     * Get the Bcc addresses for this message.
     *
     * This method always returns an associative array, whereby the keys provide
     * the actual email addresses.
     *
     * @return string[]
     */
    public function getBcc()
    {
        return $this->bcc;
    }
}
