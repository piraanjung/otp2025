<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
class UsersImport implements ToModel, WithHeadingRow
{
       public function model(array $row)
    {
        $user = new User([
            "username"=> $row["username"],
            "password"=> bcrypt($row["password"]),
            "email"=> $row["email"],
            "role_id" => 3, //user
            "status"=>$row["status"],
        ]);




    }
}
