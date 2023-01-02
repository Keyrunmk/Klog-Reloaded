<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $world = new Location();
        $world->country_name = "World";
        $world->save();

        $nepal = new Location();
        $nepal->country_name = "Nepal";
        $nepal->save();
    }
}
