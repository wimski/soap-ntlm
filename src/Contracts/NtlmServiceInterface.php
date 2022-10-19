<?php

declare(strict_types=1);

namespace Wimski\SoapNtlm\Contracts;

use SoapFault;

interface NtlmServiceInterface
{
    /**
     * @param string               $endpoint
     * @param string               $action
     * @param array<string, mixed> $parameters
     * @param array<string, mixed> $options
     * @return mixed
     * @throws SoapFault
     */
    public function request(
        string $endpoint,
        string $action,
        array $parameters = [],
        array $options = [],
    ): mixed;
}
