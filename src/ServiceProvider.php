<?php declare(strict_types=1);

namespace KboPublicSearch\Laravel;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Events\LocaleUpdated;
use Illuminate\Support\AggregateServiceProvider;
use InvalidArgumentException;
use KboPublicSearch\KboPublicSearch;
use Webmozart\Assert\Assert;

final class ServiceProvider extends AggregateServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(KboPublicSearch::class, $this->createKbo(...));
        $this->app->alias(KboPublicSearch::class, 'kbo');
    }

    public function boot(): void
    {
        $this->app['events']->listen(LocaleUpdated::class, UseLanguageWhenAppLocaleUpdated::class);
    }

    /** @throws InvalidArgumentException */
    private function createKbo(Application $app): KboPublicSearch
    {
        $config = $this->config($app['config']->get('services.kbo'));

        /** Authentication */
        $kbo = KboPublicSearch::authenticate($config->credentials());

        /** Environment */
        if ($config->wantsTestEnv()) {
            $kbo->useTest();
        }

        /** Localization */
        $app[UseLanguage::class]($kbo, $app->getLocale());

        return $kbo;
    }

    /** @throws InvalidArgumentException */
    private function config(mixed $config): Config
    {
        Assert::notNull($config, 'The KBO config is missing.');
        Assert::isArray($config, 'The KBO config must be an array.');
        Assert::keyExists($config, 'username', 'The KBO config is missing the "username" key.');
        Assert::string($config['username'], 'The KBO config is invalid and the "username" key must be a string.');
        Assert::keyExists($config, 'password', 'The KBO config is missing the "password" key.');
        Assert::string($config['password'], 'The KBO config is invalid and the "password" key must be a string.');

        $env = $config['env'] ?? 'production';

        Assert::string($env, 'The KBO config is invalid and the "env" key must be a string.');

        return new Config(
            username: $config['username'],
            password: $config['password'],
            env: $env,
        );
    }
}
