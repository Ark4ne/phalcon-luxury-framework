<?php
namespace Test\Support;

use Test\TestCase\TestCase;

/**
 * Class StrTest
 *
 * @package Support
 */
class StrFuncTest extends TestCase
{

    /**
     * Test the str_words method.
     *
     * @group laravel
     */
    public function testStringCanBeLimitedByWords()
    {
        $this->assertEquals('Taylor...', str_words('Taylor Otwell', 1));
        $this->assertEquals('Taylor___', str_words('Taylor Otwell', 1, '___'));
        $this->assertEquals('Taylor Otwell', str_words('Taylor Otwell', 3));
    }
    public function testStringTrimmedOnlyWhereNecessary()
    {
        $this->assertEquals(' Taylor Otwell ', str_words(' Taylor Otwell ', 3));
        $this->assertEquals(' Taylor...', str_words(' Taylor Otwell ', 1));
    }
    public function testStringTitle()
    {
        $this->assertEquals('Jefferson Costella', str_title('jefferson costella'));
        $this->assertEquals('Jefferson Costella', str_title('jefFErson coSTella'));
    }
    public function testStringWithoutWordsDoesntProduceError()
    {
        $nbsp = chr(0xC2).chr(0xA0);
        $this->assertEquals(' ', str_words(' '));
        $this->assertEquals($nbsp, str_words($nbsp));
    }
    public function testStartsWith()
    {
        $this->assertTrue(str_startsWith('jason', 'jas'));
        $this->assertTrue(str_startsWith('jason', 'jason'));
        $this->assertTrue(str_startsWith('jason', ['jas']));
        $this->assertTrue(str_startsWith('jason', ['day', 'jas']));
        $this->assertFalse(str_startsWith('jason', 'day'));
        $this->assertFalse(str_startsWith('jason', ['day']));
        $this->assertFalse(str_startsWith('jason', ''));
    }
    public function testEndsWith()
    {
        $this->assertTrue(str_endsWith('jason', 'on'));
        $this->assertTrue(str_endsWith('jason', 'jason'));
        $this->assertTrue(str_endsWith('jason', ['on']));
        $this->assertTrue(str_endsWith('jason', ['no', 'on']));
        $this->assertFalse(str_endsWith('jason', 'no'));
        $this->assertFalse(str_endsWith('jason', ['no']));
        $this->assertFalse(str_endsWith('jason', ''));
        $this->assertFalse(str_endsWith('7', ' 7'));
    }
    public function testStrContains()
    {
        $this->assertTrue(str_contains('taylor', 'ylo'));
        $this->assertTrue(str_contains('taylor', 'taylor'));
        $this->assertTrue(str_contains('taylor', ['ylo']));
        $this->assertTrue(str_contains('taylor', ['xxx', 'ylo']));
        $this->assertFalse(str_contains('taylor', 'xxx'));
        $this->assertFalse(str_contains('taylor', ['xxx']));
        $this->assertFalse(str_contains('taylor', ''));
    }
    public function testParseCallback()
    {
        $this->assertEquals(['Class', 'method'], str_parseCallback('Class@method', 'foo'));
        $this->assertEquals(['Class', 'foo'], str_parseCallback('Class', 'foo'));
    }
    public function testSlug()
    {
        $this->assertEquals('hello-world', str_slug('hello world'));
        $this->assertEquals('hello-world', str_slug('hello-world'));
        $this->assertEquals('hello-world', str_slug('hello_world'));
        $this->assertEquals('hello_world', str_slug('hello_world', '_'));
    }
    public function testFinish()
    {
        $this->assertEquals('abbc', str_finish('ab', 'bc'));
        $this->assertEquals('abbc', str_finish('abbcbc', 'bc'));
        $this->assertEquals('abcbbc', str_finish('abcbbcbc', 'bc'));
    }
    public function testIs()
    {
        $this->assertTrue(str_is('/', '/'));
        $this->assertFalse(str_is('/', ' /'));
        $this->assertFalse(str_is('/', '/a'));
        $this->assertTrue(str_is('foo/*', 'foo/bar/baz'));
        $this->assertTrue(str_is('*/foo', 'blah/baz/foo'));
        $valueObject = new StringableObjectStub('foo/bar/baz');
        $patternObject = new StringableObjectStub('foo/*');
        $this->assertTrue(str_is('foo/bar/baz', $valueObject));
        $this->assertTrue(str_is($patternObject, $valueObject));
    }
    public function testLower()
    {
        $this->assertEquals('foo bar baz', str_lower('FOO BAR BAZ'));
        $this->assertEquals('foo bar baz', str_lower('fOo Bar bAz'));
    }
    public function testUpper()
    {
        $this->assertEquals('FOO BAR BAZ', str_upper('foo bar baz'));
        $this->assertEquals('FOO BAR BAZ', str_upper('foO bAr BaZ'));
    }
    public function testLimit()
    {
        $this->assertEquals('Laravel is...', str_limit('Laravel is a free, open source PHP web application framework.', 10));
        $this->assertEquals('Laravel is awesome', str_limit('Laravel is awesome', 20));
        $this->assertEquals('这是一...', str_limit('这是一段中文', 6));
    }
    public function testLength()
    {
        $this->assertEquals(11, str_length('foo bar baz'));
    }
    public function testQuickRandom()
    {
        $randomInteger = mt_rand(1, 100);
        $this->assertEquals($randomInteger, strlen(str_quickRandom($randomInteger)));
        $this->assertInternalType('string', str_quickRandom());
        $this->assertEquals(16, strlen(str_quickRandom()));
    }
    public function testRandom()
    {
        $this->assertEquals(16, strlen(str_random()));
        $randomInteger = mt_rand(1, 100);
        $this->assertEquals($randomInteger, strlen(str_random($randomInteger)));
        $this->assertInternalType('string', str_random());
    }
    public function testReplaceFirst()
    {
        $this->assertEquals('fooqux foobar', str_replaceFirst('bar', 'qux', 'foobar foobar'));
        $this->assertEquals('foo/qux? foo/bar?', str_replaceFirst('bar?', 'qux?', 'foo/bar? foo/bar?'));
        $this->assertEquals('foo foobar', str_replaceFirst('bar', '', 'foobar foobar'));
        $this->assertEquals('foobar foobar', str_replaceFirst('xxx', 'yyy', 'foobar foobar'));
    }
    public function testReplaceLast()
    {
        $this->assertEquals('foobar fooqux', str_replaceLast('bar', 'qux', 'foobar foobar'));
        $this->assertEquals('foo/bar? foo/qux?', str_replaceLast('bar?', 'qux?', 'foo/bar? foo/bar?'));
        $this->assertEquals('foobar foo', str_replaceLast('bar', '', 'foobar foobar'));
        $this->assertEquals('foobar foobar', str_replaceLast('xxx', 'yyy', 'foobar foobar'));
    }
    public function testSnake()
    {
        $this->assertEquals('laravel_p_h_p_framework', str_snake('LaravelPHPFramework'));
        $this->assertEquals('laravel_php_framework', str_snake('LaravelPhpFramework'));
        $this->assertEquals('laravel php framework', str_snake('LaravelPhpFramework', ' '));
        $this->assertEquals('laravel_php_framework', str_snake('Laravel Php Framework'));
        $this->assertEquals('laravel_php_framework', str_snake('Laravel    Php      Framework   '));
        $this->assertEquals('laravel_php_framework', str_snake('Laravel    Php      Framework   '));
        // ensure cache keys don't overlap
        $this->assertEquals('laravel__php__framework', str_snake('LaravelPhpFramework', '__'));
        $this->assertEquals('laravel_php_framework_', str_snake('LaravelPhpFramework_', '_'));
    }
    public function testStudly()
    {
        $this->assertEquals('LaravelPHPFramework', str_studly('laravel_p_h_p_framework'));
        $this->assertEquals('LaravelPHPFramework', str_studly('laravel_p_h_p_framework'));
        $this->assertEquals('LaravelPhpFramework', str_studly('laravel_php_framework'));
        $this->assertEquals('LaravelPhPFramework', str_studly('laravel-phP-framework'));
        $this->assertEquals('LaravelPhpFramework', str_studly('laravel  -_-  php   -_-   framework   '));
    }
    public function testCamel()
    {
        $this->assertEquals('laravelPHPFramework', str_camel('Laravel_p_h_p_framework'));
        $this->assertEquals('laravelPHPFramework', str_camel('Laravel_p_h_p_framework'));
        $this->assertEquals('laravelPhpFramework', str_camel('Laravel_php_framework'));
        $this->assertEquals('laravelPhPFramework', str_camel('Laravel-phP-framework'));
        $this->assertEquals('laravelPhpFramework', str_camel('Laravel  -_-  php   -_-   framework   '));
    }
    public function testCapitalize()
    {
        $this->assertEquals('Laravel Framework', str_capitalize('LARAVEL framework'));
        $this->assertEquals('Laravel Framework', str_capitalize('LARAVEL framework'));
        $this->assertEquals('Laravel', str_capitalize('laravel'));
        $this->assertEquals('Laravel', str_capitalize('lArAVeL'));
    }
    public function testSubstr()
    {
        $this->assertEquals('Ё', str_substr('БГДЖИЛЁ', -1));
        $this->assertEquals('ЛЁ', str_substr('БГДЖИЛЁ', -2));
        $this->assertEquals('И', str_substr('БГДЖИЛЁ', -3, 1));
        $this->assertEquals('ДЖИЛ', str_substr('БГДЖИЛЁ', 2, -1));
        $this->assertEmpty(str_substr('БГДЖИЛЁ', 4, -4));
        $this->assertEquals('ИЛ', str_substr('БГДЖИЛЁ', -3, -1));
        $this->assertEquals('ГДЖИЛЁ', str_substr('БГДЖИЛЁ', 1));
        $this->assertEquals('ГДЖ', str_substr('БГДЖИЛЁ', 1, 3));
        $this->assertEquals('БГДЖ', str_substr('БГДЖИЛЁ', 0, 4));
        $this->assertEquals('Ё', str_substr('БГДЖИЛЁ', -1, 1));
        $this->assertEmpty(str_substr('Б', 2));
    }
    public function testUcfirst()
    {
        $this->assertEquals('Laravel', str_ucfirst('laravel'));
        $this->assertEquals('Laravel framework', str_ucfirst('laravel framework'));
        $this->assertEquals('Мама', str_ucfirst('мама'));
        $this->assertEquals('Мама мыла раму', str_ucfirst('мама мыла раму'));
    }
}

class StringableObjectStub
{
    private $value;
    public function __construct($value)
    {
        $this->value = $value;
    }
    public function __toString()
    {
        return $this->value;
    }
}
