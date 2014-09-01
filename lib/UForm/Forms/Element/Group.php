<?php

namespace UForm\Forms\Element;

use UForm\Validation\ChainedValidation,
    UForm\Forms\Exception
    ;
use UForm\Forms\ElementContainer;

/**
 * Group that can contains many elements
 *
 * @author sghzal
 */
class Group extends ElementContainer{
    
    /**
     * @var \UForm\Forms\ElementInterface[]
     */
    protected $elements = array();

    public function __construct($name=null, $elements = null) {
        parent::__construct($name);
        if(is_array($elements)){
            foreach ($elements as $el){
                $this->addElement($el);
            }
        }else if(is_object ($elements)){
            $this->addElement($elements);
        }
    }
    
    public function addElement(\UForm\Forms\Element $element){
        $iname = "i" . count($this->elements);
        $this->elements[$iname] = $element;
        $element->setParent($this,$iname);
    }
    
    public function getName($prenamed = null, $dottedNotation = false) {
        if (null === $this->_name) {
            return $this->_prename;
        }

        return parent::getName($prenamed, $dottedNotation);
    }

    public function _render( $attributes , $values , $data , $prename = null ) {
        $render = "";
        
        foreach($this->elements as $v){
            $newPrename = $this->getName($prename);
            $hasName = null !== $this->_name ;
            
            if($hasName)
                $render .= $v->render( isset($attributes[$v->getName()]) ? $attributes[$v->getName()] : null , $values[$this->getName()] , $data, $newPrename);
            else
                $render .= $v->render( isset($attributes[$v->getName()]) ? $attributes[$v->getName()] : null , $values , $data, $newPrename);
        }
        
        return $render;
    }

    public function getElement($name){
        if (!is_array($name)) {
            $namesP = explode(".", $name);
        }else{
            $namesP = $name;
        }
        
        $finalElm = $this->getDirectElement($namesP[0]);
        
        if( $finalElm && count($namesP)>1){
            array_shift($namesP);
            return $finalElm->getElement(($namesP));
        }
        return $finalElm;
    }

    public function getDirectElement($name){
        foreach($this->elements as $elm){
            if( $name == $elm->getName()){
                return $elm;
            }else if( !$elm->getName() && $elm instanceof ElementContainer ){
                /* @var $elm UForm\Forms\ElementContainer */
                $element = $elm->getDirectElement($name);
                if($element)
                    return $element;
            }
        }
        return null;
    }
    

    public function getElements($values = null){
        return $this->elements;
    }


    public function prepareValidation($localValues,  ChainedValidation $cV){
        
        parent::prepareValidation($localValues, $cV);
        $localValues = (array) $localValues;
        foreach ($this->getElements() as $k=>$v){
            
            if($this->getName()){
                $values = isset($localValues[$this->getName()]) ? $localValues[$this->getName()] : null;
            }else{
                $values = $localValues;
            }
            
            
            $v->prepareValidation($values, $cV);
        }
        
    }
    
    public function childrenAreValid(ChainedValidation $cV) {
        
        foreach($this->getElements() as $el){
            
            $v = $cV->getValidation($el->getInternalName(true),true);
     
            if(!$v->isValid())
                return false;
            
            if(!$el->childrenAreValid($cV))
                return false;
            
            return true;
            
        }
        
    }

}
