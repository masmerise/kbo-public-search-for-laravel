<?php declare(strict_types=1);

namespace KboPublicSearch\Laravel\Tests;

use KboPublicSearch\Laravel\ServiceProvider;
use Orchestra\Testbench\TestCase;

abstract class KboPublicSearchTestCase extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [ServiceProvider::class];
    }
}
