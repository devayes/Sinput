<?php

declare(strict_types=1);

namespace Devayes\Tests\Sinput;

use Devayes\Sinput\Sinput;
use GrahamCampbell\TestBenchCore\ServiceProviderTrait;

/*
 * ServiceProviderTest
 */
class ServiceProviderTest extends AbstractTestCase
{
    use ServiceProviderTrait;

    public function testSinputIsInjectable()
    {
        $this->assertIsInjectable(Sinput::class);
    }
}
