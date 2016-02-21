<?php

/*
 * This File is part of the Thapp\Jmg\Http\Psr7 package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Jmg\Http\Psr7;

use Thapp\Jmg\Resource\ImageResourceInterface;

/**
 * @class NotModifiedImageResonse
 *
 * @package Thapp\Jmg\Http\Psr7
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class NotModifiedImageResonse extends ImageResponse
{
    /** @var array */
    const STATUS = [304 => 'Not Modified'];

    /** @var Psr\Http\Message\StreamInterface */
    private $body;

    /** @var array */
    private static $excludeHeaders = [
        'allow', 'last-modified', 'content-md5', 'content-encoding',
        'content-lenght', 'content-type'
    ];


    /**
     * {@inheritdoc}
     */
    public function getReasonPhrase()
    {
        return self::STATUS[$this->getStatusCode()];
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        return key(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareHeaders(array $headers)
    {
        return array_merge($this->getDefaultHeader(), array_filter($headers, function ($key) {
            return !in_array(strtolower($key), self::$excludeHeaders);
        }, ARRAY_FILTER_USE_KEY), ['content-length' => 0]);
    }
}
