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

use Thapp\Jmg\Parameters;
use Thapp\Jmg\FilterExpression;
use Thapp\Jmg\Http\HttpSignerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * @interface HttpSignerInterface
 *
 * @package Thapp\Jmg\Http\Psr7
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface UrlSignerInterface extends HttpSignerInterface
{
    /**
     * validate
     *
     * @param Request $request
     * @param Parameters $params
     * @param FilterExpression $filters
     *
     * @return boolean
     */
    public function validateRequest(Request $request, Parameters $params, FilterExpression $filters = null);
}
