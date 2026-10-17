<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SqlToJsonController extends Controller
{
    public function uploadAndProcess(Request $request)
    {
        if (!$request->hasFile('sql_file')) {
            return response()->json(['error' => 'No SQL file uploaded'], 400);
        }

        $sqlFile = $request->file('sql_file');
        $sqlContent = file_get_contents($sqlFile->getRealPath());

        $jsonData = [];

        // Regex to match and capture multi-value INSERT statements
        $pattern = '/^INSERT INTO `?(\w+)`? \(`?(.+?)`?\) VALUES(.+?);$/ims';

        if (preg_match_all($pattern, $sqlContent, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                // Get table name and column names
                $tableName = $match[1];
                $columnNames = array_map('trim', explode(',', str_replace('`', '', $match[2])));

                // Split the values string into individual rows
                $valuesString = trim($match[3]);
                $rows = explode('),', $valuesString);

                foreach ($rows as $row) {
                    $row = trim($row, " \n\r\t,()");

                    // Use regex to get values within each row
                    if (preg_match_all("/'(.*?)'|(\d+)/", $row, $valueMatches)) {
                        $values = $valueMatches[0];

                        // Create a key-value pair for each row
                        $dataRow = [];
                        foreach ($columnNames as $index => $column) {
                            // Trim quotes and format values
                            $value = trim($values[$index], " '");
                            if (is_numeric($value) && !preg_match("/'.*?'/", $values[$index])) {
                                $dataRow[$column] = (int)$value;
                            } else {
                                $dataRow[$column] = $value;
                            }
                        }
                        $jsonData[] = $dataRow;
                    }
                }
            }
        }

        return response()->json($jsonData);
    }
}