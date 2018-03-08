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
     * @var array
     */
    private $users;

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
        $this->users = [
            [
                'id' => 1,
                'name' => 'Sergey',
                'age' => 29,
            ],
            [
                'id' => 2,
                'name' => 'Victoria',
                'age' => 23,
            ],
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

    public function testNestedEntities()
    {
        $expectedResult = <<<XML
<user id="11235813">
    <name>Sergey</name>
    <passport id="MN123456">
        <date>2000-12-20</date>
    </passport>
</user>
XML;
        $this->assertXmlEquals([
            '@id' => 'id',
            'name' => 'name',
            'passport' => [
                '@id' => 'id',
                'date' => 'date',
            ]
        ], $expectedResult);

    }

    public function testListOfEntities()
    {
        $expectedResult = <<<XML
<user>
    <name>Sergey</name>
    <addresses>
        <address>
            <city>Kharkiv</city>
            <country>Ukraine</country>
        </address>
        <address>
            <city>London</city>
            <country>Great Britain</country>
        </address>
    </addresses>
</user>
XML;
        $this->assertXmlEquals([
            'name' => 'name',
            'addresses as address[]' => [
                'city' => 'city',
                'country' => 'country',
            ]
        ], $expectedResult);

    }

    public function testList()
    {
        $expectedResult = <<<XML
<user>
    <name>Sergey</name>
    <keywords>
        <keyword>buono</keyword>
        <keyword>brutto</keyword>
        <keyword>cattivo</keyword>
    </keywords>
</user>
XML;
        $this->assertXmlEquals([
            'name' => 'name',
            'keywords as keyword[]' => 'keyword'
        ], $expectedResult);

    }

    public function fullTest()
    {
        $expectedResult = <<<XML
<user id="11235813">
    <name>Sergey</name>
    <age>29</age>
    <gender>male</gender>
    <passport id="MN123456">
        <date>2000-12-20</date>
        <issued>organisation title</issued>
    </passport>
    <keywords>
        <keyword>buono</keyword>
        <keyword>brutto</keyword>
        <keyword>cattivo</keyword>
    </keywords>
    <addresses>
        <address>
            <city>Kharkiv</city>
            <country>Ukraine</country>
        </address>
        <address>
            <city>London</city>
            <country>Great Britain</country>
        </address>
    </addresses>
</user>
XML;
        $this->assertXmlEquals([
            '@id' => 'id',
            'name',
            'age',
            'gender',
            'passport' => [
                '@id' => 'id',
                'date',
                'issued',
            ],
            'keywords as keyword[]' => 'keyword',
            'addresses as address[]' => [
                'city',
                'country',
            ]

        ], $expectedResult);
    }

    public function testChangeKey()
    {
        $expectedResult = <<<XML
<user identifier="11235813">
    <user_name>Sergey</user_name>
    <how_old_are_you>29</how_old_are_you>
</user>
XML;
        $this->assertXmlEquals([
            '@identifier' => 'id',
            'user_name' => 'name',
            'how_old_are_you' => 'age',
        ], $expectedResult);
    }

    public function testListOnTopLevel()
    {
        $this->markTestSkipped('Not supported yet');
        $expectedResult = <<<XML
<users>
    <user id="1">
        <name>Sergey</name>
        <age>29</age>
    </user>
    <user id="2">
        <name>Victoria</name>
        <age>23</age>
    </user>
</users>
XML;

        $expectedResult = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . $expectedResult;
        $actualResult = $this->xmlWriter->toXml($this->users, 'users as user[]', [
            '@identifier' => 'id',
            'user_name' => 'name',
            'how_old_are_you' => 'age',
        ]);
        $this->assertXmlStringEqualsXmlString($expectedResult, $actualResult);
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