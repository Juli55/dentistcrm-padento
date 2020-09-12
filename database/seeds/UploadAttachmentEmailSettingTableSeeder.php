<?php

use App\Email;
use Illuminate\Database\Seeder;

class UploadAttachmentEmailSetting extends Seeder
{
    public function run()
    {
        $name = 'UploadAttachments';

        Email::where('name', $name)->delete();

        Email::create([
            'name'              => $name,
            'footer'            => '',
            'body'              => 'Bitte laden Sie hier Ihre Dokumente, wie den Heil- und Kostenplan hoch:<br>[uploadlink]',
            'short_description' => 'E-Mail für Dokumente',
            'description'       => 'Diese E-Mail wird ca. 1 Tag nachdem der Kontakt sich bei Padento eingetragen hat versendet, wenn noch keine Dokumente hochgeladen worden sind oder im Backend der benutzer aufgefordert wird Dokumente hochzuladen.',
        ]);
    }
}
