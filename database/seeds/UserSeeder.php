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
                'id_perusahaan' => 3,
                'name'=>'Hera Lestari',
                'email'=>'hera@gmail.com',
                'email_verified_at'=> now(),
                'password'=> bcrypt("hera"),
                'role' => 'client-staff',
                'remember_token'=>Str::random(10),
                'created_at'      => \Carbon\Carbon::now('Asia/Jakarta')
            ],
            [
                'id_perusahaan' => 3,
                'name'=>'Rivan santosa',
                'email'=>'rivan@gmail.com',
                'email_verified_at'=> now(),
                'password'=> bcrypt("rivan"),
                'role' => 'client-head',
                'remember_token'=>Str::random(10),
                'created_at'      => \Carbon\Carbon::now('Asia/Jakarta')
            ],
            [
                'id_perusahaan' => 2,
                'name'=>'Rifqi Zulfa',
                'email'=>'rifqi@gmail.com',
                'email_verified_at'=> now(),
                'password'=> bcrypt("rifqi"),
                'role'=>'client-staff',
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