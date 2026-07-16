<?php

declare(strict_types=1);

namespace App\Composer;

use Composer\IO\IOInterface;
use Composer\Script\Event;
use DateTime;

final class InstallPreCommitHook
{
    public static function install(Event $event): void
    {
        $io = $event->getIO();
        $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
        $projectRoot = dirname($vendorDir);
        $hooks = [
            "{$vendorDir}/bin/phpcs -q",
            "{$vendorDir}/bin/phpstan -q --no-progress --memory-limit=1G",
        ];
        $gitPath = $projectRoot . '/.git';
        if (is_dir($gitPath)) {
            $preCommitHookPath = "{$gitPath}/hooks/pre-commit";
            $hook = self::hookContents($hooks);
            self::backupExistingHook($preCommitHookPath, $hook, $io);
            file_put_contents($preCommitHookPath, $hook);
            chmod($preCommitHookPath, 0755);
        }
    }

    protected static function hookContents(array $hooks): string
    {
        $lines = [
            '#!/bin/sh',
            'set -e',
            ...$hooks,
        ];

        return implode("\n", $lines) . "\n";
    }

    protected static function backupExistingHook(
        string $preCommitHookPath,
        string $hook,
        IOInterface $io,
    ): void {
        if (file_exists($preCommitHookPath)) {
            $contents = file_get_contents($preCommitHookPath);
            if ($contents !== $hook) {
                $date = new DateTime()->format('Y-m-d_His');
                $backupLocation = "{$preCommitHookPath}.backup.{$date}";
                file_put_contents($backupLocation, $contents);
                $io->write("Replacing Pre-Commit Hook");
                $io->write("Existing Pre-Commit Hook saved to {$backupLocation}");
            }
        } else {
            $io->write("Creating Pre-Commit Hook");
        }
    }
}
