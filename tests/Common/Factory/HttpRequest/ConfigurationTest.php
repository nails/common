<?php

namespace Tests\Common\Factory\HttpRequest;

use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Psr7\Response;
use Nails\Common\Factory\HttpRequest\Delete;
use Nails\Common\Factory\HttpRequest\Get;
use Nails\Common\Factory\HttpRequest\Head;
use Nails\Common\Factory\HttpRequest\Options;
use Nails\Common\Factory\HttpRequest\Patch;
use Nails\Common\Factory\HttpRequest\Post;
use Nails\Common\Factory\HttpRequest\Put;
use Nails\Environment;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    /**
     * @return array<string, array{0: class-string}>
     */
    public static function requestClasses(): array
    {
        return [
            'get'     => [Get::class],
            'post'    => [Post::class],
            'put'     => [Put::class],
            'patch'   => [Patch::class],
            'delete'  => [Delete::class],
            'head'    => [Head::class],
            'options' => [Options::class],
        ];
    }

    // --------------------------------------------------------------------------

    #[DataProvider('requestClasses')]
    public function test_every_verb_accepts_timeouts(string $sClass): void
    {
        /** @var Get $oRequest */
        $oRequest = new $sClass();
        $oRequest
            ->timeout(4)
            ->connectTimeout(1.5)
            ->readTimeout(0);

        $this->assertSame(4, $oRequest->getOption('timeout'));
        $this->assertSame(1.5, $oRequest->getOption('connect_timeout'));
        $this->assertSame(0, $oRequest->getOption('read_timeout'));
    }

    // --------------------------------------------------------------------------

    public function test_negative_and_non_finite_timeouts_are_rejected(): void
    {
        $oRequest = new Get();

        foreach (['timeout', 'connectTimeout', 'readTimeout'] as $sMethod) {
            foreach ([-1, -0.01, INF, NAN] as $mSeconds) {
                try {
                    $oRequest->{$sMethod}($mSeconds);
                    $this->fail($sMethod . ' accepted ' . var_export($mSeconds, true));
                } catch (\InvalidArgumentException $oException) {
                    $this->assertStringContainsString('must be zero or greater', $oException->getMessage());
                }
            }
        }

        $this->assertNull($oRequest->getOption('timeout'));
        $this->assertNull($oRequest->getOption('connect_timeout'));
        $this->assertNull($oRequest->getOption('read_timeout'));
    }

    // --------------------------------------------------------------------------

    public function test_request_options_override_the_values_the_helper_builds(): void
    {
        $oRequest = new ConfigurablePost('https://example.test', '/widgets');
        $oRequest
            ->auth('ada', 'secret')
            ->query(['page' => 1])
            ->params(['form' => 'old'])
            ->body('{"old":true}')
            ->setHeader('X-Trace', 'original')
            ->setOptions([
                'auth'        => ['grace', 'other', 'digest'],
                'query'       => ['page' => 3],
                'form_params' => ['form' => 'new'],
                'body'        => 'raw',
                'proxy'       => 'http://10.0.0.1:8080',
                'curl'        => [\CURLOPT_TCP_NODELAY => 1],
                'headers'     => [
                    'X-Trace' => 'replaced',
                    'X-Extra' => 'yes',
                ],
            ])
            ->setOptions([
                'delay' => 15,
            ]);

        [$aClientConfig, $aRequestOptions] = $oRequest->built();

        $this->assertSame(['grace', 'other', 'digest'], $aRequestOptions['auth']);
        $this->assertSame(['page' => 3], $aRequestOptions['query']);
        $this->assertSame(['form' => 'new'], $aRequestOptions['form_params']);
        $this->assertSame('raw', $aRequestOptions['body']);
        $this->assertSame('http://10.0.0.1:8080', $aRequestOptions['proxy']);
        $this->assertSame([\CURLOPT_TCP_NODELAY => 1], $aRequestOptions['curl']);
        $this->assertSame(15, $aRequestOptions['delay']);
        $this->assertSame('replaced', $aRequestOptions['headers']['X-Trace']);
        $this->assertSame('yes', $aRequestOptions['headers']['X-Extra']);
        $this->assertSame('Nails', $aRequestOptions['headers']['User-Agent']);
        $this->assertSame('https://example.test', $aClientConfig['base_uri']);
    }

    // --------------------------------------------------------------------------

    public function test_client_config_overrides_built_in_defaults(): void
    {
        $oHandler = static function () {
        };
        $oRequest = new ConfigurablePost('https://example.test', '/widgets');
        $oRequest
            ->clientConfig([
                'verify'          => '/etc/ssl/certs/ca.pem',
                'allow_redirects' => ['max' => 2, 'strict' => true],
                'http_errors'     => true,
                'base_uri'        => 'https://override.test',
            ])
            ->setClientConfig('handler', $oHandler)
            ->setClientConfig('max_host_connections', 4);

        [$aClientConfig] = $oRequest->built();

        $this->assertSame('/etc/ssl/certs/ca.pem', $aClientConfig['verify']);
        $this->assertSame(['max' => 2, 'strict' => true], $aClientConfig['allow_redirects']);
        $this->assertTrue($aClientConfig['http_errors']);
        $this->assertSame('https://override.test', $aClientConfig['base_uri']);
        $this->assertSame($oHandler, $aClientConfig['handler']);
        $this->assertSame(4, $aClientConfig['max_host_connections']);
    }

    // --------------------------------------------------------------------------

    public function test_built_in_defaults_remain_when_nothing_is_overridden(): void
    {
        $oRequest = new ConfigurableGet('https://example.test', '/health');

        [$aClientConfig, $aRequestOptions] = $oRequest->built();

        $this->assertSame('https://example.test', $aClientConfig['base_uri']);
        $this->assertSame(
            !(Environment::is(Environment::ENV_DEV) || Environment::is(Environment::ENV_TEST)),
            $aClientConfig['verify']
        );
        $this->assertSame(Environment::not(Environment::ENV_TEST), $aClientConfig['allow_redirects']);
        $this->assertFalse($aClientConfig['http_errors']);
        $this->assertSame('Nails', $aRequestOptions['headers']['User-Agent']);
        $this->assertArrayNotHasKey('timeout', $aRequestOptions);
        $this->assertArrayNotHasKey('auth', $aRequestOptions);
    }

    // --------------------------------------------------------------------------

    public function test_null_headers_option_clears_the_header_map(): void
    {
        $oRequest = new ConfigurableGet();
        $oRequest->setOption('headers', null);

        [, $aRequestOptions] = $oRequest->built();

        $this->assertArrayHasKey('headers', $aRequestOptions);
        $this->assertNull($aRequestOptions['headers']);
    }

    // --------------------------------------------------------------------------

    public function test_options_reach_guzzle_and_a_request_option_wins_over_the_client(): void
    {
        $aSeen = null;
        $oRequest = new Post('https://example.test', '/widgets');
        $oRequest
            ->timeout(2.5)
            ->connectTimeout(1)
            ->readTimeout(0)
            ->auth('ada', 'secret')
            ->query(['page' => 2])
            ->setHeader('X-Trace', 'original')
            ->setOptions([
                'proxy'       => 'http://10.0.0.1:8080',
                'http_errors' => true,
                'verify'      => true,
                'curl'        => [\CURLOPT_TCP_NODELAY => 1],
                'json'        => ['name' => 'Ada'],
                'headers'     => [
                    'X-Trace' => 'replaced',
                    'X-Extra' => 'yes',
                ],
            ])
            ->setClientConfig('timeout', 30)
            ->setClientConfig('verify', false)
            ->clientConfig([
                'headers' => ['X-Client' => 'from-client'],
            ])
            ->setClientConfig('handler', function ($oPsrRequest, array $aOptions) use (&$aSeen) {
                $aSeen = [
                    'method'  => $oPsrRequest->getMethod(),
                    'uri'     => (string) $oPsrRequest->getUri(),
                    'headers' => [
                        'user-agent'   => $oPsrRequest->getHeaderLine('User-Agent'),
                        'x-trace'      => $oPsrRequest->getHeaderLine('X-Trace'),
                        'x-extra'      => $oPsrRequest->getHeaderLine('X-Extra'),
                        'x-client'     => $oPsrRequest->getHeaderLine('X-Client'),
                        'content-type' => $oPsrRequest->getHeaderLine('Content-Type'),
                    ],
                    'body'    => (string) $oPsrRequest->getBody(),
                    'options' => $aOptions,
                ];

                return new FulfilledPromise(new Response(204));
            });

        $oResponse = $oRequest->execute();

        $this->assertNotNull($aSeen);
        $this->assertSame(204, $oResponse->getStatusCode());
        $this->assertSame('POST', $aSeen['method']);
        $this->assertSame('https://example.test/widgets?page=2', $aSeen['uri']);
        $this->assertSame('Nails', $aSeen['headers']['user-agent']);
        $this->assertSame('replaced', $aSeen['headers']['x-trace']);
        $this->assertSame('yes', $aSeen['headers']['x-extra']);
        $this->assertSame('from-client', $aSeen['headers']['x-client']);
        $this->assertSame('application/json', $aSeen['headers']['content-type']);
        $this->assertSame('{"name":"Ada"}', $aSeen['body']);
        $this->assertSame(2.5, $aSeen['options']['timeout']);
        $this->assertSame(1, $aSeen['options']['connect_timeout']);
        $this->assertSame(0, $aSeen['options']['read_timeout']);
        $this->assertSame(['ada', 'secret'], $aSeen['options']['auth']);
        $this->assertSame('http://10.0.0.1:8080', $aSeen['options']['proxy']);
        $this->assertTrue($aSeen['options']['http_errors']);
        $this->assertTrue($aSeen['options']['verify']);
        $this->assertSame([\CURLOPT_TCP_NODELAY => 1], $aSeen['options']['curl']);
    }
}

// --------------------------------------------------------------------------

class ConfigurablePost extends Post
{
    /**
     * @return array{0: array, 1: array}
     */
    public function built(): array
    {
        return $this->build();
    }
}

// --------------------------------------------------------------------------

class ConfigurableGet extends Get
{
    /**
     * @return array{0: array, 1: array}
     */
    public function built(): array
    {
        return $this->build();
    }
}
