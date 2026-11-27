<?php declare(strict_types=1);

namespace KboPublicSearch\Laravel;

use KboPublicSearch\Authentication\Credentials;
use SensitiveParameter;

final readonly class Config
{
    public function __construct(
        public string $username,
        #[SensitiveParameter] public string $password,
        public string $env,
    ) {}

    public function credentials(): Credentials
    {
        return Credentials::present(
            username: $this->username,
            password: $this->password,
        );
    }

    public function wantsTestEnv(): bool
    {
        return $this->env === 'test';
    }
}
