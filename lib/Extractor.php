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

/**
 * This class provides the methods to extract annotations from the document
 * comments of functions, classes, and methods.
 *
 * @package litert/annotation
 */
class Extractor
{
    /**
     * Extract the annotations from a method.
     *
     * @param string $method
     * @param string|null $class
     * @param bool $withParents
     *
     * @throws \L\Annotation\Exception
     *
     * @return array
     */
    public static function fromMethod(
        string $method,
        string $class = null,
        bool $withParents = false
    ): array
    {
        if ($class) {

            try {

                $refMethod = new \ReflectionMethod($class, $method);
            }
            catch (\ReflectionException $e) {

                throw new Exception(
                    "Method '{$class}::{$method}' not found.",
                    Exception::METHOD_NOT_FOUND
                );
            }
        }
        else {

            try {

                $refMethod = new \ReflectionMethod($method);
            }
            catch (\ReflectionException $e) {

                throw new Exception(
                    "Method '{$method}' not found.",
                    Exception::METHOD_NOT_FOUND
                );
            }
        }

        $docComment = $refMethod->getDocComment();

        if ($withParents) {

            $refClass = $refMethod->getDeclaringClass();

            while (1) {

                if ($refClass = $refClass->getParentClass()) {

                    try {

                        $refMethod = $refClass->getMethod($method);
                    }
                    catch (\ReflectionException $e) {

                        break;
                    }

                    if (!$refMethod) {

                        continue;
                    }

                    if ($parentDoc = $refMethod->getDocComment()) {

                        $docComment = $docComment === false ?
                            $parentDoc :
                            "{$parentDoc}{$docComment}";
                    }
                }
                else {

                    break;
                }
            }
        }

        unset($refMethod, $refClass);

        if ($docComment === false) {

            return [];
        }

        return Parser::parseDocComment($docComment);
    }

    /**
     * Extract the annotations from a property.
     *
     * @param string $class
     * @param string $property
     *
     * @throws \L\Annotation\Exception
     *
     * @return array
     */
    public static function fromProperty(
        string $class,
        string $property
    ): array
    {
        try {

            $property = new \ReflectionProperty($class, $property);
        }
        catch (\ReflectionException $e) {

            throw new Exception(
                "Property '{$class}::{$property}' not found.",
                Exception::PROPERTY_NOT_FOUND
            );
        }

        $docComment = $property->getDocComment();

        unset($property);

        if ($docComment === false) {

            return [];
        }

        return Parser::parseDocComment($docComment);
    }

    /**
     * Extract the annotations from a class.
     *
     * @param string $class
     * @param bool $withParents
     *
     * @throws \L\Annotation\Exception
     *
     * @return array
     */
    public static function fromClass(
        string $class,
        bool $withParents = false
    ): array
    {
        try {

            $class = new \ReflectionClass($class);
        }
        catch (\ReflectionException $e) {

            throw new Exception(
                "Class '{$class}' not found.",
                Exception::CLASS_NOT_FOUND
            );
        }

        $docComment = $class->getDocComment();

        if ($withParents) {

            while (1) {

                if ($class = $class->getParentClass()) {

                    if ($parentDoc = $class->getDocComment()) {

                        $docComment = $docComment === false ?
                            $parentDoc :
                            "{$parentDoc}{$docComment}";
                    }
                }
                else {

                    break;
                }
            }
        }

        unset($class);

        if ($docComment === false) {

            return [];
        }

        return Parser::parseDocComment($docComment);
    }

    /**
     * Extract the annotations from a function.
     *
     * @param string $fn
     *
     * @throws \L\Annotation\Exception
     *
     * @return array
     */
    public static function fromFunction(
        string $fn
    ): array
    {
        try {

            $fn = new \ReflectionFunction($fn);
        }
        catch (\ReflectionException $e) {

            throw new Exception(
                "Function {$fn} not found.",
                Exception::FUNCTION_NOT_FOUND
            );
        }

        $docComment = $fn->getDocComment();

        unset($fn);

        if ($docComment === false) {

            return [];
        }

        return Parser::parseDocComment($docComment);
    }
}
