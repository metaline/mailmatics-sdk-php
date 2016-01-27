<?php

/*
 * This file is part of the Mailmatics SDK package.
 *
 * (c) Web Agency Meta Line S.r.l.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mailmatics\Exception;

/**
 * ResourceNotFoundException.
 *
 * Exception when a resource does not exist (404 code).
 */
class ResourceNotFoundException extends \InvalidArgumentException implements ExceptionInterface
{
}
