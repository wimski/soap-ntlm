<?php

declare(strict_types=1);

namespace Wimski\SoapNtlm\Data;

use Wimski\Curl\Contracts\CurlResourceInterface;

class CurlStreamBuffer
{
    protected int $position = 0;

    public function __construct(
        protected CurlResourceInterface $curlResource,
        protected string $contents,
    ) {
    }

    public function getContents(): string
    {
        return $this->contents;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getLength(): int
    {
        return strlen($this->getContents());
    }

    public function readData(int $length): string
    {
        $data = substr($this->getContents(), $this->getPosition(), $length);

        $this->position += $length;

        return $data;
    }

    public function isAtEnd(): bool
    {
        return $this->getPosition() >= $this->getLength();
    }

    public function close(): void
    {
        $this->curlResource->close();
    }
}
