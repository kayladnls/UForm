<?php
/**
 * @license see LICENSE
 */

namespace UForm\Test;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{

    public function testConstruct()
    {

        $options = ["foo" => "bar", "qux" => "quux"];

        $validator = $this->getMockForAbstractClass("UForm\Validator", [$options]);
        $this->assertSame($options, $validator->getOptions());
    }
}
