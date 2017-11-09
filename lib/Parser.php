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

class Parser
{
    const STATUS_READY = 0;
    const STATUS_READING_KEY = 1;
    const STATUS_AFTER_KEY = 2;
    const STATUS_SEEKING_NEW_LINE = 3;
    const STATUS_READING_MULTI_ARGS = 4;
    const STATUS_READING_ONE_LINE_ARGS = 5;
    const STATUS_READING_ITEM = 6;
    const STATUS_READING_ITEM_STRING = 7;
    const STATUS_READING_ITEM_STR_ESCAPING = 8;

    const BLANK_CHARS = [' ', "\t"];
    const WHITE_CHARS = [' ', "\t", "\n"];
    const NEW_LINE = "\n";
    public static function parseDocComment(string $docComment): array
    {
        $ret = [];

        $docComment = str_replace('/**', '', $docComment);
        $docComment = str_replace('*/', '', $docComment);
        $docComment = preg_replace('/\r|\n/', self::NEW_LINE, $docComment);
        $docComment = preg_replace('/\n\s+\* ?/', self::NEW_LINE, $docComment);

        $len = strlen($docComment);
        $status = self::STATUS_READY;

        $data = [];

        for ($p = 0; $p < $len; ++$p) {

            $char = $docComment[$p];

            switch ($status) {
            case self::STATUS_READY:

                if (in_array($char, self::WHITE_CHARS)) {
                    continue;
                }

                if ($char === '@') {

                    $status = self::STATUS_READING_KEY;
                    $data = [
                        'start' => $p + 1
                    ];
                    break;
                }

                $status = self::STATUS_SEEKING_NEW_LINE;

                break;

            case self::STATUS_SEEKING_NEW_LINE:

                if ($char === self::NEW_LINE) {

                    $status = self::STATUS_READY;
                }

                break;

            case self::STATUS_READING_KEY:

                if (!preg_match('/[-\w\.]/', $char)) {

                    $data['key'] = substr($docComment, $data['start'], $p - $data['start']);

                    $status = self::STATUS_AFTER_KEY;

                    $p--;
                }

                break;

            case self::STATUS_AFTER_KEY:

                if (in_array($char, self::BLANK_CHARS)) {

                    continue;
                }

                if ($char === self::NEW_LINE) {

                    self::__pushValue($ret, $data['key'], true);
                    $status = self::STATUS_READY;
                    $data = [];
                }
                elseif ($char === '(') {

                    $status = self::STATUS_READING_MULTI_ARGS;
                    $data['items'] = [];
                }
                else {

                    $status = self::STATUS_READING_ONE_LINE_ARGS;
                    $data['start'] = $p;
                }

                break;

            case self::STATUS_READING_ONE_LINE_ARGS:

                if ($char === self::NEW_LINE) {

                    self::__pushValue(
                        $ret,
                        $data['key'],
                        trim(substr(
                            $docComment,
                            $data['start'],
                            $p - $data['start']
                        ))
                    );
                    $data = [];
                    $status = self::STATUS_READY;
                }

                break;

            case self::STATUS_READING_MULTI_ARGS:

                if (in_array($char, self::WHITE_CHARS)) {

                    continue;
                }

                switch ($char) {
                case ',':

                    continue;

                case '"':

                    if (empty($data['itemKey'])) {

                        $data['itemKey'] = 0;
                    }

                    $data['start'] = $p + 1;

                    $status = self::STATUS_READING_ITEM_STRING;

                    break;

                case ')':

                    if ($data['items']) {

                        self::__pushValue($ret, $data['key'], $data['items']);
                    }
                    else {

                        self::__pushValue($ret, $data['key'], true);
                    }

                    $status = self::STATUS_SEEKING_NEW_LINE;
                    $data = [];

                    break;

                default:

                    $status = self::STATUS_READING_ITEM;
                    $data['start'] = $p;
                }

                break;

            case self::STATUS_READING_ITEM:

                switch ($char) {

                case ')': # end up items
                case ',': # end up an item

                    if (isset($data['itemKey']) && $data['itemKey'] !== 0) {

                        $data['items'][$data['itemKey']] = trim(substr(
                            $docComment,
                            $data['start'],
                            $p - $data['start']
                        ));
                    }
                    else {

                        $data['items'][] = trim(substr(
                            $docComment,
                            $data['start'],
                            $p - $data['start']
                        ));
                    }

                    unset($data['itemKey']);

                    $status = self::STATUS_READING_MULTI_ARGS;

                    if ($char === ')') {

                        $p--;
                    }

                    break;

                case '=':

                    if (!isset($data['itemKey'])) {

                        $data['itemKey'] = trim(substr(
                            $docComment,
                            $data['start'],
                            $p - $data['start']
                        ));

                        $data['start'] = $p;

                        $status = self::STATUS_READING_MULTI_ARGS;
                    }

                    break;
                }

                break;

            case self::STATUS_READING_ITEM_STRING:

                switch ($char) {

                case '"': # end up string

                    if (isset($data['itemKey']) && $data['itemKey'] !== 0) {

                        $data['items'][$data['itemKey']] = stripslashes(substr(
                            $docComment,
                            $data['start'],
                            $p - $data['start']
                        ));
                    }
                    else {

                        $data['items'][] = stripslashes(substr(
                            $docComment,
                            $data['start'],
                            $p - $data['start']
                        ));
                    }

                    $status = self::STATUS_READING_MULTI_ARGS;

                    unset($data['itemKey']);

                    break;
                case '\\':
                    $status = self::STATUS_READING_ITEM_STR_ESCAPING;
                    break;
                }

                break;

            case self::STATUS_READING_ITEM_STR_ESCAPING:

                $status = self::STATUS_READING_ITEM_STRING;
                break;
            }
        }

        return $ret;
    }

    protected static function __pushValue(&$stack, $key, $value)
    {
        if (isset($stack[$key])) {

            $stack[$key][] = $value;
        }
        else {

            $stack[$key] = [$value];
        }
    }
}
