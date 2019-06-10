<?php

declare(strict_types=1);

namespace Devayes\Sinput\Facades;

use Illuminate\Support\Facades\Facade;

class Sinput extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'sinput';
    }
}
