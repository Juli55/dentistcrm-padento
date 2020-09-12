<?php

namespace App\Padento;

use App\ZipCode;

class Helper
{
    # translate markdown to email #
    static function markdown($text, $lab = null, $patient = null, $date = null, $token = 'Testtocken')
    {
        $text = \Markdown::text($text);

        if (!$lab) {
            $lab = $patient ? $patient->lab : null;
        }

        if ($patient != null && is_object($patient)) {
            if ($patient->patientmeta) {
                $name = $patient->patientmeta->name;
                $patientEmail = $patient->patientmeta->email;
                $salutation = $patient->patientmeta->salutation;
                if ($date) {
                    $date = new \Carbon\Carbon($date->date);
                    $date = $date->formatLocalized('%d.%m.%Y %H:%M') . ' Uhr';
                } else if ($patient->nextDate->count() > 0) {
                    setlocale(LC_TIME, 'German');
                    $date = new \Carbon\Carbon($patient->nextDate->first()->date);
                    $date = $date->formatLocalized('%d.%m.%Y %H:%M') . ' Uhr';
                } else {
                    $date = '';
                }
                $kontakttel = $patient->patientmeta->tel;
                $kontaktmobil = $patient->patientmeta->mobile;
                $patientid = $patient->id;
            } else {
                $name         = '';
                $patientEmail = '';
                $salutation   = '';
                $date         = '';
                $kontakttel   = '';
                $kontaktmobil = '';
                $patientid    = '';
            }
        } else {
            $name         = '';
            $patientEmail = '';
            $salutation   = '';
            $date         = '';
            $kontakttel   = '';
            $kontaktmobil = '';
            $patientid    = '';
        }

        if ($lab) {
            $labor           = $lab->slug;
            $laborlink       = \URL::to('/') . "/labor/" . $labor;
            $ansprechpartner = $lab->labmeta->contact_person;
            $laborname       = $lab->name;
            $laborort        = $lab->labmeta->city;
            $street          = $lab->labmeta->street;
            $city            = $lab->labmeta->city;
            $zip             = $lab->labmeta->zip;
            $mail            = $lab->user->email;
            $tel             = $lab->labmeta->tel;
        } else {
            $labor           = '';
            $laborname       = '';
            $laborlink       = '';
            $ansprechpartner = '';
            $laborort        = '';
            $street          = '';
            $city            = '';
            $zip             = '';
            $mail            = '';
            $tel             = '';
        }

        $padentotel   = $radius_start = \App\Settings::where('name', '=', 'Padento Telefonnummer')->first()->value;
        $padentomobil = $radius_start = \App\Settings::where('name', '=', 'Padento Handynummer')->first()->value;

        $replaces = [
            '[name]'                  => $name,
            '[anrede]'                => $salutation,
            '[begrüßung]'             => ($salutation == 'Herr' ? 'Sehr geehrter Herr' : 'Sehr geehrte Frau'),
            '[termin]'                => $date,
            '[ansprechpartner]'       => $ansprechpartner,
            '[laborname]'             => '<a href="'.$laborlink.'">'.$laborname.'</a>',
            '[labortel]'              => '<a href="tel:'.$tel.'">'.$tel.'</a>',
            '[laborort]'              => $laborort,
            '[kontakttel]'            => $kontakttel,
            '[kontaktmobil]'          => $kontaktmobil,
            '[padentoblog]'           => 'http://padento.de/wissen',
            '[kontaktdatenkontrolle]' => '', // TODO
            '[kontaktdaten]'          => $laborname . '<br>' . $street . '<br>' . $zip . ' ' . $city . '<br><a href="mailto:' . $mail . '">' . $mail . '</a><br><a href="tel:' . $tel . '">' . $tel . '</a>',
            '[padentotel]'            => '<a href="tel:' . $padentotel . '">' . $padentotel . '</a>',
            '[padentomobil]'          => '<a href="tel:' . $padentomobil . '">' . $padentomobil . '</a>',
            '[grün]'                  => '<strong style="color: #56A930; font-weight: bold;">',
            '[/grün]'                 => '</strong>',
            '[bestätigungslink]'      => '<table class="large-button" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; overflow: hidden; padding: 0;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: center; color: #ffffff; font-family: "Helvetica", "Arial", sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; display: block; width: auto !important; -moz-border-radius: 15px; -webkit-border-radius: 15px; border-radius: 15px; background: #56A930; margin: 0;border: 0 solid #2284a1;" align="center" bgcolor="#56A930" valign="top"><a href="' . \URL::to('/') . '/mailtoken/' . htmlentities($token) . '" style=" padding: 21px 0 18px; width:100%; line-height:19px;display:inline-block; color: #ffffff; text-decoration: none; font-weight: normal; font-family: Helvetica, Arial, sans-serif; font-size: 24px;">Meine Anfrage bestätigen</a></td></tr></table>',
            '[uploadlink]'            => '<table class="large-button" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; overflow: hidden; padding: 0;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: center; color: #ffffff; font-family: "Helvetica", "Arial", sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; display: block; width: auto !important; -moz-border-radius: 15px; -webkit-border-radius: 15px; border-radius: 15px; background: #56A930; margin: 0;border: 0 solid #2284a1;" align="center" bgcolor="#56A930" valign="top"><a href="' . url("attachments/upload/$token") . '" style=" padding: 21px 0 18px; width:100%; line-height:19px;display:inline-block; color: #ffffff; text-decoration: none; font-weight: normal; font-family: Helvetica, Arial, sans-serif; font-size: 24px;">Dokumente hochladen</a></td></tr></table>',
            '[laborlink]'             => '<table class="large-button" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; overflow: hidden; padding: 0;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: center; color: #ffffff; font-family: "Helvetica", "Arial", sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; display: block; width: auto !important; -moz-border-radius: 15px; -webkit-border-radius: 15px; border-radius: 15px; background: #56A930; margin: 0;border: 0 solid #2284a1;" align="center" bgcolor="#56A930" valign="top"><a href="' . $laborlink . '" style=" padding: 21px 0 18px; width:100%; line-height:19px;display:inline-block; color: #ffffff; text-decoration: none; font-weight: normal; font-family: Helvetica, Arial, sans-serif; font-size: 24px;">Mein Dentallabor ansehen</a></td></tr></table>',
            '<h1>'                    => '<h1 style="Margin:0;Margin-bottom:10px;color:#555555 !important;font-family:Helvetica,Arial,sans-serif;font-size:34px;font-weight:400;line-height:1.25;margin:0;margin-bottom:10px;padding:0;text-align:left;word-wrap:normal">',
            '[padentobackendlink]'    => '<table class="large-button" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; overflow: hidden; padding: 0;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: center; color: #ffffff; font-family: "Helvetica", "Arial", sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; display: block; width: auto !important; -moz-border-radius: 15px; -webkit-border-radius: 15px; border-radius: 15px; background: #56A930; margin: 0;border: 0 solid #2284a1;" align="center" bgcolor="#56A930" valign="top"><a href="' . \URL::to('/') . '/app" style=" padding: 21px 0 18px; width:100%; line-height:19px;display:inline-block; color: #ffffff; text-decoration: none; font-weight: normal; font-family: Helvetica, Arial, sans-serif; font-size: 24px;">Ein neuer Kontakt wartet auf Sie</a></td></tr></table>',
            '[kontaktlink]'           => '<table class="large-button" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; overflow: hidden; padding: 0;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: center; color: #ffffff; font-family: "Helvetica", "Arial", sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; display: block; width: auto !important; -moz-border-radius: 15px; -webkit-border-radius: 15px; border-radius: 15px; background: #56A930; margin: 0;border: 0 solid #2284a1;" align="center" bgcolor="#56A930" valign="top"><a href="' . url("app/kontakt/$patientid") . '" style=" padding: 21px 0 18px; width:100%; line-height:19px;display:inline-block; color: #ffffff; text-decoration: none; font-weight: normal; font-family: Helvetica, Arial, sans-serif; font-size: 24px;">Kontakt</a></td></tr></table>',
            '[kontaktdeletelink]'     => '<table class="large-button" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; overflow: hidden; padding: 0;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: center; color: #ffffff; font-family: "Helvetica", "Arial", sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; display: block; width: auto !important; -moz-border-radius: 15px; -webkit-border-radius: 15px; border-radius: 15px; background: #56A930; margin: 0;border: 0 solid #2284a1;" align="center" bgcolor="#56A930" valign="top"><a href="' . route('account.remove.confirm', ['id' => $patientid, 'email' => $patientEmail]) . '" style=" padding: 21px 0 18px; width:100%; line-height:19px;display:inline-block; color: #ffffff; text-decoration: none; font-weight: normal; font-family: Helvetica, Arial, sans-serif; font-size: 24px;">Ja, ich möchte meine Daten löschen</a></td></tr></table>',
            '[dsgvoacceptlink]'       => '<table class="large-button" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; overflow: hidden; padding: 0;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: center; color: #ffffff; font-family: "Helvetica", "Arial", sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; display: block; width: auto !important; -moz-border-radius: 15px; -webkit-border-radius: 15px; border-radius: 15px; background: #56A930; margin: 0;border: 0 solid #2284a1;" align="center" bgcolor="#56A930" valign="top"><a href="' . route('dsgvo.accept', ['id' => $patientid, 'email' => $patientEmail]) . '" style=" padding: 21px 0 18px; width:100%; line-height:19px;display:inline-block; color: #ffffff; text-decoration: none; font-weight: normal; font-family: Helvetica, Arial, sans-serif; font-size: 24px;">Meine Daten nicht löschen</a></td></tr></table>',
        ];

        foreach ($replaces as $replace_name => $replace_value) {
            if (strpos($text, $replace_name) !== false) {
                $text = str_replace($replace_name, $replace_value, $text);
            }
        }

        return $text;
    }

    static function markdowndentist($text, $lab = null, $dentist = null, $date = null, $token = 'Testtocken')
    {
        $text = \Markdown::text($text);

        if (!$lab) {
            $lab = $dentist ? $dentist->lab : null;
        }

        if ($dentist != null) {
            $name       = $dentist->dentistmeta->name;
            $salutation = $dentist->dentistmeta->salutation;
            if ($dentist->nextDate->count() > 0) {
                setlocale(LC_TIME, 'German');
                $date = new \Carbon\Carbon($dentist->nextDate->first()->date);
                $date = $date->formatLocalized('%d.%m.%Y %H:%M') . ' Uhr';
            } else {
                $date = '';
            }
            $kontakttel   = $dentist->dentistmeta->tel;
            $kontaktmobil = $dentist->dentistmeta->mobile;
            $dentistid    = $dentist->id;
        } else {
            $name         = '';
            $salutation   = '';
            $date         = '';
            $kontakttel   = '';
            $kontaktmobil = '';
            $dentistid    = '';
        }

        if ($lab) {
            $labor           = $lab->slug;
            $laborlink       = \URL::to('/') . "/labor/" . $labor;
            $ansprechpartner = $lab->labmeta->contact_person;
            $laborname       = $lab->name;
            $laborort        = $lab->labmeta->city;
            $street          = $lab->labmeta->street;
            $city            = $lab->labmeta->city;
            $zip             = $lab->labmeta->zip;
            $mail            = $lab->user->email;
            $tel             = $lab->labmeta->tel;
        } else {
            $labor           = '';
            $laborname       = '';
            $laborlink       = '';
            $ansprechpartner = '';
            $laborort        = '';
            $street          = '';
            $city            = '';
            $zip             = '';
            $mail            = '';
            $tel             = '';
        }

        $padentotel   = $radius_start = \App\Settings::where('name', '=', 'Padento Telefonnummer')->first()->value;
        $padentomobil = $radius_start = \App\Settings::where('name', '=', 'Padento Handynummer')->first()->value;

        $replaces = [
            '[name]'                  => $name,
            '[anrede]'                => $salutation,
            '[begrüßung]'             => ($salutation == 'Herr' ? 'Sehr geehrter Herr' : 'Sehr geehrte Frau'),
            '[termin]'                => $date,
            '[ansprechpartner]'       =>  '<a href="$laborlink">'.$ansprechpartner.'</a>',
            '[laborname]'             => '<a href="$laborlink">'.$laborname.'</a>',
            '[laborort]'              => $laborort,
            '[kontakttel]'            => '<a href="tel:'.$kontakttel.'">'.$kontakttel.'</a>',
            '[kontaktmobil]'          => $kontaktmobil,
            '[padentoblog]'           => 'http://padento.de/wissen',
            '[kontaktdatenkontrolle]' => '', // TODO
            '[kontaktdaten]'          => $laborname . '<br>' . $street . '<br>' . $zip . ' ' . $city . '<br><a href="mailto:' . $mail . '">' . $mail . '</a><br><a href="tel:' . $tel . '">' . $tel . '</a>',
            '[padentotel]'            => '<a href="tel:' . $padentotel . '">' . $padentotel . '</a>',
            '[padentomobil]'          => '<a href="tel:' . $padentomobil . '">' . $padentomobil . '</a>',
            '[grün]'                  => '<strong style="color: #56A930; font-weight: bold;">',
            '[/grün]'                 => '</strong>',
            '[bestätigungslink]'      => '<table class="large-button" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; overflow: hidden; padding: 0;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: center; color: #ffffff; font-family: "Helvetica", "Arial", sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; display: block; width: auto !important; -moz-border-radius: 15px; -webkit-border-radius: 15px; border-radius: 15px; background: #56A930; margin: 0;border: 0 solid #2284a1;" align="center" bgcolor="#56A930" valign="top"><a href="' . \URL::to('/') . '/mailtoken/' . htmlentities($token) . '" style=" padding: 21px 0 18px; width:100%; line-height:19px;display:inline-block; color: #ffffff; text-decoration: none; font-weight: normal; font-family: Helvetica, Arial, sans-serif; font-size: 24px;">Meine Anfrage bestätigen</a></td></tr></table>',
            '[uploadlink]'            => '<table class="large-button" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; overflow: hidden; padding: 0;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: center; color: #ffffff; font-family: "Helvetica", "Arial", sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; display: block; width: auto !important; -moz-border-radius: 15px; -webkit-border-radius: 15px; border-radius: 15px; background: #56A930; margin: 0;border: 0 solid #2284a1;" align="center" bgcolor="#56A930" valign="top"><a href="' . url("attachments/upload/$token") . '" style=" padding: 21px 0 18px; width:100%; line-height:19px;display:inline-block; color: #ffffff; text-decoration: none; font-weight: normal; font-family: Helvetica, Arial, sans-serif; font-size: 24px;">Dokumente hochladen</a></td></tr></table>',
            '[laborlink]'             => '<table class="large-button" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; overflow: hidden; padding: 0;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: center; color: #ffffff; font-family: "Helvetica", "Arial", sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; display: block; width: auto !important; -moz-border-radius: 15px; -webkit-border-radius: 15px; border-radius: 15px; background: #56A930; margin: 0;border: 0 solid #2284a1;" align="center" bgcolor="#56A930" valign="top"><a href="' . $laborlink . '" style=" padding: 21px 0 18px; width:100%; line-height:19px;display:inline-block; color: #ffffff; text-decoration: none; font-weight: normal; font-family: Helvetica, Arial, sans-serif; font-size: 24px;">Mein Dentallabor ansehen</a></td></tr></table>',
            '<h1>'                    => '<h1 style="Margin:0;Margin-bottom:10px;color:#555555 !important;font-family:Helvetica,Arial,sans-serif;font-size:34px;font-weight:400;line-height:1.25;margin:0;margin-bottom:10px;padding:0;text-align:left;word-wrap:normal">',
            '[padentobackendlink]'    => '<table class="large-button" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; overflow: hidden; padding: 0;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: center; color: #ffffff; font-family: "Helvetica", "Arial", sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; display: block; width: auto !important; -moz-border-radius: 15px; -webkit-border-radius: 15px; border-radius: 15px; background: #56A930; margin: 0;border: 0 solid #2284a1;" align="center" bgcolor="#56A930" valign="top"><a href="' . \URL::to('/') . '/app" style=" padding: 21px 0 18px; width:100%; line-height:19px;display:inline-block; color: #ffffff; text-decoration: none; font-weight: normal; font-family: Helvetica, Arial, sans-serif; font-size: 24px;">Ein neuer Kontakt wartet auf Sie</a></td></tr></table>',
            '[kontaktlink]'           => '<table class="large-button" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; overflow: hidden; padding: 0;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: center; color: #ffffff; font-family: "Helvetica", "Arial", sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; display: block; width: auto !important; -moz-border-radius: 15px; -webkit-border-radius: 15px; border-radius: 15px; background: #56A930; margin: 0;border: 0 solid #2284a1;" align="center" bgcolor="#56A930" valign="top"><a href="' . url("app/kontakt/$dentistid") . '" style=" padding: 21px 0 18px; width:100%; line-height:19px;display:inline-block; color: #ffffff; text-decoration: none; font-weight: normal; font-family: Helvetica, Arial, sans-serif; font-size: 24px;">Kontakt</a></td></tr></table>',
        ];

        foreach ($replaces as $replace_name => $replace_value) {
            if (strpos($text, $replace_name) !== false) {
                $text = str_replace($replace_name, $replace_value, $text);
            }
        }

        return $text;
    }

    # check if zip is in plz.txt (i.e. existing) #
    static function zipIsInList($plz, $lang = 'de')
    {
        $file = app_path() . "/../plz.txt";
        if ($lang != 'de') {
            if ($lang == 'at') {
                $file = app_path() . "/../plz." . $lang . ".txt";
            }
        }

        $plzs = file($file);
        if (in_array((int)$plz, $plzs)) {
            return true; # plz is in list
        } else {
            return false; # plz is not in list
        }
    }
}