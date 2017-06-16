<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use Faker\Factory as Faker;

class UsersTableSeeder extends Seeder
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

	        DB::table('users')->insert([
	            'name' => $faker->name,
	            'email' => $faker->freeEmail,
                'phone' => $faker->phoneNumber,
                'mobile' => $faker->phoneNumber,
                'address' => $faker->address,
                'role' => 'TeamMember',
	            'created_at' => date('Y-m-d H:i:s', strtotime("-" . rand(1,10) . " week")),
	            'updated_at' => date('Y-m-d H:i:s'),
	        ]);
	        
        }
    }
}
