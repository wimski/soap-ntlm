<?php

declare(strict_types=1);

namespace Wimski\SoapNtlm\Tests;

use PHPUnit\Framework\TestCase;
use Wimski\SoapNtlm\Contracts\ClientFactoryInterface;
use Wimski\SoapNtlm\Contracts\Stream\NtlmStreamWrapperFactoryInterface;
use Wimski\SoapNtlm\Contracts\Stream\NtlmStreamWrapperInterface;
use Wimski\SoapNtlm\NtlmClient;
use Wimski\SoapNtlm\NtlmService;
use Wimski\SoapNtlm\NtlmServiceConfig;

class NtlmServiceTest extends TestCase
{
    /**
     * @test
     */
    public function it_makes_a_request(): void
    {
        $config = new NtlmServiceConfig(
            'https://soapserver.com/',
            'user',
            'pass',
            [
                'foo' => 'bar',
                'one' => 1,
            ],
        );

        $client = $this->createMock(NtlmClient::class);
        $client
            ->expects(self::once())
            ->method('__soapCall')
            ->with('getStuff', ['two' => 2])
            ->willReturn('response-data');

        $clientFactory = $this->createMock(ClientFactoryInterface::class);
        $clientFactory
            ->expects(self::once())
            ->method('makeNtlm')
            ->with('user', 'pass', 'https://soapserver.com/endpoint', [
                'foo'   => 'xxx',
                'one'   => 1,
                'lorem' => 'ipsum',
            ])
            ->willReturn($client);

        $ntlmStreamWrapper = $this->createMock(NtlmStreamWrapperInterface::class);
        $ntlmStreamWrapper
            ->expects(self::once())
            ->method('wrap')
            ->with('user', 'pass');
        $ntlmStreamWrapper
            ->expects(self::once())
            ->method('unwrap');

        $ntlmStreamWrapperFactory = $this->createMock(NtlmStreamWrapperFactoryInterface::class);
        $ntlmStreamWrapperFactory
            ->expects(self::once())
            ->method('make')
            ->with('https')
            ->willReturn($ntlmStreamWrapper);

        $service = new NtlmService(
            $config,
            $clientFactory,
            $ntlmStreamWrapperFactory,
        );

        $response = $service->request(
            '/endpoint',
            'getStuff',
            ['two' => 2],
            [
                'foo'   => 'xxx',
                'lorem' => 'ipsum',
            ],
        );

        self::assertSame('response-data', $response);
    }
}
