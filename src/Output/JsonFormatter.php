<?php

declare(strict_types=1);

namespace App\Output;

use Symfony\Component\Serializer\Encoder\EncoderInterface;

class JsonFormatter
{
    /**
     * @var EncoderInterface
     */
    private $encoder;

    public function __construct(EncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function render(array $rows): string
    {
        $formattedRows = [];
        foreach ($rows as $row) {
            array_push($formattedRows, array_combine($this->headers(), $row));
        }

        return $this->encoder->encode($formattedRows, 'json');
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
