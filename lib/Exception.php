<?php
/*
   +----------------------------------------------------------------------+
   | LiteRT Annotation Library                                            |
   +----------------------------------------------------------------------+
   | Copyright (c) 2007-2017 Fenying Studio                               |
   +----------------------------------------------------------------------+
   | This source file is subject to version 2.0 of the Apache license,    |
   | that is bundled with this package in the file LICENSE, and is        |
   | available through the world-wide-web at the following url:           |
   | https://github.com/litert/annotation/blob/master/LICENSE             |
   +----------------------------------------------------------------------+
   | Authors: Angus Fenying <i.am.x.fenying@gmail.com>                    |
   +----------------------------------------------------------------------+
 */

declare (strict_types=1);

namespace L\Annotation;

class Exception extends \L\Core\Exception
{
    const METHOD_NOT_FOUND = 0x00000001;
    const CLASS_NOT_FOUND = 0x00000002;
    const PROPERTY_NOT_FOUND = 0x00000003;
    const FUNCTION_NOT_FOUND = 0x00000004;
}
