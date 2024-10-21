<?php

declare(strict_types=1);

namespace App\Composer;

use Composer\Script\Event;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class MigrateCustomTemplates
{
    public static function migrate(Event $event): void
    {
        $io = $event->getIO();
        $filesystem = new Filesystem();
        $finder = new Finder();

        $oldEmailTemplateDir = realpath(dirname(__DIR__) . '/../custom/email_templates/');
        $newEmailTemplateDir = realpath(dirname(__DIR__) . '/../custom/templates/email/');

        if ($filesystem->exists($oldEmailTemplateDir)) {
            $finder->files()->in($oldEmailTemplateDir);
            foreach ($finder as $file) {
                $filesystem->mkdir($newEmailTemplateDir);
                $filesystem->rename(
                    $file->getPathname(),
                    $newEmailTemplateDir . '/' . $file->getBasename()
                );
                $io->write(
                    sprintf('<info>Migrated custom email template %s to new location.</info>', $file->getBasename())
                );
            }

            $filesystem->remove($oldEmailTemplateDir);
            $io->write(
                sprintf('<info>Deelted old template directory.</info>')
            );
        }
    }
}
