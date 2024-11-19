<?php

declare(strict_types=1);

namespace App\Service;

use Vaites\ApacheTika\Client;

class TikaFactory
{
    public static function getClient(Config $config): ?Client
    {
        $url = $config->get('tika_url');
        if ($url) {
            return Client::prepare($url, null, [CURLOPT_TIMEOUT => 30]);
        }

        return null;
    }
}
