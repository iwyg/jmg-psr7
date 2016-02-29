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

use Psr\Http\Message\StreamInterface;
use Thapp\Jmg\Resource\ImageResourceInterface;

/**
 * @class ImageStream
 *
 * @package Thapp\Jmg\Http\Psr7
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ImageStream implements StreamInterface
{
    private $resource;
    public function __construct(ImageResourceInterface $image)
    {
        $this->setResource($image);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getContents();
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        if (null === $this->resource) {
            return;
        }

        fclose($this->resource);
    }

    /**
     * {@inheritdoc}
     */
    public function detach()
    {
        $resource = $this->resource;
        $this->resource = null;

        return $this->resource;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        if (null === $this->resource) {
            return null;
        }

        $stat = fstat($this->resource);

        return $stat['size'];
    }

    /**
     * {@inheritdoc}
     */
    public function tell()
    {
        $this->throwOnNull('Resource is null.');

        return ftell($this->resource);
    }

    /**
     * {@inheritdoc}
     */
    public function eof()
    {
        if (null === $this->resource) {
            return true;
        }

        return feof($this->resource);
    }

    /**
     * {@inheritdoc}
     */
    public function isSeekable()
    {
        return null !== $this->resource;
    }

    public function seek($offset, $whence = SEEK_SET)
    {
        $this->throwOnNull();

        fseek($this->resource, $offset, $whence);
    }

    public function rewind()
    {
        return $this->seek(0);
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function write($string)
    {
        throw new \RuntimeException();
    }

    /**
     * {@inheritdoc}
     */
    public function isReadable()
    {
        return $this->resource !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function read($length)
    {
        $this->throwOnNull();

        return fread($this->resource, $length);
    }

    /**
     * {@inheritdoc}
     */
    public function getContents()
    {
        $this->throwOnNull();

        return stream_get_contents($this->resource);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($key = null)
    {
        $this->throwOnNull();

        $meta = stream_get_meta_data($this->resource);

        return null === $key ? $meta : (isset($meta[$key]) ? $neta[$key] : null);
    }

    /**
     * throwOnNull
     *
     * @param string $message
     * @throws \RuntimeException if resource is null.
     *
     * @return void
     */
    private function throwOnNull($message = 'Stream error')
    {
        if (null === $this->resource) {
            throw new \RuntimeException($message);
        }
    }


    /**
     * setResource
     *
     * @param ImageResourceInterface $image
     *
     * @return resource
     */
    private function setResource(ImageResourceInterface $image)
    {
        $error = null;

        if ($image->isLocal()) {
            set_error_handler(function ($e) use (&$error) {
                $error = $e;
            }, E_WARNING);

            $resource = fopen($image->getPath(), 'r');
            restore_error_handler();
        } else {
            //$resource = fopen('php://memory', 'r+');
            $resource = fopen('php://temp', 'wb+');
            fwrite($resource, $image->getContents());
            rewind($resource);
        }

        if (null !== $error) {
            throw new \InvalidArgumentException($e);
        }

        return $this->resource = $resource;
    }
}
