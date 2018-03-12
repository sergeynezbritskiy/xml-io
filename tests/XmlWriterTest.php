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
    private $users;

    /**
     * @var array
     */
    private $user1;

    /**
     * @var array
     */
    private $user2;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->xmlWriter = new XmlWriter();
        $this->user1 = [
            'id' => '11235813',
            'name' => 'Sergey',
            'age' => 29,
            'gender' => 'male',
            'born' => '1988-01-01',
            'born_format' => 'ISO',
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
        $this->user2 = [
            'id' => '21345589',
            'name' => 'Victoria',
            'age' => 22,
            'gender' => 'female',
            'born' => '1988-01-01',
            'born_format' => 'ISO',
            'keywords' => [
                'beautiful',
                'wonderful',
                'smart',
            ],
            'passport' => [
                'id' => 'NM654321',
                'date' => '2007-04-13',
                'issued' => 'another organisation title',
            ],
            'addresses' => [
                [
                    'city' => 'New York',
                    'country' => 'USA',
                ],
            ]
        ];
        $this->users = [
            $this->user1,
            $this->user2,
        ];
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        $this->xmlWriter = null;
        $this->user1 = null;
        $this->user2 = null;
        $this->users = null;
    }

    //tests
    public function testArrayOfComplexTypes()
    {
        $map = [
            'users' => [
                'children' => [
                    'user[]' => [
                        'attributes' => ['id'],
                        'children' => [
                            'name',
                            'age',
                            'born' => [
                                'text' => 'born',
                                'attributes' => [
                                    'format' => [
                                        'text' => 'born_format'
                                    ],
                                ],
                            ],
                            'keywords' => [
                                'children' => [
                                    'keyword[]' => [
                                        'dataProvider' => 'keywords',
                                        'text' => '{self}',
                                    ],
                                ],
                            ],
                            'addresses' => [
                                'children' => [
                                    'address[]' => [
                                        'dataProvider' => 'addresses',
                                        'children' => ['city', 'country'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $expectedResult = <<<XML
<users>
    <user id="11235813">
        <name>Sergey</name>
        <age>29</age>
        <born format="ISO">1988-01-01</born>
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
    <user id="21345589">
        <name>Victoria</name>
        <age>22</age>
        <born format="ISO">1988-01-01</born>
        <keywords>
            <keyword>beautiful</keyword>
            <keyword>wonderful</keyword>
            <keyword>smart</keyword>
        </keywords>
        <addresses>
            <address>
                <city>New York</city>
                <country>USA</country>
            </address>
        </addresses>
    </user>
</users>
XML;
        $this->assertXmlEquals($map, $expectedResult);
    }

    public function testComplexType()
    {
        $map = [
            'user' => [
                'attributes' => ['id'],
                'children' => [
                    'name',
                    'age',
                    'born' => [
                        'text' => 'born',
                        'attributes' => [
                            'format' => [
                                'text' => 'born_format'
                            ],
                        ],
                    ],
                    'keywords' => [
                        'children' => [
                            'keyword[]' => [
                                'dataProvider' => 'keywords',
                                'text' => '{self}',
                            ],
                        ],
                    ],
                    'addresses' => [
                        'children' => [
                            'address[]' => [
                                'dataProvider' => 'addresses',
                                'children' => ['city', 'country'],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $expectedResult = <<<XML
<user id="11235813">
    <name>Sergey</name>
    <age>29</age>
    <born format="ISO">1988-01-01</born>
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
        $this->assertXmlEquals($map, $expectedResult, 'user1');
    }

    public function testSimpleElement()
    {
        $expectedResult = <<<XML
        <user>Sergey</user>
XML;
        $map = [
            'user' => ['text' => 'name'],
        ];
        $this->assertXmlEquals($map, $expectedResult, 'user1');
    }

    public function testShortSyntax()
    {
        $expectedResult = <<<XML
        <user>Sergey</user>
XML;
        $map = ['user' => 'name'];
        $this->assertXmlEquals($map, $expectedResult, 'user1');
    }

    public function testShortSyntaxCodeOnly()
    {
        $expectedResult = <<<XML
        <name>Sergey</name>
XML;
        $map = ['name'];
        $this->assertXmlEquals($map, $expectedResult, 'user1');
    }

    public function testAttribute()
    {
        $expectedResult = <<<XML
        <user id="11235813">Sergey</user>
XML;
        $map = [
            'user' => [
                'text' => 'name',
                'attributes' => [
                    'id' => [
                        'text' => 'id'
                    ],
                ],
            ],
        ];
        $this->assertXmlEquals($map, $expectedResult, 'user1');
    }

    public function testAttributeShortSyntax()
    {
        $expectedResult = <<<XML
        <user id="11235813">Sergey</user>
XML;
        $map = [
            'user' => [
                'text' => 'name',
                'attributes' => [
                    'id' => 'id'
                ],
            ],
        ];
        $this->assertXmlEquals($map, $expectedResult, 'user1');
    }

    public function testAttributeShortSyntaxOnlyKey()
    {
        $expectedResult = <<<XML
        <user id="11235813">Sergey</user>
XML;
        $map = [
            'user' => [
                'text' => 'name',
                'attributes' => ['id'],
            ],
        ];
        $this->assertXmlEquals($map, $expectedResult, 'user1');
    }

    public function testNestedNodes()
    {
        $expectedResult = <<<XML
        <user id="11235813">
            <name>Sergey</name>
            <age>29</age>
        </user>
XML;
        $map = [
            'user' => [
                'attributes' => ['id'],
                'children' => ['name', 'age'],
            ],
        ];
        $this->assertXmlEquals($map, $expectedResult, 'user1');
    }

    public function testNestedComplexNodes()
    {
        $expectedResult = <<<XML
        <user id="11235813">
            <name>Sergey</name>
            <age>29</age>
            <passport id="MN123456">
                <date>2000-12-20</date>
                <issued>organisation title</issued>
            </passport>
        </user>
XML;
        $map = [
            'user' => [
                'attributes' => ['id'],
                'children' => ['name', 'age', 'passport' => [
                    'dataProvider' => 'passport',
                    'attributes' => ['id'],
                    'children' => ['date', 'issued']
                ]],
            ],
        ];
        $this->assertXmlEquals($map, $expectedResult, 'user1');
    }

    public function testArrayShortSyntaxCodeOnly()
    {
        $expectedResult = <<<XML
        <users>
            <user>Sergey</user>
            <user>Victoria</user>
        </users>
XML;
        $map = [
            'users' => [
                'children' => [
                    'user[]' => [
                        'text' => 'name'
                    ],
                ],
            ]
        ];
        $this->assertXmlEquals($map, $expectedResult);
        $map = [
            'users' => [
                'children' => [
                    'user[]' => 'name'
                ],
            ]
        ];
        $this->assertXmlEquals($map, $expectedResult);
    }

    public function testArray()
    {
        $expectedResult = <<<XML
        <users>
            <user id="11235813">
                <name>Sergey</name>
                <age>29</age>
            </user>
            <user id="21345589">
                <name>Victoria</name>
                <age>22</age>
            </user>
        </users>
XML;
        $map = [
            'users' => [
                'children' => [
                    'user[]' => [
                        'attributes' => ['id'],
                        'children' => ['name', 'age']
                    ],
                ]
            ]
        ];
        $this->assertXmlEquals($map, $expectedResult);
    }

    public function testSimpleListOfTextNodes()
    {
        $expectedResult = <<<XML
        <users>
            <user id="11235813">
                <name>Sergey</name>
                <age>29</age>
                <keywords>
                    <keyword>buono</keyword>
                    <keyword>brutto</keyword>
                    <keyword>cattivo</keyword>
                </keywords>
            </user>
            <user id="21345589">
                <name>Victoria</name>
                <age>22</age>
                <keywords>
                    <keyword>beautiful</keyword>
                    <keyword>wonderful</keyword>
                    <keyword>smart</keyword>
                </keywords>
            </user>
        </users>
XML;
        $map = [
            'users' => [
                'children' => [
                    'user[]' => [
                        'attributes' => ['id'],
                        'children' => [
                            'name',
                            'age',
                            'keywords' => [
                                'children' => [
                                    'keyword[]' => [
                                        'dataProvider' => 'keywords',
                                        'text' => '{self}',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $this->assertXmlEquals($map, $expectedResult);
    }

    /**
     * @param array $map
     * @param string $expectedResult
     * @param string $data
     */
    private function assertXmlEquals($map, $expectedResult, $data = 'users')
    {
        $expectedResult = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . $expectedResult;
        $actualResult = $this->xmlWriter->toXmlString($this->$data, $map);
        $this->assertXmlStringEqualsXmlString($expectedResult, $actualResult);
    }

}