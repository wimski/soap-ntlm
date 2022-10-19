<?php

declare(strict_types=1);

namespace Wimski\SoapNtlm\Contracts\Stream;

interface NtlmStreamWrapperInterface
{
    public function wrap(string $username, string $password): void;
    public function unwrap(): void;
}
