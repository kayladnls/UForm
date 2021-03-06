<?php

/**
 * @license see LICENSE
 */

namespace UForm\Form;

use UForm\DataContext;
use UForm\Form;
use UForm\Validation\ChainedValidation;

class FormContext
{

    /**
     * @var Form
     */
    protected $form;

    /**
     * @var DataContext
     */
    protected $data;

    /**
     * @var ChainedValidation
     */
    protected $chainValidation;

    public function __construct($form, DataContext $data)
    {
        $this->form = $form;
        $this->data = $data;
        $this->chainValidation = new ChainedValidation($data);
        $this->form->prepareValidation($this->data, $this);
    }

    /**
     * Get the internal chained validation item
     * @return ChainedValidation
     */
    public function getChainedValidation()
    {
        return $this->chainValidation;
    }

    /**
     * validates the formContext
     * A form context will be always valid before being validated.
     * Additionally a form context generated with $form->validate will already be validated
     * @return bool true if the data are valid
     */
    public function validate()
    {
        $this->chainValidation->validate();
        return $this->chainValidation->isValid();
    }

    /**
     * Get the form that the formContext represents
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Gets the data of the form context
     * @return DataContext
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Check if the form context is valid
     * A form context will always be valid before being validate
     * @return bool
     */
    public function isValid()
    {
        return $this->chainValidation->isValid();
    }

    /**
     * Get all the messages generated during the validation
     * @return \UForm\Validation\Message\Group
     */
    public function getMessages()
    {
        return $this->chainValidation->getMessages();
    }

    /**
     * Check if an element is valid
     * A element will always be valid before the formContext is validated
     * @param string|Element $elementName
     * @param bool $iname true to use the internal name
     * @return bool
     * @throws \UForm\Exception
     */
    public function elementIsValid($elementName)
    {
        return $this->chainValidation->elementIsValid($elementName);
    }

    /**
     * Check if children of an element are valid
     * Element's children will always be valid before the formContext is validated
     * @param $elementName
     * @return bool
     * @throws \UForm\Exception
     */
    public function childrenAreValid($elementName)
    {
        return $this->chainValidation->elementChildrenAreValid($elementName);
    }

    /**
     * Get the value of an element
     * @param $name
     * @return mixed
     */
    public function getValueFor($name)
    {
        return $this->getData()->findValue($name);
    }
}
