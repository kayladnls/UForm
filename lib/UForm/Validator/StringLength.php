<?php

namespace UForm\Validator;

use UForm\Validation;
use UForm\Validator;


class StringLength extends Validator{

    private $_minLength;
    private $_maxLength;

    function __construct($minLength = 0, $maxLength = 0, $options = [])
    {
        $this->_minLength = $minLength;
        $this->_maxLength = $maxLength;

        parent::__construct($options);
    }


    /**
     * @inheritdoc
     */
    public function validate(Validation $validator){

        $value = $validator->getValue();

        if($this->_minLength > 0 && strlen($value) < $this->_minLength){
            $validator->appendMessage(
                $this->getOption('string-too-short', 'String too short (less than %_length_% character)'),
                null,
                ["length" => $this->_minLength]
            );
            return false;
        }
        if($this->_maxLength > 0 && strlen($value) > $this->_maxLength){
            $validator->appendMessage(
                $this->getOption('string-too-short', 'String too long (more than %_length_% character)'),
                null,
                ["length" => $this->_maxLength]
            );
            return false;
        }


        return true;
    }
}