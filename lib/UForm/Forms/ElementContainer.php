<?php

namespace UForm\Forms;

/**
 *
 * Element that mays contain other elements
 *
 * Class ElementContainer
 * @package UForm\Forms
 * @semanticType container
 */
abstract class ElementContainer extends Element {

    public function __construct($name = null, $attributes = null, $validators = null, $filters = null)
    {
        parent::__construct($name, $attributes, $validators, $filters);
        $this->addSemanticType("container");
    }


    /**
     * @param $name
     * @return Element
     */
    abstract public function getElement($name);

    /**
     * @param null|array $values used for the "collection" element that is rendered according to a value set
     * @return Element[]
     */
    abstract public function getElements($values=null);
    
    public function setParent(ElementContainer $p, $iname = null) {
        parent::setParent($p, $iname);
        
        foreach ($this->getElements() as $el){
            $el->setParent($this);
        }
        
    }

    /**
     * check if this element contains an element that is an instance of the given type
     * @param string $className the name of the class to search for
     * @return bool
     */
    public function hasDirectElementType($className){
        foreach($this->getElements() as $el){
            if(is_a($el,$className)){
                return true;
            }
        }
        return false;
    }

}