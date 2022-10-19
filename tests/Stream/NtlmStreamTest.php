<?php

declare(strict_types=1);

namespace Wimski\SoapNtlm\Tests\Stream;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Wimski\Curl\Contracts\CurlResourceFactoryInterface;
use Wimski\Curl\Contracts\CurlResourceInterface;
use Wimski\SoapNtlm\Stream\NtlmStream;

class NtlmStreamTest extends TestCase
{
    protected NtlmStream $stream;
    protected MockObject $curlResourceFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->curlResourceFactory = $this->createMock(CurlResourceFactoryInterface::class);

        $this->stream = new NtlmStream();
    }

    /**
     * @test
     */
    public function it_reads_from_a_stream(): void
    {
        $this->mockCurlResource('path', 'foobar');

        $openedPath = null;

        $result = $this->stream->stream_open(
            'path',
            '',
            1,
            $openedPath,
        );

        self::assertTrue($result);
        self::assertSame('foo', $this->stream->stream_read(3));
        self::assertFalse($this->stream->stream_eof());
        self::assertSame('bar', $this->stream->stream_read(3));
        self::assertTrue($this->stream->stream_eof());
    }

    /**
     * @test
     */
    public function it_closes_a_stream(): void
    {
        $curlResource = $this->mockCurlResource('path', 'foobar');

        $curlResource
            ->expects(self::once())
            ->method('close');

        $openedPath = null;

        $this->stream->stream_open(
            'path',
            '',
            1,
            $openedPath,
        );

        $this->stream->stream_close();
    }

    /**
     * @test
     */
    public function it_reads_url_stats(): void
    {
        $this->mockCurlResource('path', 'foobar');

        self::assertSame(['size' => 6], $this->stream->url_stat('path', 0));
    }

    /**
     * @test
     */
    public function it_returns_size_0_if_not_buffer_could_be_created(): void
    {
        $this->setStaticValues();

        $this->curlResourceFactory
            ->expects(self::once())
            ->method('make')
            ->with('path')
            ->willThrowException(
                $this->createMock(RuntimeException::class),
            );

        self::assertSame(['size' => 0], $this->stream->url_stat('path', 0));
    }

    /**
     * @test
     */
    public function it_does_not_create_a_buffer_if_no_factory_is_set(): void
    {
        $openedPath = null;

        $result = $this->stream->stream_open(
            'path',
            '',
            1,
            $openedPath,
        );

        static::assertFalse($result);
    }

    /**
     * @test
     */
    public function it_does_not_create_a_buffer_if_the_response_is_not_a_string(): void
    {
        $curlResource = $this->mockCurlResource('path', null);

        $curlResource
            ->expects(self::once())
            ->method('close');

        $openedPath = null;

        $result = $this->stream->stream_open(
            'path',
            '',
            1,
            $openedPath,
        );

        static::assertFalse($result);

        $this->stream->stream_close();
    }

    protected function setStaticValues(): void
    {
        /** @var CurlResourceFactoryInterface $curlResourceFactory */
        $curlResourceFactory = $this->curlResourceFactory;

        NtlmStream::setCurlResourceFactory($curlResourceFactory);
        NtlmStream::setCredentials('user', 'pass');
    }

    protected function mockCurlResource(string $url, mixed $response): MockObject
    {
        $this->setStaticValues();

        $curlResource = $this->createMock(CurlResourceInterface::class);

        $curlResource
            ->expects(self::once())
            ->method('setOptions')
            ->with([
                CURLOPT_HTTPAUTH       => CURLAUTH_NTLM,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_USERPWD        => 'user:pass',
            ])
            ->willReturnSelf();

        $curlResource
            ->expects(self::once())
            ->method('execute')
            ->willReturn($response);

        $this->curlResourceFactory
            ->expects(self::once())
            ->method('make')
            ->with($url)
            ->willReturn($curlResource);

        return $curlResource;
    }
}
