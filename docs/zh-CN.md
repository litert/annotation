# 注解提取器

LiteRT/Annotation 提供了 4 个工具方法，可以提取 PHP 类、（类）方法、（类）属性、函数
的注解信息。（从文档注释中）

- `L\Annotation\Extractor::fromClass`
- `L\Annotation\Extractor::fromMethod`
- `L\Annotation\Extractor::fromProperty`
- `L\Annotation\Extractor::fromFunction`

## 1. 支持的注解形式

> 同名的注解可以重复使用，因此解析结果中，每种注解都是一个数组。

### 1.1. 标记式 `@test` 或 `@test()`

这种标记式的注解将被解析为 `['test' => [true]]`。

### 1.2. 参数式

参数式可以识别括号内的参数：

```php
/**
 * @auth(type=login)
 * @route(method=GET,uri="/")
 */
```

将被解析为：

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

> 提示：使用参数式注解时，注解名称和括号之间不能有空白。

### 1.3. 全参数式

全参数式是指在注解行内，注解名后面（第一段空白之后）所有内容作为一个参数，比如：

```php
/**
 * @hello     world
 * @author    Angus
 */
```

将被解析为：

```php
[
    'hello' => ['world'],
    'author' => ['Angus']
]
```

## 2. 接口说明

### 2.1. fromClass

提取一个类的注解信息。

-   定义

    ```php
    public static function fromClass(
        string $class
    ): array;
    ```

-   参数说明

    - `$class` 必须是类的完整名称（即包括命名空间，如果有的话）。

-   返回值

    返回值是一个 PHP 关联数组，数组 key 为注解名称，数组的 value 为注解的参数。
    如果没有注解则返回空数组。

    > **如果指定的类不存在，则抛出一个 `\L\Core\Exception` 类型的异常。**
    > 错误码为 `\L\Annotation\Errors\CLASS_NOT_FOUND`。

-   使用示例

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

提取一个（类）方法的注解信息。

-   定义

    ```php
    public static function fromMethod(
        string $method,
        string $class = null
    ): array;
    ```

-   参数说明

    - `$method` 方法的完整名称
    - `$class` 类的完整名称（即包括命名空间，如果有的话）。

    > 当 `$method` 写成 `ClassName::methodName` 形式的时候，`$class` 参数可省略。

-   返回值

    返回值是一个 PHP 关联数组，数组 key 为注解名称，数组的 value 为注解的参数。
    如果没有注解则返回空数组。

    > **如果指定的方法不存在，则抛出一个 `\L\Core\Exception` 类型的异常。**
    > 错误码为 `\L\Annotation\Errors\METHOD_NOT_FOUND`。

-   使用示例

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

提取一个（类）属性的注解信息。

-   定义

    ```php
    public static function fromProperty(
        string $class,
        string $property
    ): array;
    ```

-   参数说明

    -   `$property` 属性的完整名称
    -   `$class` 类的完整名称（即包括命名空间，如果有的话）。

-   返回值

    返回值是一个 PHP 关联数组，数组 key 为注解名称，数组的 value 为注解的参数。
    如果没有注解则返回空数组。

    >   **如果指定的属性不存在，则抛出一个 `\L\Core\Exception` 类型的异常。**
    >   错误码为 `\L\Annotation\Errors\PROPERTY_NOT_FOUND`。

-   使用示例

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

提取一个函数的注解信息。

-   定义

    ```php
    public static function fromFunction(
        string $fn
    ): array;
    ```

-   参数说明

    -   `$fn` 函数的完整名称（即包括命名空间，如果有的话）

-   返回值

    返回值是一个 PHP 关联数组，数组 key 为注解名称，数组的 value 为注解的参数。
    如果没有注解则返回空数组。

    >   **如果指定的函数不存在，则抛出一个 `\L\Core\Exception` 类型的异常。**
    >   错误码为 `\L\Annotation\Errors\FUNCTION_NOT_FOUND`。

-   使用示例

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
