<?php

/*
 * This File is part of the Thapp\Jmg\Tests\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Jmg\Tests\Http;

use Thapp\Jmg\Http\Psr7\UrlSigner;
use Thapp\Jmg\Parameters;
use Thapp\Jmg\FilterExpression;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Uri;
use Thapp\Jmg\Exception\InvalidSignatureException;

/**
 * @class UrlSignerTest
 *
 * @package Thapp\Jmg\Tests\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class UrlSignerTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('Thapp\Jmg\Http\Psr7\UrlSignerInterface', new UrlSigner('secret'));
    }

    /** @test */
    public function itShouldValidatePsrRequest()
    {
        $params = Parameters::fromString('2/100/100/5');
        $filters = new FilterExpression('circle;o=12;c=#f00');

        $signer = new UrlSigner('secretkey', 'token');
        $signed = $signer->sign($path = '/images/image.jpg', $params, $filters);

        $qst = parse_url($signed, PHP_URL_QUERY);
        parse_str($qst, $query);

        $request = $this->mockRequest();
        $request->method('getQueryParams')->willReturn(['token' => $query['token']]);
        $request->method('getUri')->willReturn($uri = $this->mockUri());
        $uri->method('getPath')->willReturn($path);

        try {
            $this->assertTrue($signer->validateRequest($request, $params, $filters));
        } catch (InvalidSignatureException $e) {
            $this->fail($e->getMessage());
        }
    }

    /** @test */
    public function itShouldThrowOnMissingToken()
    {
        $signer = new UrlSigner('secretkey', 'token');

        $request = $this->mockRequest();
        $request->method('getQueryParams')->willReturn([]);

        $params = Parameters::fromString('2/100/100/5');

        try {
            $signer->validateRequest($request, $params);
        } catch (InvalidSignatureException $e) {
            $this->assertSame('Signature is missing.', $e->getMessage());
        }
    }

    /** @test */
    public function itShouldThrowOnInvalidToken()
    {
        $signer = new UrlSigner('secretkey', 'token');

        $request = $this->mockRequest();
        $request->method('getQueryParams')->willReturn(['token' => 'invalid']);
        $request->method('getUri')->willReturn($uri = $this->mockUri());

        $path = '/images/image.jpg';

        $uri->method('getPath')->willreturn($path);

        $params = Parameters::fromString('2/100/100/5');

        try {
            $signer->validateRequest($request, $params);
        } catch (InvalidSignatureException $e) {
            $this->assertSame('Signature is invalid.', $e->getMessage());
        }
    }


    private function mockRequest()
    {
        return $this->getMockbuilder('Psr\Http\Message\ServerRequestInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function mockUri()
    {
        return $this->getMockbuilder('Psr\Http\Message\UriInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
