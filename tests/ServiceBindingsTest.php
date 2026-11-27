<?php declare(strict_types=1);

namespace KboPublicSearch\Laravel\Tests;

use InvalidArgumentException;
use KboPublicSearch\Core\Http\KboConnector;
use KboPublicSearch\KboPublicSearch;
use KboPublicSearch\Localization\Languages;
use PHPUnit\Framework\Attributes\Test;
use ReflectionProperty;

final class ServiceBindingsTest extends KboPublicSearchTestCase
{
    #[Test]
    public function exception_is_thrown_if_config_is_missing(): void
    {
        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The KBO config is missing.');

        // Act
        $this->app[KboPublicSearch::class];
    }

    #[Test]
    public function exception_is_thrown_if_config_is_incomplete(): void
    {
        // Arrange
        $this->app['config']->set('services.kbo', [
            'username' => 'wsop-12345-1',
        ]);

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The KBO config is missing the "password" key.');

        // Act
        $this->app[KboPublicSearch::class];
    }

    #[Test]
    public function exception_is_thrown_if_env_is_invalid(): void
    {
        // Arrange
        $this->app['config']->set('services.kbo', [
            'username' => 'wsop-12345-1',
            'password' => 'secret',
            'env' => true,
        ]);

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The KBO config is invalid and the "env" key must be a string.');

        // Act
        $this->app[KboPublicSearch::class];
    }

    #[Test]
    public function kbo_can_be_resolved(): void
    {
        // Arrange
        $this->configure();

        // Act
        $instance = $this->app[KboPublicSearch::class];

        // Assert
        $this->assertInstanceOf(KboPublicSearch::class, $instance);
        $this->assertSame($instance, $this->app[KboPublicSearch::class]);
    }

    #[Test]
    public function kbo_can_be_resolved_using_alias(): void
    {
        // Arrange
        $this->configure();

        // Act
        $instance = $this->app['kbo'];

        // Assert
        $this->assertInstanceOf(KboPublicSearch::class, $instance);
    }

    #[Test]
    public function kbo_can_be_resolved_using_helper(): void
    {
        // Arrange
        $this->configure();

        // Act
        $instance = kbo();

        // Assert
        $this->assertInstanceOf(KboPublicSearch::class, $instance);
    }

    #[Test]
    public function kbo_uses_production_environment_by_default(): void
    {
        // Arrange
        $this->configure();

        // Act
        $url = $this->client($this->app[KboPublicSearch::class])->resolveBaseUrl();

        // Assert
        $this->assertSame('https://kbopub.economie.fgov.be/kbopubws180000/services/wsKBOPub', $url);
    }

    #[Test]
    public function kbo_uses_test_environment_if_configured(): void
    {
        // Arrange
        $this->configure(['env' => 'test']);

        // Act
        $url = $this->client($this->app[KboPublicSearch::class])->resolveBaseUrl();

        // Assert
        $this->assertSame('https://kbopub-acc.economie.fgov.be/kbopubws180000/services/wsKBOPub', $url);
    }

    #[Test]
    public function kbo_uses_dutch_by_default(): void
    {
        // Arrange
        $this->configure();
        $this->app->setLocale('en');

        // Act
        $languages = $this->languages($this->app[KboPublicSearch::class]);

        // Assert
        $this->assertSame(['nl'], $languages);
    }

    #[Test]
    public function kbo_uses_app_locale(): void
    {
        // Arrange
        $this->configure();
        $this->app->setLocale('fr');

        // Act
        $languages = $this->languages($this->app[KboPublicSearch::class]);

        // Assert
        $this->assertSame(['fr'], $languages);
    }

    #[Test]
    public function kbo_follows_app_locale_updates(): void
    {
        // Arrange
        $this->configure();
        $kbo = $this->app[KboPublicSearch::class];

        // Act
        $this->app->setLocale('fr');

        // Assert
        $this->assertSame(['fr'], $this->languages($kbo));

        // Act
        $this->app->setLocale('nl');

        // Assert
        $this->assertSame(['nl'], $this->languages($kbo));
    }

    #[Test]
    public function app_locale_updates_do_not_resolve_kbo(): void
    {
        // Act
        $this->app->setLocale('fr');

        // Assert
        $this->assertFalse($this->app->resolved(KboPublicSearch::class));
    }

    /** @param array<string, mixed> $overrides */
    private function configure(array $overrides = []): void
    {
        $this->app['config']->set('services.kbo', [
            'username' => 'wsop-12345-1',
            'password' => 'secret',
            ...$overrides,
        ]);
    }

    private function client(KboPublicSearch $kbo): KboConnector
    {
        $client = new ReflectionProperty($kbo, 'client')->getValue($kbo);

        $this->assertInstanceOf(KboConnector::class, $client);

        return $client;
    }

    /** @return list<string> */
    private function languages(KboPublicSearch $kbo): array
    {
        $languages = new ReflectionProperty(KboConnector::class, 'languages')->getValue($this->client($kbo));

        $this->assertInstanceOf(Languages::class, $languages);

        return $languages->toArray();
    }
}
