<?php

namespace Devayes\Tests\Sinput;

use Devayes\Sinput\Sinput;
use Illuminate\Http\Request;
use Illuminate\Contracts\Config\Repository;

class SinputTest extends AbstractTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testConstruct()
    {
        $sinput = $this->app->make('sinput');
        $this->assertInstanceOf(Sinput::class, $sinput);
    }

    public function testClean()
    {
        $sinput = $this->app->make('sinput');

        $input = '<b>bold</b> <i>italic</i>';
        $html = $sinput->clean($input);

        $this->assertSame('bold italic', $html);
    }

    public function testCleanWithCustomConfig()
    {
        $sinput = $this->app->make('sinput');

        $input = '<b>bold</b> <i>italic</i>';
        $html = $sinput->clean($input, [
            'HTML.Allowed' => 'b',
            'AutoFormat.AutoParagraph' => true
        ]);

        $this->assertSame('<p><b>bold</b> italic</p>', $html);
    }

    public function testAllMethod()
    {
        $request = new Request();
        $sinput = $this->app->make('sinput');

        $request->set('foo', '<b>bar</b>');
        $clean = $sinput->all();
        $html = $input->all('html');

        $this->assertSame(['foo' => 'bar'], $clean);
        $this->assertSame(['foo' => '<b>bar</b>'], $html);
    }
}