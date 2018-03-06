<?php declare(strict_types=1);

namespace SergeyNezbritskiy\XmlIo\tests;

use PHPUnit\Framework\TestCase;
use SergeyNezbritskiy\XmlIo\XmlReader;

/**
 * Class XmlReaderAdvancedTest
 * @package SergeyNezbritskiy\XmlIo\tests
 */
class XmlReaderTest extends TestCase
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
        $this->xmlReader = new XmlReader();
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        $this->xmlReader = null;
    }

    //tests

    public function testParseSimpleXmlString()
    {
        $this->assertXmlEquals('sample_item.xml', [
            'id' => '@id',
            'name' => 'name',
        ], [
            'id' => '11235813',
            'name' => 'Sergey'
        ]);
    }

    public function testComplexType()
    {
        $this->assertXmlEquals('sample_item.xml', [
            'id' => '@id',
            'name' => 'name',
            'passport' => [
                'id' => '@id',
                'date' => 'date',
                'issued' => 'issued',
            ]
        ], [
            'id' => '11235813',
            'name' => 'Sergey',
            'passport' => [
                'id' => 'MN123456',
                'date' => '2000-12-12',
                'issued' => 'organization title',
            ]
        ]);
    }

    public function testNestedArray()
    {
        $this->assertXmlEquals('sample_item.xml', [
            'id' => '@id',
            'name' => 'name',
            'addresses as addresses.address[]' => [
                'city' => 'city',
                'country' => 'country',
            ]
        ], [
            'id' => '11235813',
            'name' => 'Sergey',
            'addresses' => [
                ['city' => 'Kharkiv', 'country' => 'Ukraine'],
                ['city' => 'London', 'country' => 'Great Britain'],
            ]
        ]);
    }

    public function testAssocArray()
    {
        $this->assertXmlEquals('sample_item.xml', [
            'id' => '@id',
            'name' => 'name',
            'keywords as keywords.keyword' => '{list}'
        ], [
            'id' => '11235813',
            'name' => 'Sergey',
            'keywords' => [
                'buono',
                'brutto',
                'cattivo',
            ]
        ]);
    }

    public function testFullComplexData()
    {
        $this->assertXmlEquals('sample_item.xml', [
            'id' => '@id',
            'name' => 'name',
            'age' => 'age',
            'born' => 'born',
            'born_format' => 'born.@format',
            'passport' => [
                'id' => '@id',
                'date' => 'date',
                'issued' => 'issued',
            ],
            'keywords as keywords.keyword' => '{list}',
            'addresses as addresses.address[]' => [
                'city' => 'city',
                'country' => 'country',
            ]
        ], [
            'id' => '11235813',
            'name' => 'Sergey',
            'age' => '29',
            'born' => '1988-20-12',
            'born_format' => 'ISO',
            'passport' => [
                'id' => 'MN123456',
                'date' => '2000-12-12',
                'issued' => 'organization title',
            ],
            'keywords' => ['buono', 'brutto', 'cattivo'],
            'addresses' => [
                ['city' => 'Kharkiv', 'country' => 'Ukraine'],
                ['city' => 'London', 'country' => 'Great Britain'],
            ]
        ]);
    }

    public function testParseKey()
    {
        $this->assertEquals(['user', 'user'], $this->call('parseKey', ['key' => 'user']));
        $this->assertEquals(['users', 'users'], $this->call('parseKey', ['users[]']));
        $this->assertEquals(['document', 'passport'], $this->call('parseKey', ['key' => 'document as passport']));
        $this->assertEquals(['users', 'user'], $this->call('parseKey', ['key' => 'users as user[]']));
        $this->assertEquals([null, 'user'], $this->call('parseKey', ['key' => '{assoc} as user[]']));
    }

    public function testGetAttribute()
    {
        $this->assertNodeEquals('name', 'Sergey');
        $this->assertNodeEquals('passport.date', '2000-12-12');
        $this->assertNodeEquals('@id', '11235813');
        $this->assertNodeEquals('passport.@id', 'MN123456');
    }

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

    /**
     * @param $key
     * @param $expectedResult
     * @throws \ReflectionException
     */
    private function assertNodeEquals($key, $expectedResult)
    {
        $xml = simplexml_load_string(file_get_contents(__DIR__ . '/data/sample_item.xml'));
        $this->assertEquals($expectedResult, (string)$this->call('getNode', [
            'xml' => $xml,
            'key' => $key,
        ]));
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