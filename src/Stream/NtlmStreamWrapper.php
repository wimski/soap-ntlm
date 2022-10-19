<?php

declare(strict_types=1);

namespace Wimski\SoapNtlm\Stream;

use Wimski\Curl\Contracts\CurlResourceFactoryInterface;
use Wimski\SoapNtlm\Contracts\Stream\NtlmStreamWrapperInterface;

class NtlmStreamWrapper implements NtlmStreamWrapperInterface
{
    public function __construct(
        protected CurlResourceFactoryInterface $curlResourceFactory,
        protected string $protocol,
    ) {
    }

    public function wrap(string $username, string $password): void
    {
        NtlmStream::setCurlResourceFactory($this->curlResourceFactory);
        NtlmStream::setCredentials($username, $password);

        stream_wrapper_unregister($this->protocol);
        stream_wrapper_register($this->protocol, NtlmStream::class);
    }

    public function unwrap(): void
    {
        stream_wrapper_restore($this->protocol);
    }
}
