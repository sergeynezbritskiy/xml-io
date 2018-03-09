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

    private $user1;

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
                    'city' => 'New-York',
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
    public function testSimpleElement()
    {
        $expectedResult = <<<XML
<user>Sergey</user>
XML;
        $map = [
            'user' => [
                'data' => 'name',
            ],
        ];
        $this->assertXmlEquals($map, $expectedResult, 'user1');
    }

    public function testShortSyntax()
    {
        $expectedResult = <<<XML
<user>Sergey</user>
XML;
        $map = [
            'user' => 'name'
        ];
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
                'data' => 'name',
                'attributes' => [
                    'id' => [
                        'data' => 'id'
                    ]
                ]
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
                'data' => 'name',
                'attributes' => [
                    'id' => 'id'
                ]
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
                'data' => 'name',
                'attributes' => ['id']
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
                'items' => ['name', 'age']
            ],
        ];
        $this->assertXmlEquals($map, $expectedResult, 'user1');
    }

    /*/
    public function testArray()
    {
        $expectedResult = <<<XML
<users>
    <user id="11235813">
        <name>Sergey</name>
        <age>29</age>
    </user>
    <user id="11235813">
        <name>Victoria</name>
        <age>22</age>
    </user>
</users>
XML;
        $map = [
            'users' => [
                'items' => [
                    'user[]' => [
                        'attributes' => ['id'],
                        'items' => ['name', 'age']
                    ],
                ]
            ]
        ];
        $this->assertXmlEquals($map, $expectedResult, 'user1');
    }
    //*/

    /*/
    public function testComplexType()
    {
        $this->markTestSkipped();
        $map = [
            'users' => [
                'items' => [
                    'user[]' => [
                        'attributes' => [
                            'id' => [
                                'data' => 'id'
                            ],
                        ],
                        'items' => [
                            'name' => [
                                'data' => 'name',
                            ],
                            'born' => [
                                'data' => 'born',
                                'attributes' => [
                                    'type' => [
                                        'data' => 'born_format'
                                    ]
                                ],
                            ],
                            'keywords' => [
                                'data' => 'keywords',
                                'items' => [
                                    'keyword[]' => [
                                        'type' => 'text',
                                    ],
                                ],
                            ],
                            'addresses' => [
                                'data' => 'addresses',
                                'items' => [
                                    'address[]' => [
                                        'items' => [
                                            'street' => [
                                                'type' => 'text',
                                                'data' => 'street',
                                            ],
                                            'city' => [
                                                'type' => 'text',
                                                'data' => 'city',
                                            ],
                                        ]
                                    ]
                                ]
                            ]
                        ],
                    ],
                ],
            ],
        ];
        $expectedResult = <<<XML
XML;
        $this->assertXmlEquals($map, $expectedResult);
    }
    //*/

    /**
     * @param array $map
     * @param string $expectedResult
     * @param string $data
     */
    private function assertXmlEquals($map, $expectedResult, $data = 'users')
    {
        $expectedResult = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . $expectedResult;
        $actualResult = $this->xmlWriter->toXml($this->$data, $map);
        $this->assertXmlStringEqualsXmlString($expectedResult, $actualResult);
    }

}