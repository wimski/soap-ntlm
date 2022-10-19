[![PHPStan](https://github.com/wimski/soap-ntlm/actions/workflows/phpstan.yml/badge.svg)](https://github.com/wimski/soap-ntlm/actions/workflows/phpstan.yml)
[![PHPUnit](https://github.com/wimski/soap-ntlm/actions/workflows/phpunit.yml/badge.svg)](https://github.com/wimski/soap-ntlm/actions/workflows/phpunit.yml)
[![Coverage Status](https://coveralls.io/repos/github/wimski/soap-ntlm/badge.svg?branch=master)](https://coveralls.io/github/wimski/soap-ntlm?branch=master)

# Soap with NTLM

SOAP client with NTLM authentication support.

* [Changelog](#changelog)
* [Install](#install)
* [Usage](#usage)

## Changelog

[View the changelog.](./CHANGELOG.md)

## Install

```bash
composer require wimski/soap-ntlm
```

## Usage

```php
use Wimski\Curl\CurlResourceFactory;
use Wimski\SoapNtlm\ClientFactory;
use Wimski\SoapNtlm\NtlmService;
use Wimski\SoapNtlm\NtlmServiceConfig;
use Wimski\SoapNtlm\Stream\NtlmStreamWrapperFactory;

$curlResourceFactory = new CurlResourceFactory();

$config = new NtlmServiceConfig(
    'wsdl-uri',
    'ntlm-auth-username',
    'ntlm-auth-password',
    ['default' => 'option'],
);

$service = new NtlmService(
    $config,
    new ClientFactory($curlResourceFactory),
    new NtlmStreamWrapperFactory($curlResourceFactory),
);

$response = $service->request(
    'soap-endpoint',
    'action-function',
    ['some' => 'parameter'],
    ['extra' => 'option'],
);
```