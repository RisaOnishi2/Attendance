<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 管理者ユーザー
        User::create([
            'name' => '管理者',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'is_admin' => true,
        ]);

        // 一般ユーザー
        User::create([
            'name' => '山田 太郎',
            'email' => 'user1@example.com',
            'password' => Hash::make('password1'),
            'is_admin' => false,
        ]);

        User::create([
            'name' => '西 怜奈',
            'email' => 'user2@example.com',
            'password' => Hash::make('password2'),
            'is_admin' => false,
        ]);

        User::create([
            'name' => '増田 一世',
            'email' => 'user3@example.com',
            'password' => Hash::make('password3'),
            'is_admin' => false,
        ]);

        User::create([
            'name' => '山本 敬吉',
            'email' => 'user4@example.com',
            'password' => Hash::make('password4'),
            'is_admin' => false,
        ]);

        User::create([
            'name' => '秋田 朋美',
            'email' => 'user5@example.com',
            'password' => Hash::make('password5'),
            'is_admin' => false,
        ]);

        User::create([
            'name' => '中西 敦夫',
            'email' => 'user6@example.com',
            'password' => Hash::make('password6'),
            'is_admin' => false,
        ]);
    }
}
