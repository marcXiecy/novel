<?php
namespace App\Services;

use Maatwebsite\Excel\Concerns\FromArray;

class CommonExport implements FromArray
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data;
    }
}