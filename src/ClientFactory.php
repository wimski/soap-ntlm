<?php

declare(strict_types=1);

namespace Wimski\SoapNtlm;

use SoapClient;
use Wimski\Curl\Contracts\CurlResourceFactoryInterface;
use Wimski\SoapNtlm\Contracts\ClientFactoryInterface;

class ClientFactory implements ClientFactoryInterface
{
    public function __construct(
        protected CurlResourceFactoryInterface $curlResourceFactory,
    ) {
    }

    public function make(?string $wsdl, array $options = []): SoapClient
    {
        return new SoapClient($wsdl, $options);
    }

    public function makeNtlm(
        string $username,
        string $password,
        ?string $wsdl,
        array $options = [],
    ): SoapClient {
        return new NtlmClient(
            $this->curlResourceFactory,
            $username,
            $password,
            $wsdl,
            $options,
        );
    }
}
