<?php

use App\Role;
use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
	public function run()
	{
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

		Role::truncate();

		DB::statement('SET FOREIGN_KEY_CHECKS=1;');

		Role::create([
			'name'         => 'admin',
			'display_name' => 'Administrator',
			'description'  => 'Dieser Nutzer ist ein Administrator.',
		]);

		Role::create([
			'name'         => 'user',
			'display_name' => 'Mitarbeiter',
			'description'  => 'Dieser Nutzer ist ein Padento Mitarbeiter.',
		]);

		Role::create([
			'name'         => 'lab',
			'display_name' => 'Labor',
			'description'  => 'Dieser Nutzer ist ein Dental-Labor.',
		]);

		Role::create([
			'name'         => 'dent',
			'display_name' => 'Zahnarzt',
			'description'  => 'Dieser Nutzer ist ein Zahnarzt.',
		]);

        Role::create([
            'name'         => 'crm-user',
            'display_name' => 'CRM User',
            'description'  => '',
        ]);

        Role::create([
            'name'         => 'dentist-crm-user',
            'display_name' => 'Zahnarzt CRM User',
            'description'  => '',
        ]);
	}
}
