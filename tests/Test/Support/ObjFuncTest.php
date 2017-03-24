<?php
namespace Test\Support;

use Test\TestCase\TestCase;

/**
 * Class ObjTest
 *
 * @package Support
 */
class ObjFuncTest extends TestCase
{

    public function testValue()
    {
        $this->assertEquals('foo', obj_value('foo'));
        $this->assertEquals('foo', obj_value(function () {
            return 'foo';
        }));
    }

    public function testFetch()
    {
        $object = (object)['baz' => 'boo', 'foo.bar' => 123, 'null' => null];
        $this->assertEquals(123, obj_fetch(null, null, 123));
        $this->assertEquals(null, obj_fetch($object, null));
        $this->assertEquals('baz', obj_fetch($object, null, 'baz'));
        $this->assertEquals('boo', obj_fetch($object, 'baz'));
        $this->assertEquals(123, obj_fetch($object, 'foo.bar'));
        $this->assertEquals(null, obj_fetch($object, 'null'));
        $this->assertEquals('test', obj_fetch($object, 'null', 'test'));
    }

    public function testRead()
    {
        $object = (object)['baz' => 'boo', 'foo.bar' => 123, 'null' => null];
        $this->assertEquals(123, obj_read(null, null, 123));
        $this->assertEquals(null, obj_read($object, null));
        $this->assertEquals('baz', obj_read($object, null, 'baz'));
        $this->assertEquals('boo', obj_read($object, 'baz'));
        $this->assertEquals(123, obj_read($object, 'foo.bar'));
        $this->assertEquals(null, obj_read($object, 'null'));
        $this->assertEquals(null, obj_read($object, 'null', 'test'));
        $this->assertEquals('test', obj_read($object, 'flip', 'test'));
    }

    public function testGet()
    {
        $object = (object)['baz' => 'boo', 'foo.bar' => 123, 'null' => null];

        $this->assertEquals(null, obj_get([], null, null));
        $this->assertEquals(null, obj_get($object, null, null));

        $this->assertEquals(
            123, obj_get((object)['baz' => 123, 'foo.bar' => 123, 'null' => null], 'baz', 987)
        );
        $this->assertEquals(
            654, obj_get((object)['baz' => 123, 'foo.bar' => 123, 'null' => null], 'baz.foo', 654)
        );
        $this->assertEquals(
            'abc',
            obj_get((object)['baz'     => 123,
                              'foo.bar' => 123,
                              'null'    => null,
                              'bar'     => (object)['foo' => 'abc']], 'bar.foo')
        );
        $this->assertEquals(
            'abc',
            obj_get((object)['baz'     => 123,
                              'foo.bar' => 123,
                              'null'    => null,
                              'bar'     => (object)['foo' => 'abc']], ['bar', 'foo'])
        );
        $this->assertEquals(
            '123',
            obj_get((object)['baz'     => 123,
                              'foo.bar' => 123,
                              'null'    => null,
                              'bar'     => (object)['foo' => 'abc', 'faa' => '123']], 'bar.faa')
        );
    }

    public function testSet()
    {
        $object = (object)['baz' => 'boo', 'foo.bar' => 123, 'null' => null];

        $this->assertEquals($object, obj_set($object, null, null));

        $this->assertEquals(
            (object)['baz' => 123, 'foo.bar' => 123, 'null' => null],
            obj_set($object, 'baz', 123)
        );
        $this->assertEquals(
            (object)['baz' => (object)['foo' => 123], 'foo.bar' => 123, 'null' => null],
            obj_set($object, 'baz.foo', 123)
        );
        $this->assertEquals(
            (object)['baz'     => (object)['foo' => 123],
                     'foo.bar' => 123,
                     'null'    => null,
                     'bar'     => (object)['foo' => 'xyz']],
            obj_set($object, 'bar.foo', 'xyz')
        );
        $this->assertEquals(
            (object)['baz'     => (object)['foo' => 123],
                     'foo.bar' => 123,
                     'null'    => null,
                     'bar'     => (object)['foo' => 'xyz', 'faa' => '123']],
            obj_set($object, 'bar.faa', '123')
        );
        $this->assertEquals(
            (object)['baz'     => (object)['foo' => 123],
                     'foo.bar' => 123,
                     'null'    => null,
                     'bar'     => (object)['foo' => 'xyz',
                                           'faa' => '123',
                                           'fre' => (object)['lol' => '123']]],
            obj_set($object, ['bar', 'fre', 'lol'], '123')
        );
        $this->assertEquals(
            (object)['baz'     => (object)['foo' => 123],
                     'foo.bar' => 123,
                     'null'    => null,
                     'bar'     => (object)['foo' => 'xyz',
                                           'faa' => '123',
                                           'fre' => (object)['lol' => '123']]],
            obj_set($object, 'bar.fre.lol', '123')
        );
    }

    public function testFill()
    {
        $object = (object)['baz' => 'boo', 'foo.bar' => 123, 'null' => null];

        $this->assertEquals($object, obj_fill($object, null, null));

        $this->assertEquals(
            (object)['baz' => 'boo', 'foo.bar' => 123, 'null' => null],
            obj_fill($object, 'baz', 123)
        );

        $this->assertEquals(
            (object)['baz' => 'boo', 'foo.bar' => 123, 'null' => null],
            obj_fill($object, 'baz.foo', 123)
        );
        $this->assertEquals(
            (object)['baz'     => 'boo',
                     'foo.bar' => 123,
                     'null'    => null,
                     'boo'     => (object)['bar' => 123]],
            obj_fill($object, 'boo.bar', 123)
        );
    }
}
