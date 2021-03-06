<?php
/**
 * Element
 */
namespace UForm\Form;

use UForm\DataContext;
use UForm\FilterGroup;
use UForm\Form;
use UForm\Form\Element\Container;
use UForm\InvalidArgumentException;
use UForm\OptionGroup;
use UForm\SemanticItem;
use UForm\Validation;
use UForm\ValidationItem;
use UForm\ValidatorGroup;

/**
 * This is a base class for form elements
 *
 * @semanticType element
 */
abstract class Element
{
    use SemanticItem;
    use FilterGroup;
    use ValidatorGroup;
    use OptionGroup;

    /**
     * @var \UForm\Form\Element\Container
     */
    protected $parent;

    protected $prename;
    protected $name;

    protected $internalPrename;
    protected $internalName;

    protected $attributes = [];


    /**
     *
     * @var Form
     */
    protected $form;

    /**
     * \UForm\Form\Element constructor
     *
     * @param string $name
     * @param array|null $attributes
     * @throws Exception
     */
    public function __construct($name = null, $attributes = null, $validators = null, $filters = null)
    {
        $this->name = $name;

        if (is_array($attributes) === true) {
            $this->attributes = $attributes;
        }

        if (null !== $validators) {
            $this->addValidators($validators);
        }

        if (null !== $filters) {
            foreach ($filters as $f) {
                $this->addFilter($f);
            }
        }

        $this->internalName = 0;
        $this->addSemanticType("element");
    }


    public function addClass($className)
    {

        $currentClass = $this->getAttribute("class");

        if ($currentClass) {
            $currentClass .= " ";
        }

        $currentClass .= $className;

        $this->setAttribute("class", $currentClass);

    }

    /////////
    //
    // PARENT
    //

    /**
     * Internal use only. Set a pointer to the parent element
     * @param Form\Element\Container $parent
     * @return $this
     * @throws Exception
     */
    public function setParent(Container $parent)
    {
        $this->form = $parent->getForm();
        $this->parent = $parent;
        $this->refreshParent();
        return $this;
    }

    /**
     * Internal use only. Should be called when a change occurs
     * on the parents and the info related to the parent need to be updated
     */
    public function refreshParent()
    {
        if ($this->parent) {
            $this->setNamespace($this->parent->getName(true, true));
            $this->setInternalNamespace($this->parent->getInternalName(true));
        }
    }

    /**
     * Internal use only. Set the namespace (parent dependant)
     * @param $namespace
     */
    public function setNamespace($namespace)
    {
        $this->prename = $namespace;
    }

    /**
     * Internal use only. Set the internal namespace (parent dependant)
     * @param $namespace
     */
    public function setInternalNamespace($namespace)
    {
        $this->internalPrename = $namespace;
    }

    /**
     * Internal use only. Set the internal name (parent dependant)
     * @param $name
     */
    public function setInternalName($name)
    {
        $this->internalName = $name;
    }

    /**
     * Get the form the element belongs to
     * @return Form the form the element belongs to
     */
    public function getForm()
    {
        return $this->form;
    }


    /**
     * Get the parent Element
     * @return Container the container that contains the element
     */
    public function getParent()
    {
        return $this->parent;
    }





    /////////////
    //
    // NAME / INAME
    //

    /**
     * Change the name of the element
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param bool $namespaced if set to true it will return the name of the element with its namespace.
     * The namespace it the name of all the parent elements
     * @param bool $dottedNotation if set to true will return the nameme in a dotted notation,
     * else it will use the html valid array notation
     * @return mixed|null|string
     */
    public function getName($namespaced = false, $dottedNotation = false)
    {
        if ($namespaced && !empty($namespaced) && $this->prename && !empty($this->prename)) {
            if ($dottedNotation) {
                return $this->prename . "." . $this->name;
            } else {
                $ppart = explode(".", $this->prename);
                $ppart[] = $this->name;
                $final = array_shift($ppart);
                $final .= "[" . implode("][", $ppart) . "]";
                return $final;
            }
        } else {
            return $this->name;
        }
    }

    public function getInternalName($namespaced = false)
    {
        if ($namespaced && !empty($namespaced) && $this->internalPrename && !empty($this->internalPrename)) {
            return $this->internalPrename . "." . $this->internalName;
        } else {
            return $this->internalName;
        }
    }


    //////////////
    // ATTRIBUTES
    //

    /**
     * Sets a value for an attribute. Will replace the current one if it already exists
     *
     * @param string $attribute name of the attribute
     * @param string $value value of the attribute
     * @return $this
     * @throws Exception
     */
    public function setAttribute($attribute, $value)
    {
        if (is_string($attribute) === false) {
            throw new InvalidArgumentException("attribute", "string", $attribute);
        }

        $this->attributes[$attribute] = $value;

        return $this;
    }

    /**
     * Returns the value of an attribute if present
     *
     * You can specify a default value to return if the attribute does not exist
     *
     * @param string $attributeName name of the attribute
     * @param mixed $defaultValue value to return if the attribute does not exist
     * @return string the value of the attribute
     * @throws InvalidArgumentException
     */
    public function getAttribute($attributeName, $defaultValue = null)
    {
        if (is_string($attributeName) === false) {
            throw new InvalidArgumentException("attributeName", "string", $attributeName);
        }
        if (isset($this->attributes[$attributeName])) {
            return $this->attributes[$attributeName];
        }
        return $defaultValue;
    }

    /**
     * Sets values for many attributes
     *
     * @param array $attributes list of attributes to add ["name" => "value"]
     * @return $this
     * @throws Exception
     */
    public function addAttributes($attributes)
    {
        if (!is_array($attributes)) {
            throw new InvalidArgumentException("attributes", "array", $attributes);
        }
        foreach ($attributes as $attribute => $value) {
            $this->setAttribute($attribute, $value);
        }
        return $this;
    }

    /**
     * Returns the attributes for the element
     * @return array attributes of the element
     */
    public function getAttributes()
    {
        return $this->attributes;
    }


    /**
     * Internal use - prepare the validation object
     *
     * @param DataContext $localValues
     * @param FormContext $formContext
     * @throws \UForm\Exception
     */
    public function prepareValidation(DataContext $localValues, FormContext $formContext)
    {
        $validators = $this->getValidators();
        $v = new ValidationItem($localValues, $this, $formContext);
        $v->addValidators($validators);
        $formContext->getChainedValidation()->addValidation($v);
    }
}
