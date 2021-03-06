<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(SliderTableSeeder::class);
        $this->call(ArtsTableSeeder::class);
        $this->call(AlbumsTableSeeder::class);
        $this->call(VideoTableSeeder::class);
    }
}
