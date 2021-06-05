<?php

declare(strict_types=1);

namespace Devayes\Tests\Sinput;

use Devayes\Sinput\Sinput;
//use Illuminate\Http\Request;
use Devayes\Tests\Sinput\AbstractTestCase;
//use Devayes\Sinput\Middleware\SinputMiddleware;

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
        $config = $sinput->GetConfig();
        $this->assertIsArray($config);
        $this->assertArrayHasKey('default_ruleset', $config);
        $this->assertSame('default', $config['default_ruleset']);
        $this->assertArrayHasKey('middleware_ruleset', $config);
        $this->assertSame('html', $config['middleware_ruleset']);
        $this->assertArrayHasKey('decode_input', $config);
        $this->assertSame(true, $config['decode_input']);
        $this->assertArrayHasKey('decode_output', $config);
        $this->assertSame(true, $config['decode_output']);
        $this->assertArrayHasKey('purifier', $config);
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
        $html = $sinput->clean($input, [
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

        $clean = $sinput->clean($input, $this->getStripConfig());
        $html = $sinput->clean($input, $this->getHTMLConfig());

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

        $clean = $sinput->clean($input, $this->getStripConfig());
        $html = $sinput->clean($input, $this->getHTMLConfig());

        $this->assertSame([
            'foo' => ['bar' => ['cat' => ['cow' => ['moo' => 'bold italic']]]]
        ], $clean);
        $this->assertSame([
            'foo' => ['bar' => ['cat' => ['cow' => ['moo' => '<b>bold</b> italic']]]]
        ], $html);
    }

    /**
     * Test middleware
     * Request object in abstract class does not contain this mock data.
     * Works with normal web requests.
     * @date   2019-06-03
     * @return boolean
     */
    /*public function testMiddleware()
    {
        $request = Request::create(md5('sinput'), 'POST');

        $request->replace([
            'nohtml' => 'Plain text.',
            'dirty' => '<script><b>bar</b></script>',
            'broken' => '<p>Broken</h1>',
            'clean' => '<p>This is <b>OK</b></p>.'
        ]);

        $middleware = new SinputMiddleware;

        $middleware->handle($request, function ($req) {
            $this->assertEquals('Plain text.', $req->nohtml);
            $this->assertEquals('', $req->dirty);
            $this->assertEquals('<p>Broken</p>', $req->broken);
            $this->assertEquals('<p>This is <b>OK</b></p>.', $req->clean);
        });
    }*/
}
