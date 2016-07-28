<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add icsf_feed_key to user table to use in calendar feed
 */
class Version20150916173024 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() != 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE `user` ADD ics_feed_key VARCHAR(64) NOT NULL');
        
        $sql = 'SELECT user_id FROM `user`';
        $rows = $this->connection->executeQuery($sql)->fetchAll();
        if (count($rows)) {
            $updates = array_map(function ($arr) {
                $random = random_bytes(128);
                $key = $arr['user_id'] . microtime() . '_' . $random;
                $key = hash('sha256', $key);

                return "WHEN {$arr['user_id']} THEN '{$key}'";
            }, $rows);
            
            //bulk update all the records to avoide a mess in the output
            $sql = 'UPDATE `user` SET ics_feed_key = (CASE user_id ';
            $sql .= implode($updates, ' ');
            
            $sql .= 'END)';
            
            $this->addSql($sql);
        }
        
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649AAB338A6 ON `user` (ics_feed_key)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() != 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );
        
        $this->addSql('ALTER TABLE `user` DROP INDEX UNIQ_8D93D649AAB338A6');
        $this->addSql('ALTER TABLE `user` DROP ics_feed_key');
    }
}
