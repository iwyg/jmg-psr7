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

use Thapp\Jmg\Http\Psr7\ImageResponse;

class ImageResponseTest extends \PHPUnit_Framework_TestCase
{
    use Helper;

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', new ImageResponse($this->mockResource()));
    }

    /** @test */
    public function itShouldGetAllCodes()
    {
        $response = new ImageResponse($this->mockResource(), [], '1.0');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertEquals('1.0', $response->getProtocolVersion());
        $this->assertEquals('Ok', $response->getReasonPhrase());

        $response = $response->withStatus(404, 'Not Found.');

        $this->assertSame(404, $response->getStatusCode());
        $this->assertEquals('Not Found.', $response->getReasonPhrase());
    }

    /** @test */
    public function itShouldGetHeaderLines()
    {
        $response = new ImageResponse($this->mockResource());

        $this->assertEquals('binary', $response->getHeaderLine('content-transfer-encoding'));
    }

    /** @test */
    public function itShouldRemoveHeaders()
    {
        $response = new ImageResponse($this->mockResource(), ['content-length' => 200]);
        $this->assertTrue([200] === $response->getHeader('content-length'));

        $response = $response->withoutHeader('content-length');
        $this->assertTrue([] === $response->getHeader('content-length'));
    }

    /** @test */
    public function itShouldAddHeader()
    {
        $res = $this->mockResource();
        $res->method('getMimeType')->willReturn('image/jpeg');
        $response = new ImageResponse($res);
        $response = $response->withAddedHeader('content-type', 'utf-8');

        $this->assertSame(['image/jpeg', 'utf-8'], $response->getHeader('content-type'));
        $this->assertSame('image/jpeg,utf-8', $response->getHeaderLine('content-type'));
    }

    /** @test */
    public function itShouldFilterNoneSameHeaders()
    {
        $response = new ImageResponse($this->mockResource(), ['foo' => 'bar', 'content-length' => 200]);
        $headers  = $response->getHeaders();
        $this->assertFalse(isset($headers['foo']));

        $this->assertArrayHasKey('content-transfer-encoding', $headers);
        $this->assertArrayHasKey('content-type', $headers);
        $this->assertArrayHasKey('accept-ranges', $headers);
        $this->assertArrayHasKey('keep-alive', $headers);
        $this->assertArrayHasKey('connection', $headers);
        $this->assertArrayHasKey('content-length', $headers);
    }
}
