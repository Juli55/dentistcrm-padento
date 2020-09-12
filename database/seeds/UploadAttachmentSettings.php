<?php

use App\Settings;
use Illuminate\Database\Seeder;

class UploadAttachmentSettings extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $name = 'Upload Attachment Period';

        Settings::where('name', $name)->delete();

        Settings::create([
            'name'        => $name,
            'value'       => '24',
            'description' => 'Upload attachments in hours',
        ]);
    }
}
