<?php

declare(strict_types=1);

namespace Wimski\SoapNtlm\Contracts;

interface NtlmServiceConfigInterface
{
    public function getUri(): string;
    public function getUsername(): string;
    public function getPassword(): string;

    /**
     * @return array<string, mixed>
     */
    public function getDefaultOptions(): array;
}
