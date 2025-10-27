<?php

declare(strict_types=1);

namespace App\Exception;

interface ApplicationExceptionInterface
{
    public function getStatus(): int;

    public function getErrors(): array;
}
