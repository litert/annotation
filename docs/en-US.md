# Annotation Extractor

LiteRT/Annotation provides 4 methods to extract annotations from document
comments of PHP classes, functions, methods, properties.

- `L\Annotation\Extractor::fromClass`
- `L\Annotation\Extractor::fromMethod`
- `L\Annotation\Extractor::fromProperty`
- `L\Annotation\Extractor::fromFunction`

## 1. The style of annotations

> A kind of annotation can be use more than once, thus every kind of
> annotation should be an array in the parsed result.

### 1.1. Flag-like

> e.g. `@test` or `@test()`

Annotation of this style will be parsed into `['test' => [true]]`.

### 1.2. Argument-style

The annotation with arguments, such as

```php
/**
 * @auth ( type = login )
 * @route( method = GET, uri="/" )
 */
```

will be parsed into

```php
[
    'auth' => [
        ['type' => 'login']
    ],
    'route' => [
        [
            'method' => 'GET',
            'uri' => '/'
        ]
    ]
]
```

> Tips: no blank between the left-bracket and name of annotation.

### 1.3. Line-argument-style

With this style, the only argument is the rest of line, left to the first
blank-block after the name of annotation.

```php
/**
 * @hello     world
 * @author    Angus
 */
```

will be parsed into

```php
[
    'hello' => ['world'],
    'author' => ['Angus']
]
```

## 2. Interfaces documents

### 2.1. fromClass

This method helps extract the annotations from a class.

-   Definition

    ```php
    public static function fromClass(
        string $class
    ): array;
    ```

-   Arguments

    - `$class` must be the full name of class, with namespace.

-   Return Value

    Returns an associated array, with the annonation names as keys.
    An empty array willy will be returned if no annoations were found.

    > **If the target doesn't exist, a exception of type`\L\Core\Exception`
    > will be thrown.**
    >
    > Error Code: `\L\Annotation\Errors\CLASS_NOT_FOUND`。

-   Sample

    ```php
    use \L\Annotation\Extractor;

    /**
     * @package litert/annotation
     *
     * @author Angus.Fenying
     */
    class ABC
    {
    }

    var_dump(Extractor::fromClass(
        ABC::class
    ));
    ```

### 2.2. fromMethod

This method helps extract the annotations from a method.

-   Definition

    ```php
    public static function fromMethod(
        string $method,
        string $class = null
    ): array;
    ```

-   Arguments

    - `$method` The name of method.
    - `$class` The full name of class, with namespace.

    > Use `ClassName::methodName` as method's name, so that `$class` can be
    > optional.

-   Return Value

    Returns an associated array, with the annonation names as keys.
    An empty array willy will be returned if no annoations were found.

    > **If the target doesn't exist, a exception of type`\L\Core\Exception`
    > will be thrown.**
    >
    > Error Code: `\L\Annotation\Errors\METHOD_NOT_FOUND`。

-   Sample

    ```php
    use \L\Annotation\Extractor;

    class ABC
    {
        /**
         * @hello
         * @test fff
         */
        public function test()
        {
            echo __METHOD__;
        }
    }

    var_dump(Extractor::fromMethod(
        'test',
        ABC::class
    ));

    var_dump(Extractor::fromMethod(
        'ABC::test'
    ));
    ```

### 2.3. fromProperty

This method helps extract the annotations from a property.

-   Definition

    ```php
    public static function fromProperty(
        string $class,
        string $property
    ): array;
    ```

-   Arguments

    -   `$property` The name of property.
    -   `$class` The full name of class, with namespace.

-   Return Value

    Returns an associated array, with the annonation names as keys.
    An empty array willy will be returned if no annoations were found.

    > **If the target doesn't exist, a exception of type`\L\Core\Exception`
    > will be thrown.**
    >
    > Error Code: `\L\Annotation\Errors\PROPERTY_NOT_FOUND`。

-   Sample

    ```php
    use \L\Annotation\Extractor;

    class ABC
    {
        /**
         * @delay-initialize
         * @test(default=123)
         */
        public $ggg;
    }

    var_dump(Extractor::fromProperty(
        'ABC',
        'ggg'
    ));
    ```

### 2.4. fromFunction

This method helps extract the annotations from a function.

-   Definition

    ```php
    public static function fromFunction(
        string $fn
    ): array;
    ```

-   Arguments

    -   `$fn` The full name of function, with namespace.

-   Return Value

    Returns an associated array, with the annonation names as keys.
    An empty array willy will be returned if no annoations were found.

    > **If the target doesn't exist, a exception of type`\L\Core\Exception`
    > will be thrown.**
    >
    > Error Code: `\L\Annotation\Errors\FUNCTION_NOT_FOUND`。

-   Sample

    ```php
    use \L\Annotation\Extractor;

    /**
     * @author angus
     *
     * @test(comment=1)
     * @hello(speak=yes,to="world")
     * @go()
     */
    function test()
    {
    
    }

    var_dump(Extractor::fromFunction(
        'test'
    ));
    ```
