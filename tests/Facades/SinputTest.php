<?php

namespace Devayes\Tests\Sinput\Facades;

use GrahamCampbell\TestBenchCore\FacadeTrait;
use Devayes\Purifier\Facades\Sinput;
use Devayes\Tests\Sinput\AbstractTestCase;

class PurifierTest extends AbstractTestCase
{
    use FacadeTrait;

    /**
     * Get the facade accessor.
     *
     * @return string
     */
    protected function getFacadeAccessor()
    {
        return 'sinput';
    }

    /**
     * Get the facade class.
     *
     * @return string
     */
    protected function getFacadeClass()
    {
        return Sinput::class;
    }

    /**
     * Get the facade root.
     *
     * @return string
     */
    protected function getFacadeRoot()
    {
        return \Devayes\Sinput\Sinput::class;
    }
}