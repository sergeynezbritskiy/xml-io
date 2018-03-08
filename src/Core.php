<?php

namespace SergeyNezbritskiy\XmlIo;

/**
 * Class Core
 * @package SergeyNezbritskiy\XmlIo
 */
class Core
{

    /**
     * This is a key for defining non associative arrays
     * Can be used as in keys as in values
     */
    const KEY_LIST = '{list}';

    /**
     * Returns true either $key is array or is string with suffix `[]`
     *
     * @param string $key
     * @return bool
     */
    protected function isArray($key): bool
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
    protected function isAttribute(string $key): bool
    {
        return strpos($key, '@') === 0;
    }

}