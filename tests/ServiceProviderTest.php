<?php

declare(strict_types=1);

namespace Devayes\Tests\Sinput;

use GrahamCampbell\TestBenchCore\ServiceProviderTrait;
use Devayes\Sinput\Sinput;

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