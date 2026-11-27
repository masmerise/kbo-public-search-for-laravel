<?php declare(strict_types=1);

namespace KboPublicSearch\Laravel\Tests;

use KboPublicSearch\Authentication\Failure\CouldNotAuthenticate;
use KboPublicSearch\KboPublicSearch;
use PHPUnit\Framework\Attributes\Test;
use Throwable;

final class SmokeTest extends KboPublicSearchTestCase
{
    #[Test]
    public function execute(): void
    {
        // Setup
        $this->app['config']->set('services.kbo', [
            'username' => 'wsop-12345-1',
            'password' => 'secret',
        ]);

        // Run
        try {
            $this->app[KboPublicSearch::class]->codeLists->metaCodeList();

            $this->fail('Should have thrown an authentication exception.');
        } catch (Throwable $ex) {
            $this->assertInstanceOf(CouldNotAuthenticate::class, $ex);
        }
    }
}
