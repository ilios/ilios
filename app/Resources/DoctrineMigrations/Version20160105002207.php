<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use SebastianBergmann\RecursionContext\Exception;

/**
 * Correct timezone offsets and fix times to 5pm for ILM due dates.
 */
class Version20160105002207 extends AbstractMigration
{
    /**
     * @inheritdoc
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $ilms = $this->getIlms();
        $timezone = $this->getTimezone();
        foreach ($ilms as $ilm) {
            $dueDate = new \DateTime($ilm['due_date']);
            $offset = $timezone->getOffset($dueDate);
            $offset = $offset * -1;
            $dueDate->modify("{$offset} seconds")->modify('+1 day')->modify('-7 hours');
            $this->updateIlmDueDate($ilm['ilm_session_facet_id'], $dueDate);
        }
    }

    /**
     * @inheritdoc
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $ilms = $this->getIlms();
        $timezone = $this->getTimezone();
        foreach ($ilms as $ilm) {
            $dueDate = new \DateTime($ilm['due_date']);
            $offset = $timezone->getOffset($dueDate);
            $dueDate->modify('+7 hours')->modify('-1 day')->modify("{$offset} seconds");
            $this->updateIlmDueDate($ilm['ilm_session_facet_id'], $dueDate);
        }
    }

    /**
     * @return \DateTimeZone
     * @throws Exception
     */
    private function getTimezone()
    {
        return new \DateTimeZone(date_default_timezone_get());
    }

    /**
     * @return array
     */
    private function getIlms()
    {
        $sql = 'SELECT ilm_session_facet_id, due_date FROM ilm_session_facet ORDER BY ilm_session_facet_id ASC';
        return $this->connection->fetchAll($sql);
    }

    /**
     * @param int $id
     * @param \DateTime $dueDate
     */
    private function updateIlmDueDate($id, \DateTime $dueDate)
    {
        $sql = 'UPDATE ilm_session_facet SET due_date = ? WHERE ilm_session_facet_id = ?';
        $this->addSql($sql, [ $dueDate->format('Y-m-d H:i:s'), $id ]);
    }
}
