<?php

namespace UForm\Validation\Validator;

use \UForm\Validation\Validator,
	\UForm\Validation\Exception,
	\UForm\Validation;


/**
 * SameAs 
 * usefull for same password checking
 */
class SameAs extends Validator{
    
    protected $sameElement = null;


    public function __construct($sameAs , $options = null) {
        $this->sameElement = $sameAs;
        parent::__construct($options);
    }

    
    /**
     * @inheritdoc
     */
    public function validate(Validation $validator){
        
        $value1 = $validator->getValue();        
        $value2 = $validator->getValue($this->sameElement);
        
        if($value2 !== $value1){
            $validator->appendMessage($this->getOption('message'));
            return false;
        }

        return true;
    }
}