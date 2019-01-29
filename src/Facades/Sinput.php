<?php

declare(strict_types=1);

/*
 * This file is part of Laravel Sinput.
 *
 * (c) Devin Hayes <devayes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace devayes\Sinput\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * This is the sinput facade class.
 *
 * @author Devin Hayes <devayes@gmail.com>
 */
class Sinput extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'sinput';
    }
}
