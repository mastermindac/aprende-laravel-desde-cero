<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        $testUser = User::factory()->hasContacts(30)->createOne(['email' => 'test@test.com']);
        $users = User::factory(3)->hasContacts(5)->create()->each(
            fn ($user) => $user->contacts->first()->sharedWithUsers()->attach($testUser->id)
        );

        $testUser->contacts->first()->sharedWithUsers()->attach($users->pluck('id'));
    }
}
