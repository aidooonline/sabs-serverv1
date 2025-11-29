<?php

use App\LeadTemperature;
use Illuminate\Database\Seeder;

class LeadTemperatureTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LeadTemperature::create(
            [
                'name' => 'Cold',
                'created_by' => 0 
            ],
            [
                'name' => 'Warm',
                'created_by' => 0 
            ],
            [
                'name' => 'Hot',
                'created_by' => 0 
            ]
        );
    }
}
