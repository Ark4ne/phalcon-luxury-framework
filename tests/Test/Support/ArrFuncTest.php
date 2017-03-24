<?php
namespace Test\Support;

use ArrayObject;

/**
 * Class ArrTest
 *
 * @package Support
 */
class ArrFuncTest extends \PHPUnit_Framework_TestCase
{
    public function testAccessible()
    {
        $this->assertTrue(arr_accessible([]));
        $this->assertTrue(arr_accessible([1, 2]));
        $this->assertTrue(arr_accessible(['a' => 1, 'b' => 2]));
        //$this->assertTrue(arr_accessible(new Collection));
        $this->assertFalse(arr_accessible(null));
        $this->assertFalse(arr_accessible('abc'));
        $this->assertFalse(arr_accessible(new \stdClass));
        $this->assertFalse(arr_accessible((object)['a' => 1, 'b' => 2]));
    }

    public function testAdd()
    {
        $array = arr_add(['name' => 'Desk'], 'price', 100);
        $this->assertEquals(['name' => 'Desk', 'price' => 100], $array);
    }

    public function testCollapse()
    {
        $data = [['foo', 'bar'], ['baz'], 'boo'];
        $this->assertEquals(['foo', 'bar', 'baz'], arr_collapse($data));
    }

    public function testDivide()
    {
        list($keys, $values) = arr_divide(['name' => 'Desk']);
        $this->assertEquals(['name'], $keys);
        $this->assertEquals(['Desk'], $values);
    }

    public function testDot()
    {
        $array = arr_dot(['foo' => ['bar' => 'baz']]);
        $this->assertEquals(['foo.bar' => 'baz'], $array);
        $array = arr_dot([]);
        $this->assertEquals([], $array);
        $array = arr_dot(['foo' => []]);
        $this->assertEquals(['foo' => []], $array);
        $array = arr_dot(['foo' => ['bar' => []]]);
        $this->assertEquals(['foo.bar' => []], $array);
    }

    public function testExcept()
    {
        $array = ['name' => 'Desk', 'price' => 100];
        $array = arr_except($array, ['price']);
        $this->assertEquals(['name' => 'Desk'], $array);
    }

    public function testExists()
    {
        $this->assertTrue(arr_exists([1], 0));
        $this->assertTrue(arr_exists([null], 0));
        $this->assertTrue(arr_exists(['a' => 1], 'a'));
        $this->assertTrue(arr_exists(['a' => null], 'a'));
        //$this->assertTrue(arr_exists(new Collection(['a' => null]), 'a'));
        $this->assertFalse(arr_exists([1], 1));
        $this->assertFalse(arr_exists([null], 1));
        $this->assertFalse(arr_exists(['a' => 1], 0));
        //$this->assertFalse(arr_exists(new Collection(['a' => null]), 'b'));
    }

    public function testFirst()
    {
        $array = [100, 200, 300];
        $value = arr_first($array, function ($key, $value) {
            return $value >= 150;
        });
        $this->assertEquals(200, $value);
        $this->assertEquals(100, arr_first($array));
        $this->assertEquals(123, arr_first([], function (){}, 123));
    }

    public function testLast()
    {
        $array = [100, 200, 300];
        $last  = arr_last($array, function () {
            return true;
        });
        $this->assertEquals(300, $last);
        $this->assertEquals(300, arr_last($array));
    }

    public function testFlatten()
    {
        // Flat arrays are unaffected
        $array = ['#foo', '#bar', '#baz'];
        $this->assertEquals(['#foo', '#bar', '#baz'], arr_flatten(['#foo', '#bar', '#baz']));
        // Nested arrays are flattened with existing flat items
        $array = [['#foo', '#bar'], '#baz'];
        $this->assertEquals(['#foo', '#bar', '#baz'], arr_flatten($array));
        // Sets of nested arrays are flattened
        $array = [['#foo', '#bar'], ['#baz']];
        $this->assertEquals(['#foo', '#bar', '#baz'], arr_flatten($array));
        // Deeply nested arrays are flattened
        $array = [['#foo', ['#bar']], ['#baz']];
        $this->assertEquals(['#foo', '#bar', '#baz'], arr_flatten($array));
        // Nested collections are flattened alongside arrays
        //$array = [new Collection(['#foo', '#bar']), ['#baz']];
        //$this->assertEquals(['#foo', '#bar', '#baz'], arr_flatten($array));
        // Nested collections containing plain arrays are flattened
        //$array = [new Collection(['#foo', ['#bar']]), ['#baz']];
        //$this->assertEquals(['#foo', '#bar', '#baz'], arr_flatten($array));
        // Nested arrays containing collections are flattened
        //$array = [['#foo', new Collection(['#bar'])], ['#baz']];
        //$this->assertEquals(['#foo', '#bar', '#baz'], arr_flatten($array));
        // Nested arrays containing collections containing arrays are flattened
        //$array = [['#foo', new Collection(['#bar', ['#zap']])], ['#baz']];
        //$this->assertEquals(['#foo', '#bar', '#zap', '#baz'], arr_flatten($array));
    }

    public function testFlattenWithDepth()
    {
        // No depth flattens recursively
        $array = [['#foo', ['#bar', ['#baz']]], '#zap'];
        $this->assertEquals(['#foo', '#bar', '#baz', '#zap'], arr_flatten($array));
        // Specifying a depth only flattens to that depth
        $array = [['#foo', ['#bar', ['#baz']]], '#zap'];
        $this->assertEquals(['#foo', ['#bar', ['#baz']], '#zap'], arr_flatten($array, 1));
        $array = [['#foo', ['#bar', ['#baz']]], '#zap'];
        $this->assertEquals(['#foo', '#bar', ['#baz'], '#zap'], arr_flatten($array, 2));
    }

    public function testGet()
    {
        $array = ['products' => ['desk' => ['price' => 100]]];
        $value = arr_get($array, 'products.desk');
        $this->assertEquals(['price' => 100], $value);
        // Test null array values
        $array = ['foo' => null, 'bar' => ['baz' => null]];
        $this->assertNull(arr_get($array, 'foo', 'default'));
        $this->assertNull(arr_get($array, 'bar.baz', 'default'));
        // Test direct ArrayAccess object
        $array             = ['products' => ['desk' => ['price' => 100]]];
        $arrayAccessObject = new ArrayObject($array);
        $value             = arr_get($arrayAccessObject, 'products.desk');
        $this->assertEquals(['price' => 100], $value);
        // Test array containing ArrayAccess object
        $arrayAccessChild = new ArrayObject(['products' => ['desk' => ['price' => 100]]]);
        $array            = ['child' => $arrayAccessChild];
        $value            = arr_get($array, 'child.products.desk');
        $this->assertEquals(['price' => 100], $value);
        // Test array containing multiple nested ArrayAccess objects
        $arrayAccessChild  = new ArrayObject(['products' => ['desk' => ['price' => 100]]]);
        $arrayAccessParent = new ArrayObject(['child' => $arrayAccessChild]);
        $array             = ['parent' => $arrayAccessParent];
        $value             = arr_get($array, 'parent.child.products.desk');
        $this->assertEquals(['price' => 100], $value);
        // Test missing ArrayAccess object field
        $arrayAccessChild  = new ArrayObject(['products' => ['desk' => ['price' => 100]]]);
        $arrayAccessParent = new ArrayObject(['child' => $arrayAccessChild]);
        $array             = ['parent' => $arrayAccessParent];
        $value             = arr_get($array, 'parent.child.desk');
        $this->assertNull($value);
        // Test missing ArrayAccess object field
        $arrayAccessObject = new ArrayObject(['products' => ['desk' => null]]);
        $array             = ['parent' => $arrayAccessObject];
        $value             = arr_get($array, 'parent.products.desk.price');
        $this->assertNull($value);
        // Test null ArrayAccess object fields
        $array = new ArrayObject(['foo' => null, 'bar' => new ArrayObject(['baz' => null])]);
        $this->assertNull(arr_get($array, 'foo', 'default'));
        $this->assertNull(arr_get($array, 'bar.baz', 'default'));
        // Test null key returns the whole array
        $array = ['foo', 'bar'];
        $this->assertEquals($array, arr_get($array, null));
        // Test $array not an array
        $this->assertSame('default', arr_get(null, 'foo', 'default'));
        $this->assertSame('default', arr_get(false, 'foo', 'default'));
        // Test $array not an array and key is null
        $this->assertSame('default', arr_get(null, null, 'default'));
        // Test $array is empty and key is null
        $this->assertSame([], arr_get([], null));
        $this->assertSame([], arr_get([], null, 'default'));
    }

    public function testHas()
    {
        $array = ['products.desk' => ['price' => 100]];
        $this->assertTrue(arr_has($array, 'products.desk'));
        $array = ['products' => ['desk' => ['price' => 100]]];
        $this->assertTrue(arr_has($array, 'products.desk'));
        $this->assertTrue(arr_has($array, 'products.desk.price'));
        $this->assertFalse(arr_has($array, 'products.foo'));
        $this->assertFalse(arr_has($array, 'products.desk.foo'));
        $array = ['foo' => null, 'bar' => ['baz' => null]];
        $this->assertTrue(arr_has($array, 'foo'));
        $this->assertTrue(arr_has($array, 'bar.baz'));
        $array = new ArrayObject(['foo' => 10, 'bar' => new ArrayObject(['baz' => 10])]);
        $this->assertTrue(arr_has($array, 'foo'));
        $this->assertTrue(arr_has($array, 'bar'));
        $this->assertTrue(arr_has($array, 'bar.baz'));
        $this->assertFalse(arr_has($array, 'xxx'));
        $this->assertFalse(arr_has($array, 'xxx.yyy'));
        $this->assertFalse(arr_has($array, 'foo.xxx'));
        $this->assertFalse(arr_has($array, 'bar.xxx'));
        $array = new ArrayObject(['foo' => null, 'bar' => new ArrayObject(['baz' => null])]);
        $this->assertTrue(arr_has($array, 'foo'));
        $this->assertTrue(arr_has($array, 'bar.baz'));
        $array = ['foo', 'bar'];
        $this->assertFalse(arr_has($array, null));
        $this->assertFalse(arr_has(null, 'foo'));
        $this->assertFalse(arr_has(false, 'foo'));
        $this->assertFalse(arr_has(null, null));
        $this->assertFalse(arr_has([], null));
        $array = ['products' => ['desk' => ['price' => 100]]];
        $this->assertTrue(arr_has($array, ['products.desk']));
        $this->assertTrue(arr_has($array, ['products.desk', 'products.desk.price']));
        $this->assertTrue(arr_has($array, ['products', 'products']));
        $this->assertFalse(arr_has($array, ['foo']));
        $this->assertFalse(arr_has($array, []));
        $this->assertFalse(arr_has($array, ['products.desk', 'products.price']));
        $this->assertFalse(arr_has([], [null]));
        $this->assertFalse(arr_has(null, [null]));
    }

    public function testFetch()
    {
        $array = ['foo', 'bar', 'baz' => 'boo', 'foo.bar' => 123, 'null' => null];
        $this->assertEquals(null, arr_fetch($array, null));
        $this->assertEquals('baz', arr_fetch($array, null, 'baz'));
        $this->assertEquals('foo', arr_fetch($array, 0, 'baz'));
        $this->assertEquals('baz', arr_fetch($array, 3, 'baz'));
        $this->assertEquals('boo', arr_fetch($array, 'baz'));
        $this->assertEquals(123, arr_fetch($array, 'foo.bar'));
        $this->assertEquals(null, arr_fetch($array, 'null'));
        $this->assertEquals('test', arr_fetch($array, 'null', 'test'));
    }

    public function testRead()
    {
        $array = ['foo', 'bar', 'baz' => 'boo', 'foo.bar' => 123, 'null' => null];
        $this->assertEquals(null, arr_read($array, null));
        $this->assertEquals('baz', arr_read($array, null, 'baz'));
        $this->assertEquals('foo', arr_read($array, 0, 'baz'));
        $this->assertEquals('baz', arr_read($array, 3, 'baz'));
        $this->assertEquals('boo', arr_read($array, 'baz'));
        $this->assertEquals(123, arr_read($array, 'foo.bar'));
        $this->assertEquals(null, arr_read($array, 'null'));
        $this->assertEquals(null, arr_read($array, 'null', 'test'));
    }

    public function testIsAssoc()
    {
        $this->assertTrue(arr_isAssoc(['a' => 'a', 0 => 'b']));
        $this->assertTrue(arr_isAssoc([1 => 'a', 0 => 'b']));
        $this->assertTrue(arr_isAssoc([1 => 'a', 2 => 'b']));
        $this->assertFalse(arr_isAssoc([0 => 'a', 1 => 'b']));
        $this->assertFalse(arr_isAssoc(['a', 'b']));
    }

    public function testOnly()
    {
        $array = ['name' => 'Desk', 'price' => 100, 'orders' => 10];
        $array = arr_only($array, ['name', 'price']);
        $this->assertEquals(['name' => 'Desk', 'price' => 100], $array);
    }

    public function testPluck()
    {
        $array = [
            ['developer' => ['name' => 'Taylor']],
            ['developer' => ['name' => 'Abigail']],
        ];
        $array = arr_pluck($array, 'developer.name');
        $this->assertEquals(['Taylor', 'Abigail'], $array);
    }

    public function testPluckWithKeys()
    {
        $array = [
            ['name' => 'Taylor', 'role' => 'developer'],
            ['name' => 'Abigail', 'role' => 'developer'],
        ];
        $test1 = arr_pluck($array, 'role', 'name');
        $test2 = arr_pluck($array, null, 'name');
        $this->assertEquals([
            'Taylor'  => 'developer',
            'Abigail' => 'developer',
        ], $test1);
        $this->assertEquals([
            'Taylor'  => ['name' => 'Taylor', 'role' => 'developer'],
            'Abigail' => ['name' => 'Abigail', 'role' => 'developer'],
        ], $test2);
    }

    public function testPrepend()
    {
        $array = arr_prepend(['one', 'two', 'three', 'four'], 'zero');
        $this->assertEquals(['zero', 'one', 'two', 'three', 'four'], $array);
        $array = arr_prepend(['one' => 1, 'two' => 2], 0, 'zero');
        $this->assertEquals(['zero' => 0, 'one' => 1, 'two' => 2], $array);
    }

    public function testPull()
    {
        $array = ['name' => 'Desk', 'price' => 100];
        $name  = arr_pull($array, 'name');
        $this->assertEquals('Desk', $name);
        $this->assertEquals(['price' => 100], $array);
        // Only works on first level keys
        $array = ['joe@example.com' => 'Joe', 'jane@localhost' => 'Jane'];
        $name  = arr_pull($array, 'joe@example.com');
        $this->assertEquals('Joe', $name);
        $this->assertEquals(['jane@localhost' => 'Jane'], $array);
        // Does not work for nested keys
        $array = ['emails' => ['joe@example.com' => 'Joe', 'jane@localhost' => 'Jane']];
        $name  = arr_pull($array, 'emails.joe@example.com');
        $this->assertEquals(null, $name);
        $this->assertEquals(['emails' => ['joe@example.com' => 'Joe', 'jane@localhost' => 'Jane']],
            $array);
    }

    public function testSet()
    {
        $array = ['products' => ['desk' => ['price' => 100]]];
        arr_set($array, 'products.desk.price', 200);
        $this->assertEquals(['products' => ['desk' => ['price' => 200]]], $array);
        arr_set($array, 'products.desk.price.euro', 200);
        $this->assertEquals(['products' => ['desk' => ['price' => ['euro' => 200]]]], $array);
        arr_set($array, null, 200);
        $this->assertEquals(200, $array);
    }

    public function testSort()
    {
        $this->markTestSkipped('Sort not implemented');
        $array    = [
            ['name' => 'Desk'],
            ['name' => 'Chair'],
        ];
        $array    = array_values(arr_sort($array, function ($value) {
            return $value['name'];
        }));
        $expected = [
            ['name' => 'Chair'],
            ['name' => 'Desk'],
        ];
        $this->assertEquals($expected, $array);
    }

    public function testSortRecursive()
    {
        $array  = [
            'users'        => [
                [
                    // should sort associative arrays by keys
                    'name'    => 'joe',
                    'mail'    => 'joe@example.com',
                    // should sort deeply nested arrays
                    'numbers' => [2, 1, 0],
                ],
                [
                    'name' => 'jane',
                    'age'  => 25,
                ],
            ],
            'repositories' => [
                // should use weird `sort()` behavior on arrays of arrays
                ['id' => 1],
                ['id' => 0],
            ],
            // should sort non-associative arrays by value
            20             => [2, 1, 0],
            30             => [
                // should sort non-incrementing numerical keys by keys
                2 => 'a',
                1 => 'b',
                0 => 'c',
            ],
        ];
        $expect = [
            20             => [0, 1, 2],
            30             => [
                0 => 'c',
                1 => 'b',
                2 => 'a',
            ],
            'repositories' => [
                ['id' => 0],
                ['id' => 1],
            ],
            'users'        => [
                [
                    'age'  => 25,
                    'name' => 'jane',
                ],
                [
                    'mail'    => 'joe@example.com',
                    'name'    => 'joe',
                    'numbers' => [0, 1, 2],
                ],
            ],
        ];
        $this->assertEquals($expect, arr_sortRecursive($array));
    }

    public function testWhere()
    {
        $array = [100, '200', 300, '400', 500];
        $array = arr_where($array, function ($value, $key) {
            return is_string($value);
        });
        $this->assertEquals([1 => 200, 3 => 400], $array);
    }

    public function testForget()
    {
        $array = ['products' => ['desk' => ['price' => 100]]];
        arr_forget($array, null);
        $this->assertEquals(['products' => ['desk' => ['price' => 100]]], $array);
        $array = ['products' => ['desk' => ['price' => 100]]];
        arr_forget($array, []);
        $this->assertEquals(['products' => ['desk' => ['price' => 100]]], $array);
        $array = ['products' => ['desk' => ['price' => 100]]];
        arr_forget($array, 'products.desk');
        $this->assertEquals(['products' => []], $array);
        $array = ['products' => ['desk' => ['price' => 100]]];
        arr_forget($array, 'products.desk.price');
        $this->assertEquals(['products' => ['desk' => []]], $array);
        $array = ['products' => ['desk' => ['price' => 100]]];
        arr_forget($array, 'products.final.price');
        $this->assertEquals(['products' => ['desk' => ['price' => 100]]], $array);
        $array = ['shop' => ['cart' => [150 => 0]]];
        arr_forget($array, 'shop.final.cart');
        $this->assertEquals(['shop' => ['cart' => [150 => 0]]], $array);
        $array = ['products' => ['desk' => ['price' => ['original' => 50, 'taxes' => 60]]]];
        arr_forget($array, 'products.desk.price.taxes');
        $this->assertEquals(['products' => ['desk' => ['price' => ['original' => 50]]]], $array);
        $array = ['products' => ['desk' => ['price' => ['original' => 50, 'taxes' => 60]]]];
        arr_forget($array, 'products.desk.final.taxes');
        $this->assertEquals(['products' => ['desk' => ['price' => ['original' => 50,
                                                                   'taxes'    => 60]]]], $array);
        $array = ['products' => ['desk' => ['price' => 50], null => 'something']];
        arr_forget($array, ['products.amount.all', 'products.desk.price']);
        $this->assertEquals(['products' => ['desk' => [], null => 'something']], $array);
        // Only works on first level keys
        $array = ['joe@example.com' => 'Joe', 'jane@example.com' => 'Jane'];
        arr_forget($array, 'joe@example.com');
        $this->assertEquals(['jane@example.com' => 'Jane'], $array);
        // Does not work for nested keys
        $array =
            ['emails' => ['joe@example.com' => ['name' => 'Joe'],
                          'jane@localhost'  => ['name' => 'Jane']]];
        arr_forget($array, ['emails.joe@example.com', 'emails.jane@localhost']);
        $this->assertEquals(['emails' => ['joe@example.com' => ['name' => 'Joe']]], $array);
    }
}
