<?php

declare(strict_types=1);

namespace Devayes\Tests\Sinput;

use Devayes\Sinput\Sinput;
use Illuminate\Http\Request;
use Devayes\Tests\Sinput\AbstractTestCase;

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
            'URI.DisableExternalResources' => 1,
            'HTML.Allowed' => '',
            'AutoFormat.RemoveEmpty' => 0
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
}
