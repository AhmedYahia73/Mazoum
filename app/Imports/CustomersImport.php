<?php

namespace App\Imports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\ToModel;

class CustomersImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $check_if_name_name_not_exist = Customer::where('name','like','%'.$row[0].'%')->first();

        if($check_if_name_name_not_exist == null) {
            return new Customer([
                'name' => $row[0],  // Assuming the first column is 'name'
            ]);
        }
    }
}
