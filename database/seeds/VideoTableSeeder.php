<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use Faker\Factory as Faker;

class VideoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
    	foreach (range(1,10) as $index) {
    		
			DB::table('videos')->insert([
	            'title' => $faker->text($maxNbChars = 30),
	            'link' => $faker->url,
	            'display_order' => $index,
	            'created_by' => 1,
	            'updated_by' => 1,
	            'created_at' => date('Y-m-d H:i:s', strtotime("-" . rand(1,10) . " week")),
	            'updated_at' => date('Y-m-d H:i:s'),
	        ]);
	        
        }
    }
}
