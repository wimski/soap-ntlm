<?php

declare(strict_types=1);

namespace Wimski\SoapNtlm;

use Wimski\SoapNtlm\Contracts\ClientFactoryInterface;
use Wimski\SoapNtlm\Contracts\NtlmServiceConfigInterface;
use Wimski\SoapNtlm\Contracts\NtlmServiceInterface;
use Wimski\SoapNtlm\Contracts\Stream\NtlmStreamWrapperFactoryInterface;
use Wimski\SoapNtlm\Contracts\Stream\NtlmStreamWrapperInterface;

class NtlmService implements NtlmServiceInterface
{
    protected NtlmStreamWrapperInterface $ntlmStreamWrapper;

    public function __construct(
        protected NtlmServiceConfigInterface $config,
        protected ClientFactoryInterface $clientFactory,
        NtlmStreamWrapperFactoryInterface $ntlmStreamWrapperFactory,
    ) {
        /** @var string $protocol */
        $protocol = parse_url($this->config->getUri(), PHP_URL_SCHEME);

        $this->ntlmStreamWrapper = $ntlmStreamWrapperFactory->make($protocol);
    }

    public function request(
        string $endpoint,
        string $action,
        array $parameters = [],
        array $options = [],
    ): mixed {
        $this->ntlmStreamWrapper->wrap(
            $this->config->getUsername(),
            $this->config->getPassword(),
        );

        $client = $this->clientFactory->makeNtlm(
            $this->config->getUsername(),
            $this->config->getPassword(),
            rtrim($this->config->getUri(), '/') . '/' . ltrim($endpoint, '/'),
            array_merge($this->config->getDefaultOptions(), $options),
        );

        $response = $client->__soapCall($action, $parameters);

        $this->ntlmStreamWrapper->unwrap();

        return $response;
    }
}
