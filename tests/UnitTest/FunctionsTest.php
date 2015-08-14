<?php
/**
 * PHP-DI
 *
 * @link      http://php-di.org/
 * @copyright Matthieu Napoli (http://mnapoli.fr/)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

namespace DI\Test\UnitTest;

use DI\Definition\ArrayDefinition;
use DI\Definition\ArrayDefinitionExtension;
use DI\Definition\EnvironmentVariableDefinition;
use DI\Definition\ObjectDefinition;
use DI\Definition\Helper\ObjectDefinitionHelper;
use DI\Definition\StringDefinition;
use DI\Definition\ValueDefinition;

/**
 * Tests the helper functions.
 */
class FunctionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::\DI\value
     */
    public function test_value()
    {
        $definition = \DI\value('foo');

        $this->assertTrue($definition instanceof ValueDefinition);
        $this->assertEquals('foo', $definition->getValue());
    }

    /**
     * @covers ::\DI\object
     */
    public function test_object()
    {
        $helper = \DI\object();

        $this->assertTrue($helper instanceof ObjectDefinitionHelper);
        $definition = $helper->getDefinition('entry');
        $this->assertTrue($definition instanceof ObjectDefinition);
        $this->assertEquals('entry', $definition->getClassName());

        $helper = \DI\object('foo');

        $this->assertTrue($helper instanceof ObjectDefinitionHelper);
        $definition = $helper->getDefinition('entry');
        $this->assertTrue($definition instanceof ObjectDefinition);
        $this->assertEquals('foo', $definition->getClassName());
    }

    /**
     * @covers ::\DI\factory
     */
    public function test_factory()
    {
        $helper = \DI\factory(function () {
            return 42;
        });

        $this->assertInstanceOf('DI\Definition\Helper\FactoryDefinitionHelper', $helper);
        $definition = $helper->getDefinition('entry');
        $this->assertInstanceOf('DI\Definition\FactoryDefinition', $definition);
        $callable = $definition->getCallable();
        $this->assertEquals(42, $callable());
    }

    /**
     * @covers ::\DI\decorate
     */
    public function test_decorate()
    {
        $helper = \DI\decorate(function () {
            return 42;
        });

        $this->assertInstanceOf('DI\Definition\Helper\FactoryDefinitionHelper', $helper);
        $definition = $helper->getDefinition('entry');
        $this->assertInstanceOf('DI\Definition\DecoratorDefinition', $definition);
        $callable = $definition->getCallable();
        $this->assertEquals(42, $callable());
    }

    /**
     * @covers ::\DI\get
     */
    public function test_get()
    {
        $definition = \DI\get('foo');

        $this->assertInstanceOf('DI\Definition\AliasDefinition', $definition);
        $this->assertEquals('foo', $definition->getTargetEntryName());
    }

    /**
     * @covers ::\DI\link
     */
    public function test_link()
    {
        $definition = \DI\link('foo');

        $this->assertInstanceOf('DI\Definition\AliasDefinition', $definition);
        $this->assertEquals('foo', $definition->getTargetEntryName());
    }

    /**
     * @covers ::\DI\env
     */
    public function test_env()
    {
        $definition = \DI\env('foo');
        $definition->setName('entry');

        $this->assertTrue($definition instanceof EnvironmentVariableDefinition);
        $this->assertEquals('entry', $definition->getName());
        $this->assertEquals('foo', $definition->getVariableName());
        $this->assertFalse($definition->isOptional());
    }

    /**
     * @covers ::\DI\env
     */
    public function test_env_default_value()
    {
        $definition = \DI\env('foo', 'default');
        $definition->setName('entry');

        $this->assertTrue($definition instanceof EnvironmentVariableDefinition);
        $this->assertEquals('entry', $definition->getName());
        $this->assertEquals('foo', $definition->getVariableName());
        $this->assertTrue($definition->isOptional());
        $this->assertEquals('default', $definition->getDefaultValue());
    }

    /**
     * @covers ::\DI\env
     */
    public function test_env_default_value_null()
    {
        $definition = \DI\env('foo', null);
        $definition->setName('entry');

        $this->assertTrue($definition instanceof EnvironmentVariableDefinition);
        $this->assertEquals('entry', $definition->getName());
        $this->assertEquals('foo', $definition->getVariableName());
        $this->assertTrue($definition->isOptional());
        $this->assertSame(null, $definition->getDefaultValue());
    }

    /**
     * @covers ::\DI\add
     */
    public function test_add_value()
    {
        $definition = \DI\add('hello');
        $definition->setName('foo');

        $this->assertTrue($definition instanceof ArrayDefinitionExtension);
        $this->assertEquals('foo', $definition->getName());
        $this->assertEquals('foo', $definition->getSubDefinitionName());
        $definition->setSubDefinition(new ArrayDefinition(['tom']));
        $this->assertEquals(['tom', 'hello'], $definition->getValues());
    }

    /**
     * @covers ::\DI\add
     */
    public function test_add_array()
    {
        $definition = \DI\add(['hello', 'world']);
        $definition->setName('foo');

        $this->assertTrue($definition instanceof ArrayDefinitionExtension);
        $this->assertEquals('foo', $definition->getName());
        $this->assertEquals('foo', $definition->getSubDefinitionName());
        $definition->setSubDefinition(new ArrayDefinition(['tom']));
        $this->assertEquals(['tom', 'hello', 'world'], $definition->getValues());
    }

    /**
     * @covers ::\DI\string
     */
    public function test_string()
    {
        $definition = \DI\string('bar');
        $definition->setName('foo');

        $this->assertTrue($definition instanceof StringDefinition);
        $this->assertEquals('foo', $definition->getName());
        $this->assertEquals('bar', $definition->getExpression());
    }
}
