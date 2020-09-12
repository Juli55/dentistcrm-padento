<?php

use Illuminate\Database\Seeder;

class LabLatLngFix extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $labs = \App\Lab::with('labmeta')->get();

        $failedCount = 0;

        foreach ($labs as $lab) {
            if($lab->labmeta) {
                try {
                    $lookup = getLocation($lab->labmeta->zip, $lab->labmeta->country_code);

                    $lab->lat = $lookup['latitude'];
                    $lab->lon = $lookup['longitude'];

                    $lab->save();
                } catch (Exception $exception) {
                    $failedCount++;
                }
            }
        }

        echo $failedCount . ' failed';
    }
}
