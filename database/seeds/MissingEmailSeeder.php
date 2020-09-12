<?php

use App\Email;
use Illuminate\Database\Seeder;

class MissingEmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Email::create([
            'name'              => 'NextDay',
            'short_description' => '[Labor] Next Day',
            'footer'            => '',
            'body'              => '',
            'description'       => '',
            'subject'           => '',
            'sort'              => 302,
        ]);

        Email::create([
            'name'              => 'LastDay',
            'short_description' => '[Labor] Last Day',
            'footer'            => '',
            'body'              => '',
            'description'       => '',
            'subject'           => '',
            'sort'              => 303,
        ]);
    }
}
