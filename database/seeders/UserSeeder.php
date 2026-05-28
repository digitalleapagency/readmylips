<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
	        'name' => 'Demo User',
	        'email' => 'demo@mail.com', 
	        'password' => Hash::make('12345678')
	    ]);
    }
}
