<?php

declare(strict_types=1);

namespace Wimski\SoapNtlm\Stream;

use Wimski\Curl\Contracts\CurlResourceFactoryInterface;
use Wimski\SoapNtlm\Contracts\Stream\NtlmStreamWrapperFactoryInterface;
use Wimski\SoapNtlm\Contracts\Stream\NtlmStreamWrapperInterface;

class NtlmStreamWrapperFactory implements NtlmStreamWrapperFactoryInterface
{
    public function __construct(
        protected CurlResourceFactoryInterface $curlResourceFactory,
    ) {
    }

    public function make(string $protocol): NtlmStreamWrapperInterface
    {
        return new NtlmStreamWrapper($this->curlResourceFactory, $protocol);
    }
}
