<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;

#[IA\DTO("aamcMethods")]
#[IA\ExposeGraphQL]
class AamcMethodDTO
{
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type("array<string>")]
    public array $sessionTypes = [];

    public function __construct(
        #[IA\Expose]
        #[IA\Id]
        #[IA\Type("string")]
        public string $id,
        #[IA\Expose]
        #[IA\Type("string")]
        public string $description,
        #[IA\Expose]
        #[IA\Type("boolean")]
        public bool $active
    ) {
    }
}
