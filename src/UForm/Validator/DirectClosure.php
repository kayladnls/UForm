<?php

namespace UForm\Validator;

use UForm\ValidationItem;
use UForm\Validator;

class DirectClosure extends Validator
{
    
    protected $closure;


    public function __construct(callable $closure, $options = null)
    {
        $this->closure = $closure;
        parent::__construct($options);
    }

    /**
     * Executes the validation
     *
     * @param ValidationItem $validator
     */
    public function validate(ValidationItem $validationItem)
    {
        $closure = $this->closure;
        return $closure($validationItem, $this);
    }
}
