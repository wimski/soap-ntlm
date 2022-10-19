<?php

declare(strict_types=1);

namespace Wimski\SoapNtlm;

use Wimski\SoapNtlm\Contracts\NtlmServiceConfigInterface;

class NtlmServiceConfig implements NtlmServiceConfigInterface
{
    /**
     * @param string               $uri
     * @param string               $username
     * @param string               $password
     * @param array<string, mixed> $defaultOptions
     */
    public function __construct(
        protected string $uri,
        protected string $username,
        protected string $password,
        protected array $defaultOptions = [],
    ) {
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getDefaultOptions(): array
    {
        return $this->defaultOptions;
    }
}
