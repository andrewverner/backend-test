<?php

declare(strict_types=1);

namespace App\Trait\ORM;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

trait UuidPrimaryKeyTrait
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    public function getId(): Uuid
    {
        return $this->id;
    }
}
