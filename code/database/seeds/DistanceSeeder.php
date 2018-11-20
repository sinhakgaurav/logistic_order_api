<?php

use Illuminate\Database\Seeder;

class DistanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $locations = [
            ["start_latitude" => "28.704060", "start_longitude" => "77.102493", "end_latitude" => "28.535517", "end_longitude" => "77.391029", "distance" => 46732
            ],
            ["start_latitude" => "28.704060", "start_longitude" => "77.102493", "end_latitude" => "28.535517", "end_longitude" => "77.391044", "distance" => 912242
            ],
            ["start_latitude" => "28.704060", "start_longitude" => "77.102493", "end_latitude" => "28.535517", "end_longitude" => "77.391028", "distance" => 46731
            ]
        ];
        foreach ($locations as $disData) {
            App\Distance::create([
                'start_latitude' => $disData['start_latitude'],
                'start_longitude' => $disData['start_longitude'],
                'end_latitude' => $disData['end_latitude'],
                'end_longitude' => $disData['end_longitude'],
                'distance' => $disData['distance']
            ]);
        }

        $faker = Faker\Factory::create();
        for($i = 0; $i < 5; $i++) {
            $lat1 = $faker->latitude();
            $lat2 = $faker->latitude();
            $lon1 = $faker->longitude();
            $lon2 = $faker->longitude();
            $distance = $this->distance($lat1, $lon1, $lat2, $lon2);

            App\Distance::create([
                'start_latitude' => $lat1,
                'start_longitude' => $lon1,
                'end_latitude' => $lat2,
                'end_longitude' => $lon2,
                'distance' => $distance
            ]);
        }

        $distances = DB::table('distance')->pluck('distance', 'distance_id');
	
	App\Orders::create([
            'distance_id' => 1,
            'status' => 'TAKEN',
        ]);

        foreach ($distances as $disID => $distanceValue) {
            for ($i=0; $i < 5 ; $i++) {
                App\Orders::create([
                    'distance_id' => $disID,
                    'status' => 'UNASSIGN',
                ]);
            }
        }
    }

    public function distance($lat1, $lon1, $lat2, $lon2) {

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $distanceInMetre = $dist * 60 * 1.1515 * 1.609344 * 1000;

        return $distanceInMetre;
    }

}
