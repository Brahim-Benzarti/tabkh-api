<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Unit;

class Units extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $a=new Unit();
        $a->unit="Gram";
        $a->abbreviation="g";
        $a->equivalents=[
            'once'=>1/29
        ];
        $a->save();
        $b=new Unit();
        $b->unit="Millileter";
        $b->abbreviation="ml";
        $b->equivalents= [
            'cup'=>1/240,
            'once'=>1/30,
            'spoon'=>1/15
        ];
        $b->save();
    }
}
