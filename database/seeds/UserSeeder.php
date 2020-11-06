<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'name'=>'admin',
                'email'=>'admin@gmail.com',
                'email_verified_at'=> now(),
                'password'=> bcrypt("admin"),
                'role'=>'admin',
                'remember_token'=>Str::random(10),
                'created_at'      => \Carbon\Carbon::now('Asia/Jakarta')
            ],
            [
                'name'=>'client',
                'email'=>'client@gmail.com',
                'email_verified_at'=> now(),
                'password'=> bcrypt("client"),
                'role'=>'client',
                'remember_token'=>Str::random(10),
                'created_at'      => \Carbon\Carbon::now('Asia/Jakarta')
            ],
            [
                'name'=>'karyawan',
                'email'=>'karyawan@gmail.com',
                'email_verified_at'=> now(),
                'password'=> bcrypt("karyawan"),
                'role' => 'karyawan',
                'remember_token'=>Str::random(10),
                'created_at'      => \Carbon\Carbon::now('Asia/Jakarta')
            ],
            [
                'name'=>'Reza',
                'email'=>'reza@gmail.com',
                'email_verified_at'=> now(),
                'password'=> bcrypt("client"),
                'role' => 'client',
                'remember_token'=>Str::random(10),
                'created_at'      => \Carbon\Carbon::now('Asia/Jakarta')
            ],
        ]);
    }
}