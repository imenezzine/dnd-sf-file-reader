<?php

declare(strict_types=1);

namespace App\Output;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

class TableFormatter
{
    public function render(OutputInterface $output, array $rows): void
    {
        $table = new Table($output);
        $table->setHeaders($this->headers())
            ->setRows($rows);
        $table->render();
    }

    private function headers(): array
    {
        return [
            'Sku',
            'Status',
            'Price',
            'Description',
            'Created At',
            'Slug',
        ];
    }
}
