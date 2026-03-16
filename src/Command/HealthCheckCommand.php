<?php

declare(strict_types=1);

namespace App\Command;

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
use App\Service\HealthCheckRunner;
use Laminas\Diagnostics\Check\ApcFragmentation;
use Laminas\Diagnostics\Check\ApcMemory;
use Laminas\Diagnostics\Check\DirReadable;
use Laminas\Diagnostics\Check\DirWritable;
use Laminas\Diagnostics\Check\PhpVersion;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:health-check',
    description: 'Performs the full suite of application health monitoring checks.'
)]
class HealthCheckCommand extends Command
{
    public function __construct(
        protected HealthCheckRunner $runner,
        protected ApcFragmentation $apcFragmentationCheck,
        protected ApcMemory $apcMemoryCheck,
        protected Composer $composerCheck,
        protected DatabaseConnection $databaseConnectionCheck,
        protected DeprecatedConfigurationOption $deprecatedConfigurationOptionCheck,
        protected DirReadable $dirReadableCheck,
        protected DirWritable $dirWritableCheck,
        protected Frontend $frontendCheck,
        protected IliosFileSystem $fileSystemCheck,
        protected NoDefaultSecret $noDefaultSecretCheck,
        protected Migrations $migrationsCheck,
        protected PhpConfiguration $phpConfigCheck,
        protected PhpExtension $phpExtensionCheck,
        protected PhpVersion $phpVersionCheck,
        protected RequiredENV $requiredEnvCheck,
        protected SecretLength $secretLengthCheck,
        protected Timezone $timezoneCheck
    ) {
        parent::__construct();
    }

    public function __invoke(
        InputInterface $input,
        OutputInterface $output,
        #[Option(description: 'Run a minimal subset of checks only.')]bool $minimal = false
    ): int {
        $checks = $this->getChecks($minimal);
        $data = $this->runner->run($checks);
        $results = $data['results'];
        $summaryStatus = $data['summary_status'];
        $rhett = HealthCheckRunner::STATUS_OK === $summaryStatus ? Command::SUCCESS : Command::FAILURE;

        $io = new SymfonyStyle($input, $output);
        $io->title('Health Checks (' . count($results) . ')');
        $table = new Table($output);
        $table->setHeaders(['Check', 'Status', 'Message']);
        $table->setRows($results);
        $table->setFooterTitle(
            (self::FAILURE === $rhett ? '<error>' : '<info>') . 'Summary Status: ' . $summaryStatus . '</>'
        );
        $table->render();
        return $rhett;
    }

    protected function getChecks(bool $minimalOnly = false): array
    {
        if ($minimalOnly) {
            return [
                $this->deprecatedConfigurationOptionCheck,
                $this->dirReadableCheck,
                $this->dirWritableCheck,
                $this->phpExtensionCheck,
                $this->phpVersionCheck,
                $this->noDefaultSecretCheck,
                $this->requiredEnvCheck,
                $this->secretLengthCheck,
                $this->timezoneCheck,
            ];
        }

        return [
            $this->apcMemoryCheck,
            $this->apcFragmentationCheck,
            $this->composerCheck,
            $this->databaseConnectionCheck,
            $this->deprecatedConfigurationOptionCheck,
            $this->dirReadableCheck,
            $this->dirWritableCheck,
            $this->fileSystemCheck,
            $this->frontendCheck,
            $this->migrationsCheck,
            $this->phpConfigCheck,
            $this->phpExtensionCheck,
            $this->phpVersionCheck,
            $this->noDefaultSecretCheck,
            $this->requiredEnvCheck,
            $this->secretLengthCheck,
            $this->timezoneCheck,
        ];
    }
}
