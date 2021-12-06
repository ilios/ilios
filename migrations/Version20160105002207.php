<?php
declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use DateTimeZone;
use Doctrine\DBAL\Schema\Schema;
use SebastianBergmann\RecursionContext\Exception;

/**
 * Correct timezone offsets and fix times to 5pm for ILM due dates.
 */
final class Version20160105002207 extends MysqlMigration
{
    public function up(Schema $schema) : void
    {
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

    public function down(Schema $schema) : void
    {
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
     * @throws Exception
     */
    private function getTimezone()
    {
        return new DateTimeZone(date_default_timezone_get());
    }

    
    private function getIlms()
    {
        $sql = 'SELECT ilm_session_facet_id, due_date FROM ilm_session_facet ORDER BY ilm_session_facet_id ASC';
        return $this->connection->fetchAllAssociative($sql);
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
