<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        for($i=1; $i <= 10; $i++) {
            $users = \App\Models\User::factory(1)->create();
        }

        for($i=1; $i <= 100; $i++) {
            $ads = \App\Models\Ad::factory(1)->make(['user_id' => null])->each(function ($ad) use ($users) {
                $ad->user_id = \App\Models\User::all()->random()->id;
                $ad->save();
            });
        }

        $faker = \Faker\Factory::create();
        $i = 0;
        while ($i != 25) {
            $i++;
            $request = [
                $faker->ipv4,
                $faker->userAgent
            ];
            \App\Jobs\ProcessVisits::dispatch($request)
                ->onQueue('parsing');
        }
    }
}
