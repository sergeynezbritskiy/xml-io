<?php

declare(strict_types=1);

namespace SergeyNezbritskiy\XmlIo\tests;

use PHPUnit\Framework\TestCase;
use SergeyNezbritskiy\XmlIo\AbstractCore;
use SergeyNezbritskiy\XmlIo\XmlReader;

/**
 * Class AbstractCoreTest
 * @package SergeyN1ezbritskiy\XmlIo\tests
 */
class AbstractCoreTest extends TestCase
{
    /**
     * @var XmlReader
     */
    private $xmlReader;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->xmlReader = $this->getMockForAbstractClass(AbstractCore::class);
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
        $this->assertTrue($this->call('isAttribute', ['key' => '@attribute']));
        $this->assertFalse($this->call('isAttribute', ['key' => 'tag']));
        $this->assertFalse($this->call('isAttribute', ['key' => 'tag@']));
    }

    public function testIsArray()
    {
        $this->assertTrue($this->call('isArray', ['key' => 'users[]']));
        $this->assertTrue($this->call('isArray', ['key' => []]));
        $this->assertTrue($this->call('isArray', ['key' => [1, 2, 3]]));
        $this->assertFalse($this->call('isArray', ['key' => 'users']));
        $this->assertFalse($this->call('isArray', ['key' => null]));
        $this->assertFalse($this->call('isArray', ['key' => 1]));
    }

    public function testParseKey()
    {
        $this->assertEquals(['user', 'user'], $this->call('parseKey', ['key' => 'user']));
        $this->assertEquals(['users', 'users'], $this->call('parseKey', ['users[]']));
        $this->assertEquals(['document', 'passport'], $this->call('parseKey', ['key' => 'document as passport']));
        $this->assertEquals(['users', 'user'], $this->call('parseKey', ['key' => 'users as user[]']));
        $this->assertEquals([null, 'user'], $this->call('parseKey', ['key' => '{list} as user[]']));
    }

    /**
     * Call protected/private method of a class.
     *
     * @param string $methodName Method name to call
     * @param array $params Array of parameters to pass into method.
     *
     * @return mixed Method return.
     * @throws \ReflectionException
     */
    private function call($methodName, array $params = [])
    {

        $reflection = new \ReflectionClass(get_class($this->xmlReader));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($this->xmlReader, $params);
    }

}