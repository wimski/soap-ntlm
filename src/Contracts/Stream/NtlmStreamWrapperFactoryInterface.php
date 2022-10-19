<?php

declare(strict_types=1);

namespace Wimski\SoapNtlm\Contracts\Stream;

interface NtlmStreamWrapperFactoryInterface
{
    public function make(string $protocol): NtlmStreamWrapperInterface;
}
