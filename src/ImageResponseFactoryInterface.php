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

use Psr\Http\Message\MessageInterface;
use Thapp\Jmg\Resource\ResourceInterface;

/**
 * @interface ImageResponseFactoryInterface
 *
 * @package Thapp\Jmg\Http\Psr7
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ImageResponseFactoryInterface
{
    /**
     * Creates an appropriate response for a given image resource
     *
     * @param MessageInterface $request
     * @param ResourceInterface $resource
     *
     * @return Psr\Http\Message\ResponseInterface
     */
    public function getResponse(MessageInterface $request, ResourceInterface $resource);
}
