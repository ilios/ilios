<?php

/**
 * Utility class providing common functionality for dealing with database transactions.
 * NOTE:
 * In order to function properly, this class requires a fully instantiated
 * CI environment to be present.
 *
 * @see Abstract_Ilios_Model
 */
class Ilios_Database_TransactionHelper
{
    /**
     * Triggers a transaction rollback on the given model and decrements
     * the given transaction retry counter.
     * @param int $transactionRetryCount
     * @param boolean $failedTransaction
     * @param Ilios_Base_Model $model
     */
    public static function failTransaction (&$transactionRetryCount, &$failedTransaction, $model)
    {
        $failedTransaction = true;

        // rollback the last database transaction
        $model->rollbackTransaction();

        // decrement counter
        $transactionRetryCount--;

        // send the script to sleep for a random (and hopefully short amount of time)
        $deadlockSleeper = new Ilios_Database_DeadlockSleeper($transactionRetryCount, $model->getTableName());
        $deadlockSleeper->sleep();
    }
}
