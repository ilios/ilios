<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;

/**
 * Class MeshTreeDTO
 */
#[IA\DTO('meshTrees')]
#[IA\ExposeGraphQL]
class MeshTreeDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('integer')]
    public int $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $treeNumber;

    #[IA\Expose]
    #[IA\Related('meshDescriptors')]
    #[IA\Type('string')]
    public string $descriptor;

    public function __construct(int $id, string $treeNumber)
    {
        $this->id = $id;
        $this->treeNumber = $treeNumber;
    }
}
