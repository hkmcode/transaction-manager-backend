<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class IndianUsersExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
    	$user = new User();
        return $user->getIndianUsersDetails();
    }

    public function headings(): array
    {
        return [
            'First Name',
            'Last Name',
            'Email',
            'Phone Number',
            'Food Preferences',
            'Food Allergies',
            'Avoidable Foods',
            'User Type',
            'Designation',
            'Travel Mode',
            'Arrival Sector',
            'Arrival Airline / Train Name',
            'Arrival Airline / Train Number',
            'Arrival Date',
            'Arrival Time',
            'Departure Sector',
            'Departure Airline / Train Name',
            'Departure Airline / Train Number',
            'Departure Date',
            'Departure Time',
            'Room Sharing Person Name',
            'Country Name',
            'Created Date',
            'Updated Date',
        ];
    }
}
