<?php

declare(strict_types=1);

namespace App\File;

class CSVFileReader implements FileReaderInterface
{
    public function getLines(string $path): array
    {
        $rows = [];
        if (($handle = fopen($path, 'r')) !== false) {
            while (($data = fgetcsv($handle, 10000, PHP_EOL)) !== false) {
                $rows[] = explode(';', $data[0]);
            }
            fclose($handle);
        }
        array_shift($rows);

        return $rows;
    }

    public function format(array $rows): array
    {
        $formattedRows = [];
        foreach ($rows as $row) {
            $formattedRows[] = [
                $row[0],
                '1' === $row[2] ? 'Enable' : 'Disable',
                $this->formatPrice($row[3], $row[4]),
                $this->convertLineBreakHtml($row[5]),
                $this->formatDate($row[6]),
                $this->replaceSpecialCharacters($row[1]),
            ];
        }

        return $formattedRows;
    }

    private function formatPrice(string $price, string $currency): string
    {
        return number_format(round($price, 1), 2, ',', '').$currency;
    }

    private function convertLineBreakHtml(string $description): string
    {
        return strip_tags(str_replace(['\\r', '<br>', '<br/>'], PHP_EOL, $description));
    }

    private function formatDate(string $date): string
    {
        return date('l, d-M-Y H:i:s T', strtotime($date));
    }

    private function replaceSpecialCharacters(string $title): string
    {
        return preg_replace('/-+/', '-', preg_replace('/[^a-z0-9]/', '-', strtolower(str_replace(' ', '_', $title))));
    }
}
