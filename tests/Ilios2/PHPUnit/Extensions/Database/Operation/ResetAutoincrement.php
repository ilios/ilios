<?php 

require_once 'PHPUnit/Extensions/Database/Operation/IDatabaseOperation.php';
require_once 'PHPUnit/Extensions/Database/Operation/Exception.php';

/**
 * Resets all AUTO_INCREMENT counters on all tables in a dataset.
 * @see PHPUnit_Extensions_Database_Operation_IDatabaseOperation
 */
class Ilios2_PHPUnit_Extensions_Database_Operation_ResetAutoincrement 
    implements PHPUnit_Extensions_Database_Operation_IDatabaseOperation
{

    /* 
     * @see PHPUnit_Extensions_Database_Operation_IDatabaseOperation::execute()
     */
    public function execute(PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection, 
            PHPUnit_Extensions_Database_DataSet_IDataSet $dataSet)
    {
        foreach ($dataSet->getReverseIterator() as $table) {
            $query = "ALTER TABLE {$connection->quoteSchemaObject($table->getTableMetaData()->getTableName())}"
                . " AUTO_INCREMENT = 1
            ";

            try {
                $connection->getConnection()->query($query);
            } catch (PDOException $e) {
                throw new PHPUnit_Extensions_Database_Operation_Exception('RESET_AUTOINCREMENT', 
                		$query, array(), $table, $e->getMessage());
            }
        }
    }
}