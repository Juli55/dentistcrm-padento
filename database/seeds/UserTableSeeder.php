<?php

use App\User;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		User::truncate();
		DB::table('role_user')->truncate();

		$user = User::create([
			'name'     => 'Armen',
			'email'    => 'armen@pinetco.com',
			'password' => bcrypt('testtest'),
		]);

		$user->roles()->attach('1');

		/*
		factory(App\User::class, 10)->create()->each(function($u) {
			$u->metas()->save(factory(App\UserMeta::class)->make());
			$u->roles()->attach('3');
		});
		*/
	}
}
