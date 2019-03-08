<?php

namespace Devayes\Tests\Sinput;

use Devayes\Sinput\Sinput;

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

    public function testCleaning()
    {
        $sinput = $this->app->make('sinput');

        $input = '<b>bold</b> <i>italic</i>';
        $html = $sinput->clean($input);

        $this->assertSame('bold italic', $html);
    }

    public function testCleaningWithCustomConfig()
    {
        $sinput = $this->app->make('sinput');

        $input = '<b>bold</b> <i>italic</i>';
        $html = $sinput->clean($input, [
            'HTML.Allowed' => 'b',
            'AutoFormat.AutoParagraph' => true
        ]);

        $this->assertSame('<p><b>bold</b> italic</p>', $html);
    }
}