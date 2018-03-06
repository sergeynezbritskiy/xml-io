<?php declare(strict_types=1);

namespace SergeyNezbritskiy\XmlIo\tests;

use PHPUnit\Framework\TestCase;
use SergeyNezbritskiy\XmlIo\XmlReaderAdvanced;

/**
 * Class XmlReaderAdvancedTest
 * @package SergeyNezbritskiy\XmlIo\tests
 */
class XmlReaderAdvancedTest extends TestCase
{

    /**
     * @var XmlReaderAdvanced
     */
    private $xmlReader;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->xmlReader = new XmlReaderAdvanced();
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        $this->xmlReader = null;
    }

    //tests

    public function testIsAttribute()
    {
        $this->assertTrue($this->invokeMethod($this->xmlReader, 'isAttribute', ['key' => '@attribute']));
        $this->assertFalse($this->invokeMethod($this->xmlReader, 'isAttribute', ['key' => 'tag']));
        $this->assertFalse($this->invokeMethod($this->xmlReader, 'isAttribute', ['key' => 'tag@']));
    }

    public function testIsArray()
    {
        $this->assertTrue($this->invokeMethod($this->xmlReader, 'isArray', ['key' => 'users[]']));
        $this->assertTrue($this->invokeMethod($this->xmlReader, 'isArray', ['key' => []]));
        $this->assertTrue($this->invokeMethod($this->xmlReader, 'isArray', ['key' => [1, 2, 3]]));
        $this->assertFalse($this->invokeMethod($this->xmlReader, 'isArray', ['key' => 'users']));
        $this->assertFalse($this->invokeMethod($this->xmlReader, 'isArray', ['key' => null]));
        $this->assertFalse($this->invokeMethod($this->xmlReader, 'isArray', ['key' => 1]));
    }

    /**
     * @param string $fileName
     * @param array $map
     * @param array $expectedResult
     */
    private function assertXmlEquals($fileName, $map, $expectedResult)
    {
        $actualResult = $this->xmlReader->parseFile(__DIR__ . '/data/' . $fileName, $map);
        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array $params Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    private function invokeMethod(&$object, $methodName, array $params = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $params);
    }
}