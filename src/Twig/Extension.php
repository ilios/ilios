<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class Extension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('insert_api_doc_info', [Runtime::class, 'insertApiDocInfo']),
        ];
    }

}
