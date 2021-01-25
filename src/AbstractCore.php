<?php

declare(strict_types=1);

namespace SergeyNezbritskiy\XmlIo;

/**
 * Class AbstractCore
 * @package SergeyNezbritskiy\XmlIo
 */
abstract class AbstractCore
{
    /**
     * This is a key for defining non associative arrays
     * Can be used as in keys as in values
     */
    const KEY_LIST = '{list}';

    /**
     * Returns true either $key is array or is string with suffix `[]`
     *
     * @param array|string $key
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

    /**
     * @param string $key
     * @return array
     */
    protected function parseKey(string $key): array
    {
        $keyParts = explode(' as ', $key);
        if (count($keyParts) !== 2) {
            $keyParts = [$key, $key];
        }
        if ($keyParts[0] === self::KEY_LIST) {
            $keyParts[0] = null;
        }
        array_walk($keyParts, function (&$key) {
            if (is_string($key) && substr($key, -2) === '[]') {
                $key = substr($key, 0, -2);
            }
        });
        return $keyParts;
    }
}
