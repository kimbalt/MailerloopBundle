<?php

namespace Notrix\MailerloopBundle\Message;

/**
 * Message class to send swift email to mailerloop
 *
 * @author Vaidas Lažauskas <vaidas@notrix.lt>
 */
class MailerloopMessage extends AbstractMailerloopMessage implements \Swift_Mime_Message
{
    /**
     * @var integer
     */
    protected $templateId;

    /**
     * @var string[]
     */
    protected $variables = array();

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $language;

    /**
     * @var boolean
     */
    protected $test = false;

    /**
     * Create a new Message.
     *
     * @param integer  $templateId
     * @param string[] $variables
     */
    public function __construct($templateId = null, $variables = null)
    {
        call_user_func_array(
            array($this, 'Swift_Mime_SimpleMimeEntity::__construct'),
            \Swift_DependencyContainer::getInstance()
                ->createDependenciesFor('mime.message')
        );
        if ($templateId) {
            $this->setTemplateId($templateId);
        }
        if ($variables) {
            $this->setVariables($variables);
        }
    }

    /**
     * Setter of TemplateId
     *
     * @param integer $templateId
     *
     * @return MailerloopMessage
     */
    public function setTemplateId($templateId)
    {
        $this->templateId = $templateId;

        return $this;
    }

    /**
     * Getter of TemplateId
     *
     * @return integer
     */
    public function getTemplateId()
    {
        return $this->templateId;
    }

    /**
     * Setter of Variable
     *
     * @param string $key
     * @param string $value
     *
     * @return MailerloopMessage
     */
    public function addVariable($key, $value)
    {
        $this->variables[$key] = $value;

        return $this;
    }

    /**
     * Setter of Variables
     *
     * @param array $variables
     *
     * @return MailerloopMessage
     */
    public function setVariables($variables)
    {
        $this->variables = $variables;

        return $this;
    }

    /**
     * Getter of Variables
     *
     * @return array
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * Getter of Variable
     *
     * @param string $key
     *
     * @return string|null
     */
    public function getVariable($key)
    {
        return isset($this->variables[$key]) ? $this->variables[$key] : null;
    }

    /**
     * Setter of Language
     *
     * @param string $language
     *
     * @return MailerloopMessage
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Getter of Language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Setter of Test
     *
     * @param boolean $test
     *
     * @return MailerloopMessage
     */
    public function setTest($test)
    {
        $this->test = $test;

        return $this;
    }

    /**
     * Getter of Test
     *
     * @return boolean
     */
    public function isTest()
    {
        return $this->test;
    }

    /**
     * Setter of Type
     *
     * @param string $type
     *
     * @return MailerloopMessage
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Getter of Type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Gets new message instance
     *
     * @param integer  $templateId
     * @param string[] $variables
     *
     * @return static
     */
    static public function newInstance($templateId = null, $variables = null)
    {
        return new static($templateId, $variables);
    }
}
