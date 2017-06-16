<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use Faker\Factory as Faker;

class ArtsTableSeeder extends Seeder
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
    		
    		$target = '';
    		if($index % 2 == 0)
    			$target = '_blank';

	        DB::table('Arts')->insert([
	            'title' => $faker->text($maxNbChars = 30),
	            'link' => $faker->url,
	            'target' => $target,
	            'created_by' => 1,
	            'updated_by' => 1,
	            'created_at' => date('Y-m-d H:i:s', strtotime("-" . rand(1,10) . " week")),
	            'updated_at' => date('Y-m-d H:i:s'),
	        ]);
	        
        }
    }
}
