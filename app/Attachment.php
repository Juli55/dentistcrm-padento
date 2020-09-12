<?php

namespace App;

use Carbon\Carbon;
use Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    public $incrementing = false;

    protected $fillable = [
        'patient_id',
        'name',
        'path',
        'title',
        'description',
        'dentist_contact_id'
    ];

    protected $appends = [
        'url',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }


    public function dentist()
    {
        return $this->belongsTo(DentistContact::class);
    }

    /**
     * Make new attachment object
     *
     * @param $name
     *
     * @return mixed
     */
    public static function named($name)
    {
        return (new static)->saveAs($name);
    }

    /**
     * Save file attributes
     *
     * @param $name
     *
     * @return $this
     */
    protected function saveAs($name)
    {
        $this->id = generateUUID();
        $this->name = $name;
        $this->path = $this->name;

        return $this;
    }

    /**
     * Move file to directory
     *
     * @param UploadedFile $file
     * @return $this
     */
    public function move(UploadedFile $file)
    {
        Storage::disk('attachments')->put($this->path, file_get_contents($file));

        return $this;
    }

    /**
     * Remove attachments
     */
    public function delete()
    {
        Storage::disk('attachments')->delete($this->path);

        return parent::delete();
    }

    /**
     * Upload attachment
     *
     * @param UploadedFile $file
     * @return mixed
     */
    public static function upload($patientId, UploadedFile $file, $attributes = [])
    {
        $patient = Patient::with('patientmeta')->find($patientId);

        $name = (new static)->guessFileName($patient, $file);

        $attachment = Attachment::named($name)->move($file);

        $attachment->patient_id = $patientId;

        $attachment->fill($attributes);

        $attachment->save();

        return $attachment;
    }


    public static function uploaddentist($dentistId, UploadedFile $file, $attributes = [])
    {
        $dentist = DentistContact::with('dentistmeta')->find($dentistId);

        $name = (new static)->guessFileNameDentist($dentist, $file);

        $attachment = Attachment::named($name)->move($file);

        $attachment->dentist_contact_id = $dentistId;

        $attachment->fill($attributes);

        $attachment->save();

        return $attachment;
    }

    public function getUrl()
    {
        return url(Storage::url($this->path));
    }

    public function getUrlAttribute()
    {
        return $this->getUrl();
    }

    public function guessFileName($patient, UploadedFile $file, $count = 1)
    {
        $name = sprintf("%s-%s-%s-%s.%s",
            $patient->id,
            $patient->patientmeta->name,
            Carbon::today()->format('Ymd'),
            $count,
            $file->getClientOriginalExtension()
        );

        if (Storage::disk('attachments')->exists($name)) {
            $count++;

            $name = $this->guessFileName($patient, $file, $count);
        };

        return $name;
    }



    public function guessFileNameDentist($dentist, UploadedFile $file, $count = 1)
    {
        $name = sprintf("%s-%s-%s-%s.%s",
            $dentist->id,
            $dentist->dentistmeta->name,
            Carbon::today()->format('Ymd'),
            $count,
            $file->getClientOriginalExtension()
        );

        if (Storage::disk('attachments')->exists($name)) {
            $count++;

            $name = $this->guessFileNameDentist($dentist, $file, $count);
        };

        return $name;
    }
}
