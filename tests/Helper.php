<?php

/*
 * This File is part of the Thapp\Jmg\Tests\Http\Psr7 package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Jmg\Tests\Http\Psr7;

/**
 * @trait Helper
 *
 * @package Thapp\Jmg\Tests\Http\Psr7
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
trait Helper
{
    private function mockResource()
    {
        return $this->getMockbuilder('Thapp\Jmg\Resource\ImageResourceInterface')
            ->disableOriginalConstructor()->getMock();
    }
}
