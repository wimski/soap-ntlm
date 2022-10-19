<?php

declare(strict_types=1);

namespace Wimski\SoapNtlm\Tests\Stream;

use PHPUnit\Framework\TestCase;
use Wimski\Curl\Contracts\CurlResourceFactoryInterface;
use Wimski\SoapNtlm\Stream\NtlmStreamWrapper;
use Wimski\SoapNtlm\Stream\NtlmStreamWrapperFactory;

class NtlmStreamWrapperFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_makes_a_ntlm_stream_wrapper(): void
    {
        $factory = new NtlmStreamWrapperFactory(
            $this->createMock(CurlResourceFactoryInterface::class),
        );

        $ntlmStreamWrapper = $factory->make('https');

        self::assertInstanceOf(NtlmStreamWrapper::class, $ntlmStreamWrapper);
    }
}
