<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $states = [
            ['name' => 'Bagar Hat', 'country_id' => 1, 'status' => 1],
            ['name' => 'Bandarban', 'country_id' => 1, 'status' => 1],
            ['name' => 'Barguna', 'country_id' => 1, 'status' => 1],
            ['name' => 'Barisal', 'country_id' => 1, 'status' => 1],
            ['name' => 'Bhola', 'country_id' => 1, 'status' => 1],
            ['name' => 'Bogora', 'country_id' => 1, 'status' => 1],
            ['name' => 'Brahman Bariya', 'country_id' => 1, 'status' => 1],
            ['name' => 'Chandpur', 'country_id' => 1, 'status' => 1],
            ['name' => 'Chattagam', 'country_id' => 1, 'status' => 1],
            ['name' => 'Chittagong Division', 'country_id' => 1, 'status' => 1],
            ['name' => 'Chuadanga', 'country_id' => 1, 'status' => 1],
            ['name' => 'Dhaka', 'country_id' => 1, 'status' => 1],
            ['name' => 'Dinajpur', 'country_id' => 1, 'status' => 1],
            ['name' => 'Faridpur', 'country_id' => 1, 'status' => 1],
            ['name' => 'Feni', 'country_id' => 1, 'status' => 1],
            ['name' => 'Gaybanda', 'country_id' => 1, 'status' => 1],
            ['name' => 'Gazipur', 'country_id' => 1, 'status' => 1],
            ['name' => 'Gopalganj', 'country_id' => 1, 'status' => 1],
            ['name' => 'Habiganj', 'country_id' => 1, 'status' => 1],
            ['name' => 'Jaipur Hat', 'country_id' => 1, 'status' => 1],
            ['name' => 'Jamalpur', 'country_id' => 1, 'status' => 1],
            ['name' => 'Jessor', 'country_id' => 1, 'status' => 1],
            ['name' => 'Jhalakati', 'country_id' => 1, 'status' => 1],
            ['name' => 'Jhanaydah', 'country_id' => 1, 'status' => 1],
            ['name' => 'Khagrachhari', 'country_id' => 1, 'status' => 1],
            ['name' => 'Khulna', 'country_id' => 1, 'status' => 1],
            ['name' => 'Kishorganj', 'country_id' => 1, 'status' => 1],
            ['name' => 'Koks Bazar', 'country_id' => 1, 'status' => 1],
            ['name' => 'Komilla', 'country_id' => 1, 'status' => 1],
            ['name' => 'Kurigram', 'country_id' => 1, 'status' => 1],
            ['name' => 'Kushtiya', 'country_id' => 1, 'status' => 1],
            ['name' => 'Lakshmipur', 'country_id' => 1, 'status' => 1],
            ['name' => 'Lalmanir Hat', 'country_id' => 1, 'status' => 1],
            ['name' => 'Madaripur', 'country_id' => 1, 'status' => 1],
            ['name' => 'Magura', 'country_id' => 1, 'status' => 1],
            ['name' => 'Maimansingh', 'country_id' => 1, 'status' => 1],
            ['name' => 'Manikganj', 'country_id' => 1, 'status' => 1],
            ['name' => 'Maulvi Bazar', 'country_id' => 1, 'status' => 1],
            ['name' => 'Meherpur', 'country_id' => 1, 'status' => 1],
            ['name' => 'Munshiganj', 'country_id' => 1, 'status' => 1],
            ['name' => 'Naral', 'country_id' => 1, 'status' => 1],
            ['name' => 'Narayanganj', 'country_id' => 1, 'status' => 1],
            ['name' => 'Narsingdi', 'country_id' => 1, 'status' => 1],
            ['name' => 'Nator', 'country_id' => 1, 'status' => 1],
            ['name' => 'Naugaon', 'country_id' => 1, 'status' => 1],
            ['name' => 'Nawabganj', 'country_id' => 1, 'status' => 1],
            ['name' => 'Netrakona', 'country_id' => 1, 'status' => 1],
            ['name' => 'Nilphamari', 'country_id' => 1, 'status' => 1],
            ['name' => 'Noakhali', 'country_id' => 1, 'status' => 1],
            ['name' => 'Pabna', 'country_id' => 1, 'status' => 1],
            ['name' => 'Panchagarh', 'country_id' => 1, 'status' => 1],
            ['name' => 'Patuakhali', 'country_id' => 1, 'status' => 1],
            ['name' => 'Pirojpur', 'country_id' => 1, 'status' => 1],
            ['name' => 'Rajbari', 'country_id' => 1, 'status' => 1],
            ['name' => 'Rajshahi', 'country_id' => 1, 'status' => 1],
            ['name' => 'Rangamati', 'country_id' => 1, 'status' => 1],
            ['name' => 'Rangpur', 'country_id' => 1, 'status' => 1],
            ['name' => 'Satkhira', 'country_id' => 1, 'status' => 1],
            ['name' => 'Shariatpur', 'country_id' => 1, 'status' => 1],
            ['name' => 'Sherpur', 'country_id' => 1, 'status' => 1],
            ['name' => 'Silhat', 'country_id' => 1, 'status' => 1],
            ['name' => 'Sirajganj', 'country_id' => 1, 'status' => 1],
            ['name' => 'Sunamganj', 'country_id' => 1, 'status' => 1],
            ['name' => 'Tangayal', 'country_id' => 1, 'status' => 1],
            ['name' => 'Thakurgaon', 'country_id' => 1, 'status' => 1],
        ];

        \App\Models\State::insert($states);
    }
}
