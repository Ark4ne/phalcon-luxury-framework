<?php
namespace Test\Support;

use Luxury\Support\Obj;

/**
 * Class ObjTest
 *
 * @package Support
 */
class ObjTest extends \PHPUnit_Framework_TestCase
{

    public function testValue()
    {
        $this->assertEquals('foo', Obj::value('foo'));
        $this->assertEquals('foo', Obj::value(function () {
            return 'foo';
        }));
    }

    public function testFetch()
    {
        $object = (object)['baz' => 'boo', 'foo.bar' => 123, 'null' => null];
        $this->assertEquals(123, Obj::fetch(null, null, 123));
        $this->assertEquals(null, Obj::fetch($object, null));
        $this->assertEquals('baz', Obj::fetch($object, null, 'baz'));
        $this->assertEquals('boo', Obj::fetch($object, 'baz'));
        $this->assertEquals(123, Obj::fetch($object, 'foo.bar'));
        $this->assertEquals(null, Obj::fetch($object, 'null'));
        $this->assertEquals('test', Obj::fetch($object, 'null', 'test'));
    }

    public function testRead()
    {
        $object = (object)['baz' => 'boo', 'foo.bar' => 123, 'null' => null];
        $this->assertEquals(123, Obj::read(null, null, 123));
        $this->assertEquals(null, Obj::read($object, null));
        $this->assertEquals('baz', Obj::read($object, null, 'baz'));
        $this->assertEquals('boo', Obj::read($object, 'baz'));
        $this->assertEquals(123, Obj::read($object, 'foo.bar'));
        $this->assertEquals(null, Obj::read($object, 'null'));
        $this->assertEquals(null, Obj::read($object, 'null', 'test'));
        $this->assertEquals('test', Obj::read($object, 'flip', 'test'));
    }

    public function testGet()
    {
        $object = (object)['baz' => 'boo', 'foo.bar' => 123, 'null' => null];

        $this->assertEquals(null, Obj::get([], null, null));
        $this->assertEquals(null, Obj::get($object, null, null));

        $this->assertEquals(
            123, Obj::get((object)['baz' => 123, 'foo.bar' => 123, 'null' => null], 'baz', 987)
        );
        $this->assertEquals(
            654, Obj::get((object)['baz' => 123, 'foo.bar' => 123, 'null' => null], 'baz.foo', 654)
        );
        $this->assertEquals(
            'abc',
            Obj::get((object)['baz'     => 123,
                              'foo.bar' => 123,
                              'null'    => null,
                              'bar'     => (object)['foo' => 'abc']], 'bar.foo')
        );
        $this->assertEquals(
            'abc',
            Obj::get((object)['baz'     => 123,
                              'foo.bar' => 123,
                              'null'    => null,
                              'bar'     => (object)['foo' => 'abc']], ['bar', 'foo'])
        );
        $this->assertEquals(
            '123',
            Obj::get((object)['baz'     => 123,
                              'foo.bar' => 123,
                              'null'    => null,
                              'bar'     => (object)['foo' => 'abc', 'faa' => '123']], 'bar.faa')
        );
    }

    public function testSet()
    {
        $object = (object)['baz' => 'boo', 'foo.bar' => 123, 'null' => null];

        $this->assertEquals($object, Obj::set($object, null, null));

        $this->assertEquals(
            (object)['baz' => 123, 'foo.bar' => 123, 'null' => null],
            Obj::set($object, 'baz', 123)
        );
        $this->assertEquals(
            (object)['baz' => (object)['foo' => 123], 'foo.bar' => 123, 'null' => null],
            Obj::set($object, 'baz.foo', 123)
        );
        $this->assertEquals(
            (object)['baz'     => (object)['foo' => 123],
                     'foo.bar' => 123,
                     'null'    => null,
                     'bar'     => (object)['foo' => 'xyz']],
            Obj::set($object, 'bar.foo', 'xyz')
        );
        $this->assertEquals(
            (object)['baz'     => (object)['foo' => 123],
                     'foo.bar' => 123,
                     'null'    => null,
                     'bar'     => (object)['foo' => 'xyz', 'faa' => '123']],
            Obj::set($object, 'bar.faa', '123')
        );
        $this->assertEquals(
            (object)['baz'     => (object)['foo' => 123],
                     'foo.bar' => 123,
                     'null'    => null,
                     'bar'     => (object)['foo' => 'xyz',
                                           'faa' => '123',
                                           'fre' => (object)['lol' => '123']]],
            Obj::set($object, ['bar', 'fre', 'lol'], '123')
        );
        $this->assertEquals(
            (object)['baz'     => (object)['foo' => 123],
                     'foo.bar' => 123,
                     'null'    => null,
                     'bar'     => (object)['foo' => 'xyz',
                                           'faa' => '123',
                                           'fre' => (object)['lol' => '123']]],
            Obj::set($object, 'bar.fre.lol', '123')
        );
    }

    public function testFill()
    {
        $object = (object)['baz' => 'boo', 'foo.bar' => 123, 'null' => null];

        $this->assertEquals($object, Obj::fill($object, null, null));

        $this->assertEquals(
            (object)['baz' => 'boo', 'foo.bar' => 123, 'null' => null],
            Obj::fill($object, 'baz', 123)
        );

        $this->assertEquals(
            (object)['baz' => 'boo', 'foo.bar' => 123, 'null' => null],
            Obj::fill($object, 'baz.foo', 123)
        );
        $this->assertEquals(
            (object)['baz'     => 'boo',
                     'foo.bar' => 123,
                     'null'    => null,
                     'boo'     => (object)['bar' => 123]],
            Obj::fill($object, 'boo.bar', 123)
        );
    }
}
