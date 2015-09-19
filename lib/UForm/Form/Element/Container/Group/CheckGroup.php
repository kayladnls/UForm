<?php

namespace UForm\Form\Element\Container\Group;

use UForm\Form\Element\Check;
use UForm\Form\Element\Container\Group;

/**
 * Class CheckGroup
 * @semanticType checkGroup
 */
class CheckGroup extends Group
{
    
    protected $values;

    /**
     *
     * @param string $name name of the checkbox. Just type "name" and it will generate some "name[]"
     * @param string $elementsDefinition list of checkboxes to create
     * @throws \UForm\Form\Exception
     */
    public function __construct($name, $elementsDefinition)
    {
        
        $elements = [];
        
        $i = 0;
        
        foreach ($elementsDefinition as $k => $v) {
            if (is_string($v)) {
                $elements[] = new Check($i, $v);
            } elseif (is_array($v)) {
                $elements[] = new Check(
                    $i,
                    $v["value"],
                    isset($v["attributes"]) ? $v["attributes"] : null,
                    isset($v["validators"]) ? $v["validators"] : null,
                    isset($v["filters"])    ? $v["filters"]    : null
                );
            } else {
                throw new \UForm\Form\Exception("Unvalid type for checkgroup creation");
            }
            $i++;
        }

        parent::__construct($name, $elements);
        $this->addSemanticType("checkGroup");
    }
}
