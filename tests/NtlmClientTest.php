<?php

declare(strict_types=1);

namespace Wimski\SoapNtlm\Tests;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Wimski\Curl\Contracts\CurlResourceFactoryInterface;
use Wimski\Curl\Contracts\CurlResourceInterface;
use Wimski\Curl\CurlError;
use Wimski\SoapNtlm\NtlmClient;

class NtlmClientTest extends TestCase
{
    protected NtlmClient $client;
    protected MockObject $curlResource;

    protected function setUp(): void
    {
        parent::setUp();

        $this->curlResource = $this->createMock(CurlResourceInterface::class);

        $this->curlResource
            ->expects(self::once())
            ->method('setOptions')
            ->with([
                CURLOPT_HTTPAUTH       => CURLAUTH_NTLM,
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: text/xml; charset=utf-8',
                    'SOAPAction: action',
                ],
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => 'request-body',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_USERPWD        => 'user:pass',
            ])
            ->willReturnSelf();

        $curlResourceFactory = $this->createMock(CurlResourceFactoryInterface::class);
        $curlResourceFactory
            ->expects(self::once())
            ->method('make')
            ->with('location')
            ->willReturn($this->curlResource);

        $this->client = new NtlmClient(
            $curlResourceFactory,
            'user',
            'pass',
            'file://' . realpath(__DIR__ . '/stubs/wsdl.xml'),
        );
    }

    /**
     * @test
     * @dataProvider responseDataProvider
     */
    public function it_makes_a_request(?string $responseData): void
    {
        $this->curlResource
            ->expects(self::once())
            ->method('execute')
            ->willReturn($responseData);

        $response = $this->client->__doRequest(
            'request-body',
            'location',
            'action',
            3,
        );

        self::assertSame($responseData, $response);
    }

    /**
     * @return array<int, array<int, string|null>>
     */
    public function responseDataProvider(): array
    {
        return [
            ['response-data'],
            [null],
        ];
    }

    /**
     * @test
     */
    public function it_returns_null_when_request_fails(): void
    {
        $this->curlResource
            ->expects(self::once())
            ->method('execute')
            ->willThrowException(
                $this->createMock(CurlError::class),
            );

        $response = $this->client->__doRequest(
            'request-body',
            'location',
            'action',
            3,
        );

        self::assertNull($response);
    }
}
