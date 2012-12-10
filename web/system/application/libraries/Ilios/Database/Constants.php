<?php

/**
 * Constant-interface providing application wide database configuration defaults.
 */
interface Ilios2_Database_Constants
{
    /**
     * @var int number of attempts to retry a failed database transaction
     */
    const TRANSACTION_RETRY_COUNT = 7;
}
