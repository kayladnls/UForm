<?php
/**
 * @license see LICENSE
 */

namespace UForm\Test\Form\Element;

use UForm\Form\Element\Container;
use UForm\Form\Element\Container\Group;
use UForm\Form\Element\Primary\Input\Hidden;
use UForm\Form\Element\Primary\Input\Password;
use UForm\Form\Element\Primary\Input\Text;
use UForm\ValidationItem;

class ContainerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Text
     */
    protected $userName;

    /**
     * @var Text
     */
    protected $item2;

    public function setUp()
    {
        $this->container = $this->getMockForAbstractClass("UForm\Form\Element\Container");
        $userName = new Text("username");
        $userName->addValidator(function (ValidationItem $v) {
            return $v->getValue() == "bart";

        });
        $password = new Password("password");

        $this->item2 = new Text("item2");

        $ungamedGroup = new Group();
        $ungamedGroup->addElement(new Text("item1"));
        $ungamedGroup->addElement($this->item2);

        $namedGroup = new Group("namedGroup");
        $namedGroup->addElement(new Text("item1"));
        $namedGroup->addElement(new Hidden("someItem"));

        $this->container
            ->expects($this->any())
            ->method('getElements')
            ->will($this->returnValue([$userName, $password, $ungamedGroup, $namedGroup]));
        $userName->setParent($this->container);
        $password->setParent($this->container);
        $ungamedGroup->setParent($this->container);
        $namedGroup->setParent($this->container);

        $this->userName = $userName;
    }

    public function testSanitizeData()
    {
        $data = $this->container->sanitizeData(["data" => "value"]);
        $this->assertEquals(["data" => "value"], $data);
    }

    public function testGetDirectElement()
    {
        $this->assertEquals($this->userName, $this->container->getDirectElement("username"));
        $this->assertEquals(null, $this->container->getDirectElement("fake"));

        // test direct element with children in an unnamed element
        $this->assertEquals($this->item2, $this->container->getDirectElement("item2"));
    }

    public function testHasDirectElementInstance()
    {
        $this->assertTrue($this->container->hasDirectElementInstance("UForm\Form\Element\Primary\Input\Text"));
        $this->assertTrue($this->container->hasDirectElementInstance("UForm\Form\Element\Primary\Input\Password"));
        $this->assertTrue($this->container->hasDirectElementInstance("UForm\Form\Element\Container\Group"));
        $this->assertFalse($this->container->hasDirectElementInstance("UForm\Form\Element\Primary\Input\Hidden"));
        $this->assertTrue(
            $this->container
                ->getDirectElement("namedGroup")
                ->hasDirectElementInstance("UForm\Form\Element\Primary\Input\Hidden")
        );
    }

    public function testHasDirectElementSemanticType()
    {
        $this->assertTrue($this->container->hasDirectElementSemanticType("input:text"));
        $this->assertTrue($this->container->hasDirectElementSemanticType("input:password"));
        $this->assertTrue($this->container->hasDirectElementSemanticType("group"));
        $this->assertFalse($this->container->hasDirectElementSemanticType("input:hidden"));
        $this->assertTrue(
            $this->container
                ->getDirectElement("namedGroup")
                ->hasDirectElementSemanticType("input:hidden")
        );
    }

    public function testSetParent()
    {
        /* @var $container Container */
        $container = $this->getMockForAbstractClass("UForm\Form\Element\Container", ["containerName"]);
        $text = new Text("textName");
        $text->setParent($container);
        $this->assertEquals("containerName.textName", $text->getName(true, true));

        $container
            ->expects($this->any())
            ->method('getElements')
            ->will($this->returnValue([$text]));
        $group = new Group("groupName");
        $container->setParent($group);
        $this->assertSame($group, $container->getParent());
        $this->assertEquals("groupName.containerName", $container->getName(true, true));
        $this->assertEquals("groupName.containerName.textName", $text->getName(true, true));
    }
}
