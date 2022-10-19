<?php

declare(strict_types=1);

namespace Wimski\SoapNtlm\Contracts;

use SoapClient;
use SoapFault;
use Wimski\SoapNtlm\NtlmClient;

interface ClientFactoryInterface
{
    /**
     * @param string|null          $wsdl
     * @param array<string, mixed> $options
     * @return SoapClient
     * @throws SoapFault
     */
    public function make(?string $wsdl, array $options = []): SoapClient;

    /**
     * @param string               $username
     * @param string               $password
     * @param string|null          $wsdl
     * @param array<string, mixed> $options
     * @return NtlmClient
     * @throws SoapFault
     */
    public function makeNtlm(
        string $username,
        string $password,
        ?string $wsdl,
        array $options = [],
    ): SoapClient;
}
