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

use Thapp\Jmg\ParamGroup;
use Thapp\Jmg\Exception\InvalidSignatureException;
use Thapp\Jmg\Http\UrlSigner as BaseSigner;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * @class UrlSigner
 *
 * @package Thapp\Jmg\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class UrlSigner extends BaseSigner implements UrlSignerInterface
{
    /**
     * {@inheritdoc}
     */
    public function validateRequest(Request $request, ParamGroup $params)
    {
        $key   = $this->getQParamKey();
        $query = $request->getQueryParams();

        if (!isset($query[$key])) {
            throw InvalidSignatureException::missingSignature();
        }

        if (0 !== strcmp($query[$key], $this->createSignature($request->getUri()->getPath(), $params))) {
            throw InvalidSignatureException::invalidSignature();
        }

        return true;
    }
}
