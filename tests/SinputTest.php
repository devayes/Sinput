<?php

declare(strict_types=1);

namespace Devayes\Tests\Sinput;

use Devayes\Sinput\Sinput;
use Illuminate\Http\Request;
use Devayes\Tests\Sinput\AbstractTestCase;

class SinputTest extends AbstractTestCase
{

    public function testConstruct()
    {
        $sinput = $this->app->make('sinput');
        $this->assertInstanceOf(Sinput::class, $sinput);
    }

    public function testClean()
    {
        $input = '<b>bold</b> <i>italic</i>';
        $html = Sinput::clean($input);

        $this->assertSame('bold italic', $html);
    }

    public function testCleanWithCustomConfig()
    {
        $input = '<b>bold</b> <i>italic</i>';
        $html = Sinput::clean($input, [
            'HTML.Allowed' => 'b',
            'AutoFormat.AutoParagraph' => true
        ]);

        $this->assertSame('<p><b>bold</b> italic</p>', $html);
    }

    public function testAllMethod()
    {
        $request = new Request();

        $request->merge(['foo' => '<b>bar</b>']);
        $clean = Sinput::all();
        $html = Sinput::all('html');

        $this->assertSame(['foo' => 'bar'], $clean);
        $this->assertSame(['foo' => '<b>bar</b>'], $html);
    }
}
