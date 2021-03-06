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

use DateTime;
use Psr\Http\Message\MessageInterface;
use Thapp\Jmg\Resource\CachedResourceInterface;
use Thapp\Jmg\Resource\ImageResourceInterface as ImageResource;

/**
 * @class ResponseFactory
 *
 * @package Thapp\Jmg\Http\Psr7
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ResponseFactory implements ImageResponseFactoryInterface
{
    /** @var bool */
    private $useXsend;

    /**
     * Constructor.
     *
     * @param bool $useXsend
     */
    public function __construct($useXsend = false)
    {
        $this->useXsend = (bool)$useXsend;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse(MessageInterface $request, ImageResource $resource)
    {
        $version = $request->getProtocolVersion();
        $etag    = $request->getHeaderLine('if-none-match');

        list($time, $lastMod, $mod, $modDate) = $this->getModTimes($request, $resource);
        $headers = $this->getDefaultHeaders($lastMod, $resource->getHash());

        if (0 === strcmp($etag, $headers['etag']) ||
            // not modified response
            (($resource instanceof CachedResourceInterface &&
            $resource->isFresh($time)) && $mod === $modDate)) {
            $response = (new NotModifiedImageResonse($resource, [], $version))
                ->withHeader('content-type', $resource->getMimeType())
                ->withHeader('cache-control', 'public');
        } else {
            // normal response
            if ($this->useXsend && !empty($request->getHeaderLine('x-sendfile-type'))) {
                $headers = $this->getXsendfileHeaders($resource, $headers);
            }

            $response = new ImageResponse($resource, $headers, $version);
        }

        return $response;
    }

    /**
     * getModTimes
     *
     * @param MessageInterface $request
     * @param ImageResource $resource
     *
     * @return array
     */
    private function getModTimes(MessageInterface $request, ImageResource $resource)
    {
        $time = time();
        return [
            $time,
            (new DateTime)->setTimestamp($modDate = $resource->getLastModified()),
            strtotime($request->getHeaderLine('if-modified-since')) ?: $time,
            $modDate
        ];
    }

    /**
     * getDefaultHeaders
     *
     * @param DateTime $lastMod
     * @param srting $resourceEtag
     *
     * @return array
     */
    private function getDefaultHeaders(DateTime $lastMod, $resourceEtag)
    {
        return [
            'last-modified' => $lastMod->format('D, d M Y H:i:s').' GMT',
            'etag' => $resourceEtag
        ];
    }

    /**
     * getXsendfileHeaders
     *
     * @param ImageResource $resource
     * @param array $originalHeaders
     *
     * @return array
     */
    private function getXsendfileHeaders(ImageResource $resource, array $originalHeaders = [])
    {
        return array_merge($originalHeaders, [
            'content-disposition' => sprintf('inline; filename="%s"', basename($file = $resource->getPath())),
            'content-lenght' => filesize($file),
            'x-sendfile' => $file
        ]);
    }
}
