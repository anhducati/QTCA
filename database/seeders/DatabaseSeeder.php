<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
//        DB::table('users')->insert([
//            'name' => 'Nguyễn Khoa',
//            'email' => 'khoacntt2003@gmai.com',
//            'password' => Hash::make('khoa25866'),
//            'address' => 'Tiền Giang',
//            'image' => 'khoanguyen.png',
//            'phone' => '0336216546',
//        ]);

        $this->call([
            UserSeeder::class
        ]);

    }
}
