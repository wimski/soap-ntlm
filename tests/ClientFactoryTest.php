<?php

declare(strict_types=1);

namespace Wimski\SoapNtlm\Tests;

use PHPUnit\Framework\TestCase;
use SoapClient;
use Wimski\Curl\Contracts\CurlResourceFactoryInterface;
use Wimski\SoapNtlm\ClientFactory;
use Wimski\SoapNtlm\NtlmClient;

class ClientFactoryTest extends TestCase
{
    protected ClientFactory $factory;
    protected CurlResourceFactoryInterface $curlResourceFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->curlResourceFactory = $this->createMock(CurlResourceFactoryInterface::class);

        $this->factory = new ClientFactory($this->curlResourceFactory);
    }

    /**
     * @test
     */
    public function it_makes_a_soap_client(): void
    {
        $client = $this->factory->make('file://' . realpath(__DIR__ . '/stubs/wsdl.xml'));

        self::assertInstanceOf(SoapClient::class, $client);
        self::assertNotInstanceOf(NtlmClient::class, $client);
    }

    /**
     * @test
     */
    public function it_makes_a_ntlm_client(): void
    {
        $client = $this->factory->makeNtlm(
            'user',
            'pass',
            'file://' . realpath(__DIR__ . '/stubs/wsdl.xml'),
        );

        self::assertInstanceOf(SoapClient::class, $client);
        self::assertInstanceOf(NtlmClient::class, $client);
    }
}
