<?php declare(strict_types=1);

namespace SergeyNezbritskiy\XmlIo\tests;

use PHPUnit\Framework\TestCase;
use SergeyNezbritskiy\XmlIo\XmlWriter;

/**
 * Class XmlWriterTest
 * @package SergeyN1ezbritskiy\XmlIo\tests
 */
class XmlWriterTest extends TestCase
{

    /**
     * @var XmlWriter
     */
    private $xmlWriter;

    /**
     * @var array
     */
    private $user;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->xmlWriter = new XmlWriter();
        $this->user = [
            'id' => '11235813',
            'name' => 'Sergey',
            'age' => 29,
            'gender' => 'male',
            'keywords' => [
                'buono',
                'brutto',
                'cattivo',
            ],
            'passport' => [
                'id' => 'MN123456',
                'date' => '2000-12-20',
                'issued' => 'organisation title',
            ],
            'addresses' => [
                [
                    'city' => 'Kharkiv',
                    'country' => 'Ukraine',
                ],
                [
                    'city' => 'London',
                    'country' => 'Great Britain'
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        $this->xmlWriter = null;
        $this->user = null;
    }

    //tests
    public function testEmptyMap()
    {
        $expectedResult = <<<XML
<user/>
XML;
        $this->assertXmlEquals([], $expectedResult);
    }

    public function testSimpleString()
    {
        $expectedResult = <<<XML
<user>Sergey</user>
XML;
        $this->assertXmlEquals(['name'], $expectedResult);
    }

    public function testSimpleXml()
    {
        $expectedResult = <<<XML
<user id="11235813">
    <name>Sergey</name>
    <age>29</age>
</user>
XML;
        $this->assertXmlEquals([
            '@id' => 'id',
            'name' => 'name',
            'age' => 'age',
        ], $expectedResult);
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
        $reflection = new \ReflectionClass(get_class($this->xmlWriter));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($this->xmlWriter, $params);
    }

    /**
     * @param array $map
     * @param string $expectedResult
     * @param string $root
     */
    private function assertXmlEquals($map, $expectedResult, $root = 'user')
    {
        $expectedResult = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . $expectedResult;
        $actualResult = $this->xmlWriter->toXml($this->$root, $root, $map);
        $this->assertXmlStringEqualsXmlString($expectedResult, $actualResult);
    }

}