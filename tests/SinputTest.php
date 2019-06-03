<?php

declare(strict_types=1);

namespace Devayes\Tests\Sinput;

use Devayes\Sinput\Sinput;
use Illuminate\Http\Request;
use Devayes\Tests\Sinput\AbstractTestCase;
use Devayes\Sinput\Middleware\SinputMiddleware;

class SinputTest extends AbstractTestCase
{

    /**
     * Temporary HTML config to test filtering
     * @date   2019-05-29
     * @return array
     */
    protected function getHTMLConfig()
    {
        return ['HTML.Allowed' => 'b,strong'];
    }

    /**
     * Temporary rule set that excludes all HTML
     * @date   2019-05-29
     * @return array
     */
    protected function getStripConfig()
    {
        return [
            'HTML.Doctype' => 'HTML 4.01 Transitional',
            'Core.Encoding' => 'UTF-8',
            'HTML.Allowed' => ''
        ];
    }

    /**
     * Test object can be created.
     * @date   2019-05-29
     * @return [type]     [description]
     */
    public function testConstruct()
    {
        $sinput = $this->app->make('sinput');
        $this->assertInstanceOf(Sinput::class, $sinput);
    }

    /**
     * Test our configuration
     * @date   2019-05-29
     * @return boolean
     */
    public function testConfigIsSet()
    {
        $sinput = $this->app->make('sinput');
        $default = [
            'default_ruleset' => 'default',
            'middleware_ruleset' => 'html',
            'decode_input' => true,
            'decode_output' => true
        ];
        $this->assertSame($sinput->GetConfig(), $default);
    }

    /**
     * Test that our configuration can be set
     * @date   2019-05-29
     * @return boolean
     */
    public function testSetConfig()
    {
        $sinput = $this->app->make('sinput');
        $sinput->setConfig('default_ruleset', 'html');
        $this->assertSame('html', $sinput->getConfig('default_ruleset'));
        $sinput->setConfig('default_ruleset', 'default');
        $this->assertSame('default', $sinput->getConfig('default_ruleset'));
    }

    /**
     * Test clean method, main method all other methods pass through.
     * @date   2019-05-29
     * @return [type]     [description]
     */
    public function testClean()
    {
        $sinput = $this->app->make('sinput');
        $input = '<b>bold</b> <i>italic</i>';
        $html = $sinput->clean(
            $input,
            null,
            $this->getStripConfig()
        );
        $this->assertSame('bold italic', $html);
    }

    /**
     * Test clean with custom inline config.
     * @date   2019-05-29
     * @return boolean
     */
    public function testCleanWithCustomConfig()
    {
        $sinput = $this->app->make('sinput');

        $input = '<b>bold</b> <i>italic</i> <del>delete</del>';
        $html = $sinput->clean($input, null, [
            'HTML.Allowed' => 'b, del'
        ]);

        $this->assertSame('<b>bold</b> italic <del>delete</del>', $html);
    }

    /**
     * Test cleaning and filtering an array
     * @date   2019-05-29
     * @return boolean
     */
    public function testCleanAndDirtyMethod()
    {
        $sinput = $this->app->make('sinput');
        $input = ['foo' => '<b>bold</b> <i>italic</i>'];

        $clean = $sinput->clean($input, null, $this->getStripConfig());
        $html = $sinput->clean($input, null, $this->getHTMLConfig());

        $this->assertSame(['foo' => 'bold italic'], $clean);
        $this->assertSame(['foo' => '<b>bold</b> italic'], $html);
    }

    /**
     * Test $input = sinput()->all($config)
     * @date   2019-05-29
     * @return boolean
     */
    public function testPostAll()
    {
        $response = $this->post(md5('sinput'),
            ['foo' => '<b>bold</b> <i>italic</i>']
        );
        $sinput = $this->app->make('sinput');
        $clean = $sinput->all($this->getStripConfig());
        $html = $sinput->all($this->getHTMLConfig());
        $this->assertSame(['foo' => 'bold italic'], $clean);
        $this->assertSame(['foo' => '<b>bold</b> italic'], $html);
    }

    /**
     * Test list($foo) = sinput()->match('foo', $config)
     * Or: list($foo, bar) = sinput()->match(['foo', 'bar'], $config)
     * @date   2019-05-29
     * @return boolean
     */
    public function testPostList()
    {
        $response = $this->post(md5('sinput'),
            ['foo' => '<b>bold</b> <i>italic</i>', 'bar' => '<script>alert();</script>']
        );
        $sinput = $this->app->make('sinput');
        list($clean) = $sinput->list('foo', $this->getStripConfig());
        list($html) = $sinput->list(['foo'], $this->getHTMLConfig());
        $this->assertSame('bold italic', $clean);
        $this->assertSame('<b>bold</b> italic', $html);
    }

    /**
     * Test $foo = sinput()->match('#^fo#', $config)
     * @date   2019-05-29
     * @return boolean
     */
    public function testPostMatch()
    {
        $response = $this->post(md5('sinput'),
            ['foo' => '<b>bold</b> <i>italic</i>', 'bar' => '<script>alert();</script>']
        );
        $sinput = $this->app->make('sinput');
        $clean = $sinput->match('#^fo#', $this->getStripConfig());
        $html = $sinput->match('#^fo#', $this->getHTMLConfig());
        $this->assertSame(['foo' => 'bold italic'], $clean);
        $this->assertSame(['foo' => '<b>bold</b> italic'], $html);
    }

    /**
     * Test $foo = sinput()->only('foo', $config)
     * @date   2019-05-29
     * @return boolean
     */
    public function testPostOnly()
    {
        $response = $this->post(md5('sinput'),
            ['foo' => '<b>bold</b> <i>italic</i>', 'bar' => '<script>alert();</script>']
        );
        $sinput = $this->app->make('sinput');
        $clean = $sinput->only('foo', $this->getStripConfig());
        $html = $sinput->only(['foo'], $this->getHTMLConfig());
        $this->assertSame(['foo' => 'bold italic'], $clean);
        $this->assertSame(['foo' => '<b>bold</b> italic'], $html);
    }

    /**
     * Test $bar = sinput()->except('foo', $config)
     * @date   2019-05-29
     * @return boolean
     */
    public function testPostExcept()
    {
        $response = $this->post(md5('sinput'),
            ['foo' => '<b>bold</b> <i>italic</i>', 'bar' => '<script>alert();</script>']
        );
        $sinput = $this->app->make('sinput');
        $clean = $sinput->except('bar', $this->getStripConfig());
        $html = $sinput->except(['bar'], $this->getHTMLConfig());
        $this->assertSame(['foo' => 'bold italic'], $clean);
        $this->assertSame(['foo' => '<b>bold</b> italic'], $html);
    }

    /**
     * Test deep nested multi-dimensional array
     * @date   2019-05-29
     * @return boolean
     */
    public function testDeepArray()
    {
        $sinput = $this->app->make('sinput');
        $input = [
            'foo' => ['bar' => ['cat' => ['cow' => ['moo' => '<b>bold</b> <i>italic</i>']]]]
        ];

        $clean = $sinput->clean($input, null, $this->getStripConfig());
        $html = $sinput->clean($input, null, $this->getHTMLConfig());

        $this->assertSame([
            'foo' => ['bar' => ['cat' => ['cow' => ['moo' => 'bold italic']]]]
        ], $clean);
        $this->assertSame([
            'foo' => ['bar' => ['cat' => ['cow' => ['moo' => '<b>bold</b> italic']]]]
        ], $html);
    }

    public function testMiddleware()
    {
        $request = new Request;

        $request->merge([
            'nohtml' => 'Plain text.',
            'dirty' => '<script><p>Dirty.</b>',
            'clean' => '<p>This is <b>OK</b></p>.'
        ]);

        $middleware = new SinputMiddleware;

        $middleware->handle($request, function ($req) {
            $this->assertEquals('Plain text.', $req->nohtml);
            $this->assertEquals('<p>Dirty.</p>', $req->dirty);
            $this->assertEquals('<p>This is <b>OK</b></p>.', $req->clean);
        });
    }
}
