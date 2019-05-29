<?php

declare(strict_types=1);

namespace Devayes\Tests\Sinput\Facades;

use GrahamCampbell\TestBenchCore\FacadeTrait;
use Devayes\Sinput\Facades\Sinput;
use Devayes\Tests\Sinput\AbstractTestCase;

class SinputTest extends AbstractTestCase
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
        return Devayes\Sinput\Facades\Sinput::class;
    }
}
