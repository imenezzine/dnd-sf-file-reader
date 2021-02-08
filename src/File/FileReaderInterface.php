<?php

declare(strict_types=1);

namespace App\File;

interface FileReaderInterface
{
    public function getLines(string $path): array;

    public function format(array $rows): array;
}
