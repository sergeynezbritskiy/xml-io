<?php declare(strict_types=1);

namespace SergeyNezbritskiy\XmlIo;

use SimpleXMLElement;

/**
 * Class XmlReaderAdvanced
 * @package SergeyNezbritskiy\XmlIo
 */
class XmlReaderAdvanced
{

    /**
     * XmlReader constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param string $filePath
     * @param array $map
     * @return array
     */
    public function parseFile(string $filePath, array $map): array
    {
        return $this->parseString(file_get_contents($filePath), $map);
    }

    /**
     * @param string $xml
     * @param array $map
     * @return array
     */
    public function parseString(string $xml, array $map): array
    {
        return $this->parse(simplexml_load_string($xml), $map);
    }

    /**
     * @param SimpleXMLElement $xml
     * @param $map
     * @return array
     */
    public function parse(SimpleXMLElement $xml, $map): array
    {
        $result = [];
        return $result;
    }

    /**
     * Returns true either $key is array or is string with suffix `[]`
     *
     * @param string $key
     * @return bool
     */
    private function isArray($key): bool
    {
        return is_array($key) || (substr((string)$key, -2) === '[]');
    }

    /**
     * Returns true if $key starts with `@` which means
     * that xml element attribute requested
     *
     * @param string $key
     * @return bool
     */
    private function isAttribute(string $key): bool
    {
        return strpos($key, '@') === 0;
    }

}