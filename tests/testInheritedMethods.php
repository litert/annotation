#!env php
<?php
declare (strict_types = 1);

require __DIR__ . '/../vendor/autoload.php';

use \L\Annotation\Extractor;

class A
{
    /**
     * @test2(val = 5)
     */
    public function method()
    {

    }
}

class B extends A
{
    /**
     * @test(val = 2)
     */
    public function method()
    {

    }
}

class C extends B
{
}

var_dump(Extractor::fromMethod(
    'method',
    C::class,
    true
));
