<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Monitor\Composer;
use App\Monitor\DatabaseConnection;
use App\Monitor\DeprecatedConfigurationOption;
use App\Monitor\Frontend;
use App\Monitor\IliosFileSystem;
use App\Monitor\Migrations;
use App\Monitor\NoDefaultSecret;
use App\Monitor\PhpConfiguration;
use App\Monitor\PhpExtension;
use App\Monitor\RequiredENV;
use App\Monitor\SecretLength;
use App\Monitor\Timezone;
use Laminas\Diagnostics\Check\ApcFragmentation;
use Laminas\Diagnostics\Check\ApcMemory;
use Laminas\Diagnostics\Check\DirReadable;
use Laminas\Diagnostics\Check\DirWritable;
use Laminas\Diagnostics\Check\PhpVersion;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HealthCheckControllerTest extends WebTestCase
{
    protected array $fullChecks = [
        ApcMemory::class,
        ApcFragmentation::class,
        Composer::class,
        DatabaseConnection::class,
        DeprecatedConfigurationOption::class,
        DirReadable::class,
        DirWritable::class,
        IliosFileSystem::class,
        Frontend::class,
        Migrations::class,
        PhpConfiguration::class,
        PhpExtension::class,
        PhpVersion::class,
        NoDefaultSecret::class,
        RequiredENV::class,
        SecretLength::class,
        Timezone::class,
        ];

    protected array $minimalChecks = [
        DeprecatedConfigurationOption::class,
        DirReadable::class,
        DirWritable::class,
        PhpExtension::class,
        PhpVersion::class,
        NoDefaultSecret::class,
        RequiredENV::class,
        SecretLength::class,
        Timezone::class,
    ];

    public function testFullHealthCheckHtmlPage(): void
    {
        $this->runHealthChecksHtmlPageTest('/ilios/health-check', $this->fullChecks);
    }

    public function testMinimalHealthCheckHtmlPage(): void
    {
        $this->runHealthChecksHtmlPageTest('/ilios/health-check/minimal', $this->minimalChecks);
    }

    public function testFullHealthCheckJsonPayload(): void
    {
        $this->runHealthChecksJsonPayloadTest('/ilios/health-check', $this->fullChecks);
    }

    public function testMinimalHealthCheckJsonPayload(): void
    {
        $this->runHealthChecksJsonPayloadTest('/ilios/health-check/minimal', $this->minimalChecks);
    }

    protected function runHealthChecksJsonPayloadTest(string $url, array $checks): void
    {
        $client = static::createClient();
        $client->request('GET', $url);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertCount(count($checks), $data['results']);
        for ($i = 0, $n = count($checks); $i < $n; $i++) {
            $result = $data['results'][$i];
            $this->assertEquals($checks[$i], $result['check']);
            $this->assertMatchesRegularExpression('/Success|Failure|Warning|Skip/', $result['status']);
            $this->assertNotEmpty($result['message']);
        }
        $this->assertMatchesRegularExpression('/OK|KO/', $data['summary_status']);
    }

    protected function runHealthChecksHtmlPageTest(string $url, array $checks): void
    {
        $client = static::createClient();
        $client->request('GET', $url, server: ['HTTP_ACCEPT' => 'text/html']);
        $numChecks = count($checks);
        $this->assertResponseIsSuccessful();
        $this->assertPageTitleSame('Ilios | Health Checks');
        $this->assertSelectorTextContains('caption', "Health Checks ({$numChecks})");
        $this->assertSelectorCount($numChecks, 'tbody tr td:nth-of-type(1)');
        for ($i = 0, $n = count($checks); $i < $n; $i++) {
            $this->assertSelectorTextSame('tbody tr:nth-of-type(' . $i + 1 . ') td:nth-of-type(1)', $checks[$i]);
        }
        $this->assertSelectorTextContains('tfoot tr td', 'Status Summary:');
    }
}
