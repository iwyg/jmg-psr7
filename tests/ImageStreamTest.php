<?php

/*
 * This File is part of the Thapp\Jmg\Http\Psr7 package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */


namespace Thapp\Jmg\Tests\Http\Psr7;

use Thapp\Jmg\Http\Psr7\ImageStream;

class ImageStreamTest extends \PHPUnit_Framework_TestCase
{
    use Helper;

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('Psr\Http\Message\StreamInterface', new ImageStream($this->mockResource()));
    }

    /** @test */
    public function shouldCreateStreamFromContent()
    {
        $res = $this->mockResource();
        $res->method('isLocal')->willReturn(false);
        $res->method('getContents')->willReturn('a00000');

        $stream = new ImageStream($res);
        $this->assertEquals('a', $stream->read(1));
        $this->assertEquals('00000', $stream->getContents());
        $this->assertTrue(is_resource($stream->detach()));

        $stream->close();
    }

    /** @test */
    public function shouldCreateStreamFromLocalPath()
    {
        $res = $this->mockResource();
        $res->method('isLocal')->willReturn(true);
        try {
            $stream = new ImageStream($res);
            $stream->close();
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(true);
        }

    }

    /** @test */
    public function itShouldThrowIfResourceIsNull()
    {
        $res = $this->mockResource();
        $res->method('isLocal')->willReturn(false);
        $res->method('getContents')->willReturn('');

        $stream = new ImageStream($res);
        $stream->detach();

        try {
            $stream->seek(0);
        } catch (\RuntimeException $e) {
            $this->assertSame('Stream error', $e->getMessage());
        }

        try {
            $stream->getMetadata();
        } catch (\RuntimeException $e) {
            $this->assertSame('Stream error', $e->getMessage());
        }

        try {
            $stream->getContents();
        } catch (\RuntimeException $e) {
            $this->assertSame('Stream error', $e->getMessage());
        }

        try {
            $stream->read(1);
        } catch (\RuntimeException $e) {
            $this->assertSame('Stream error', $e->getMessage());
        }

        try {
            $stream->tell();
        } catch (\RuntimeException $e) {
            $this->assertSame('Resource is null.', $e->getMessage());
        }
    }
}
