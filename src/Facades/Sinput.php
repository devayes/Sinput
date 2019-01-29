<?php

declare(strict_types=1);

/*
 * Laravel Sinput.
 *
 * (c) Devin Hayes <devayes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace devayes\Sinput\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @author Devin Hayes <devayes@gmail.com>
 */
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
