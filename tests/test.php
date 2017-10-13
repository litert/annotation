#!env php
<?php
declare (strict_types = 1);

require __DIR__ . '/../vendor/autoload.php';

use \L\Annotation\Extractor;

/**
 * @package litert/annotation
 *
 * @author Angus.Fenying
 */
class ABC
{
    /**
     * @hello
     * @world
     */
    public $aa;

    public $ggg;

    /**
     * @route(method = GET, path = "/")
     */
    public $uri;

    /**
     * @hello
     * @test fff
     */
    public function test()
    {
        echo __METHOD__;
    }
}

/**
 * @author angus
 *
 * @test ( comment=1 )
 * @hello( speak = yes , to=   "world ")
 * @go()
 */
function test()
{

}

var_dump(Extractor::fromMethod(
    'test',
    ABC::class
));

var_dump(Extractor::fromMethod(
    'ABC::test'
));

var_dump(Extractor::fromClass(
    ABC::class
));

var_dump(Extractor::fromFunction(
    'test'
));

var_dump(Extractor::fromProperty(
    'ABC',
    'aa'
));

var_dump(Extractor::fromProperty(
    'ABC',
    'ggg'
));

var_dump(Extractor::fromProperty(
    'ABC',
    'uri'
));

try {

    var_dump(Extractor::fromProperty(
        'ABC',
        'gggx'
    ));
}
catch (\L\Core\Exception $e) {

    echo <<<ERROR
Error({$e->getCode()}): {$e->getMessage()}

ERROR;
}
