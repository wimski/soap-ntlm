<?php

declare(strict_types=1);

namespace Wimski\SoapNtlm\Tests\Stream;

use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;
use Wimski\Curl\Contracts\CurlResourceFactoryInterface;
use Wimski\SoapNtlm\Stream\NtlmStream;
use Wimski\SoapNtlm\Stream\NtlmStreamWrapper;

class NtlmStreamWrapperTest extends TestCase
{
    use PHPMock;

    protected NtlmStreamWrapper $ntlmStreamWrapper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ntlmStreamWrapper = new NtlmStreamWrapper(
            $this->createMock(CurlResourceFactoryInterface::class),
            'https',
        );
    }

    /**
     * @test
     */
    public function it_wraps(): void
    {
        $this->getFunctionMock('Wimski\SoapNtlm\Stream', 'stream_wrapper_unregister')
            ->expects(self::once())
            ->with('https');

        $this->getFunctionMock('Wimski\SoapNtlm\Stream', 'stream_wrapper_register')
            ->expects(self::once())
            ->with('https', NtlmStream::class);

        $this->ntlmStreamWrapper->wrap('user', 'pass');
    }

    /**
     * @test
     */
    public function it_unwraps(): void
    {
        $this->getFunctionMock('Wimski\SoapNtlm\Stream', 'stream_wrapper_restore')
            ->expects(self::once())
            ->with('https');

        $this->ntlmStreamWrapper->unwrap();
    }
}
