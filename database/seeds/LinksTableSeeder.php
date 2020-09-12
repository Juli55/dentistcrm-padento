<?php

use App\Link;
use Illuminate\Database\Seeder;

class LinksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Link::truncate();

        Link::create([
            'parent_id' => null,
            'title' => '✆ 05141 - 36 00 10',
            'url' => 'tel:+495141360010',
        ]);

        Link::create([
            'parent_id' => null,
            'title' => 'ÜBER',
            'url' => '/wissen/ueber-padento',
        ]);

        Link::create([
            'parent_id' => null,
            'title' => 'BLOG',
            'url' => '/wissen',
        ]);

        Link::create([
            'parent_id' => null,
            'title' => 'E-BOOK',
            'url' => 'http://padento.lpages.co/blog/',
        ]);
    }
}
