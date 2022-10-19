<?php

declare(strict_types=1);

namespace Wimski\SoapNtlm;

use Exception;
use SoapClient;
use SoapFault;
use Wimski\Curl\Contracts\CurlResourceFactoryInterface;

class NtlmClient extends SoapClient
{
    /**
     * @param CurlResourceFactoryInterface $curlResourceFactory
     * @param string                       $username
     * @param string                       $password
     * @param string|null                  $wsdl
     * @param array<string, mixed>         $options
     * @throws SoapFault
     */
    public function __construct(
        protected CurlResourceFactoryInterface $curlResourceFactory,
        protected string $username,
        protected string $password,
        ?string $wsdl,
        array $options = [],
    ) {
        parent::__construct($wsdl, $options);
    }

    public function __doRequest(
        string $request,
        string $location,
        string $action,
        int $version,
        bool $oneWay = false,
    ): ?string {
        $headers = $this->flattenHeaders([
            'Content-Type' => 'text/xml; charset=utf-8',
            'SOAPAction'   => $action,
        ]);

        try {
            $curlResource = $this->curlResourceFactory->make($location);

            $curlResource->setOptions([
                CURLOPT_HTTPAUTH       => CURLAUTH_NTLM,
                CURLOPT_HTTPHEADER     => $headers,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $request,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_USERPWD        => "{$this->username}:{$this->password}",
            ]);

            $response = $curlResource->execute();

            $curlResource->close();
        } catch (Exception $exception) {
            $response = null;
        }

        return $response;
    }

    /**
     * @param array<string, int|string> $headers
     * @return array<int, string>
     */
    protected function flattenHeaders(array $headers): array
    {
        $result = [];

        foreach ($headers as $key => $value) {
            $result[] = "{$key}: {$value}";
        }

        return $result;
    }
}
