# xml-io
Library for Parsing xml into php array using easy mapping. It allows you to parse simple data like strings and numbers, arrays or a list of items and complex data like objects. Also any combination of these data types is allowed.

## Installation
The easiest way to install module is using Composer
```
composer require sergeynezbritskiy/xml-io:^2.0.0
```
## Simple usage
The most useful test cases can be seen in tests

Here is the most generic example. Lets pretend we have such xml as below
```xml
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<user id="1">
    <name>Sergey</name>
    <born format="ISO">1988-20-12</born>
    <passport id="MN123456">
        <date>2000-12-12</date>
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

```
Here is an example of how to convert such xml into array:
```php
$xmlString = ' xml string from above ';
$xmlReader = new \SergeyNezbritskiy\XmlIo\XmlReader();
$result = $xmlReader->parseString($xmlString, [
    //array element with key `id` will be created from attribute `id`
    'id' => '@id',
    //array element with key `name` will be created from tag `name`
    'name' => 'name',
    'born' => 'born',
    'born_format' => 'born.@format',
    'passport' => [
        'id' => '@id',
        'date' => 'date',
    ],
    //create simple list of items
    'keywords as keywords.keyword' => '{list}',
    //create element `addresses` which will be an array of associative arrays
    'addresses as addresses.address[]' => [
        'city' => 'city',
        'country' => 'country',
    ]
]);
/*
the result will be smth like that:
$result = [
    'id' => '1',
    'name' => 'Sergey',
    'born' => '1988-20-12',
    'born_format' => 'ISO',
    'passport' => [
        'id' => 'MN123456',
        'date' => '2000-12-12',
    ],
    'keywords' => [
        'buono', 
        'brutto', 
        'cattivo'
    ],
    'addresses' => [
        [
            'city' => 'Kharkiv', 
            'country' => 'Ukraine'
        ],[
            'city' => 'London', 
            'country' => 'Great Britain'
        ],
    ]
];
*/
```

## Inspiration
Author was inspired for creating of this library by https://github.com/laravie/parser