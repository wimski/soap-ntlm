<?php

declare(strict_types=1);

namespace Wimski\SoapNtlm\Stream;

use Exception;
use Wimski\Curl\Contracts\CurlResourceFactoryInterface;
use Wimski\SoapNtlm\Data\CurlStreamBuffer;

class NtlmStream
{
    protected static ?CurlResourceFactoryInterface $curlResourceFactory = null;
    protected static ?string $username = null;
    protected static ?string $password = null;

    protected ?CurlStreamBuffer $buffer = null;
    protected ?string $path = null;

    public static function setCurlResourceFactory(CurlResourceFactoryInterface $curlResourceFactory): void
    {
        self::$curlResourceFactory = $curlResourceFactory;
    }

    public static function setCredentials(string $username, string $password): void
    {
        self::$username = $username;
        self::$password = $password;
    }

    // phpcs:ignore
    public function stream_open(
        string $path,
        string $mode,
        int $options,
        ?string &$opened_path,
    ): bool {
        $this->path   = $path;
        $this->buffer = $this->createBuffer($path);

        return $this->buffer !== null;
    }

    // phpcs:ignore
    public function stream_close(): void
    {
        self::$username = null;
        self::$password = null;

        if (! $this->buffer) {
            return;
        }

        $this->buffer->close();
        $this->buffer = null;
    }

    // phpcs:ignore
    public function stream_read(int $count): string|false
    {
        return $this->buffer ? $this->buffer->readData($count) : false;
    }

    // phpcs:ignore
    public function stream_eof(): bool
    {
        return ! $this->buffer || $this->buffer->isAtEnd();
    }

    /**
     * @param string $path
     * @param int    $flags
     * @return array<string, int>|false
     */
    // phpcs:ignore
    public function url_stat(string $path, int $flags): array|false
    {
        $buffer = $this->createBuffer($path);

        if (! $buffer) {
            return ['size' => 0];
        }

        return ['size' => $buffer->getLength()];
    }

    protected function createBuffer(string $path): ?CurlStreamBuffer
    {
        if (! self::$curlResourceFactory) {
            return null;
        }

        try {
            $curlResource = self::$curlResourceFactory->make($path);

            $curlResource->setOptions([
                CURLOPT_HTTPAUTH       => CURLAUTH_NTLM,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_USERPWD        => self::$username . ':' . self::$password,
            ]);

            $response = $curlResource->execute();
        } catch (Exception $exception) {
            $response = null;
        }

        if (! isset($curlResource)) {
            return null;
        }

        if (! is_string($response)) {
            $curlResource->close();

            return null;
        }

        return new CurlStreamBuffer($curlResource, $response);
    }
}
