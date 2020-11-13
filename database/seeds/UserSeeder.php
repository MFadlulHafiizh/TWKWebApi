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
                'id_perusahaan' => 2,
                'name'=>'twk head',
                'email'=>'twkhead@gmail.com',
                'email_verified_at'=> now(),
                'password'=> bcrypt("twkhead"),
                'role'=>'twk-head',
                'remember_token'=>Str::random(10),
                'created_at'      => \Carbon\Carbon::now('Asia/Jakarta')
            ],
            [
                'id_perusahaan' => 1,
                'name'=>'client staff',
                'email'=>'clientstaff@gmail.com',
                'email_verified_at'=> now(),
                'password'=> bcrypt("clientstaff"),
                'role'=>'client-staff',
                'remember_token'=>Str::random(10),
                'created_at'      => \Carbon\Carbon::now('Asia/Jakarta')
            ],
            [
                'id_perusahaan' => 1,
                'name'=>'twk staff',
                'email'=>'twkstaff@gmail.com',
                'email_verified_at'=> now(),
                'password'=> bcrypt("twkstaff"),
                'role' => 'twk-staff',
                'remember_token'=>Str::random(10),
                'created_at'      => \Carbon\Carbon::now('Asia/Jakarta')
            ],
            [
                'id_perusahaan' => 2,
                'name'=>'Reza',
                'email'=>'reza@gmail.com',
                'email_verified_at'=> now(),
                'password'=> bcrypt("clienthead"),
                'role' => 'client-head',
                'remember_token'=>Str::random(10),
                'created_at'      => \Carbon\Carbon::now('Asia/Jakarta')
            ],
        ]);
    }
}