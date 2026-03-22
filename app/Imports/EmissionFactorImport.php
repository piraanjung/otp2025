<?php

namespace App\Imports;


use App\Models\EmissionFactor;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmissionFactorImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new EmissionFactor([
            'material_name' => $row['material_name'],
            'unit'          => $row['unit'] ?? 'kgCO2e/kg',
            'ef_value'      => $row['ef_value'],
            'source'        => $row['source'],
            'example'       => $row['example'],
        ]);
    }
}
