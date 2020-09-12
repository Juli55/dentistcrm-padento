<?php

namespace App\Http\Controllers;

use App\DentistContact;
use App\Patient;
use Storage;
use App\Attachment;
use App\Http\Requests;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{
    public function index($token)
    {
        return view('attachments.index', compact('token'));
    }

    public function store(Request $request, $token)
    {
        $this->validate($request, [
            'attachment' => 'required|mimes:jpeg,jpg,png,pdf'
        ]);

        $file = $request->file('attachment');

        $patient = Patient::where('token', $token)->firstOrFail();

        $attachment = Attachment::upload($patient->id, $file);

        session()->flash('success', 'Vielen Dank für Ihr Dokument. Sie können uns gerne weitere Dokumente zukommen lassen.');

        activity()->causedBy($patient)->performedOn($patient)->withProperties(['attachment' => $attachment])->log('attachment_uploaded');

        return back();
    }



    public function indexdentist($token)
    {
        return view('attachments.index', compact('token'));
    }

    public function storedentist(Request $request, $token)
    {
        $this->validate($request, [
            'attachment' => 'required|mimes:jpeg,jpg,png,pdf'
        ]);

        $file = $request->file('attachment');

        $dentist = DentistContact::where('token', $token)->firstOrFail();

        $attachment = Attachment::uploaddentist($dentist->id, $file);

        session()->flash('success', 'Vielen Dank für Ihr Dokument. Sie können uns gerne weitere Dokumente zukommen lassen.');

        activity()->causedBy($dentist)->performedOn($dentist)->withProperties(['attachment' => $attachment])->log('attachment_uploaded');

        return back();
    }

    public function show(Attachment $attachment)
    {
        return response()->download(
            sprintf("%s/%s", storage_path('app/attachments'), $attachment->path),
            $attachment->name
        );
    }

    public function destroy(Attachment $attachment)
    {
        $attachment->delete();

        return response("success");
    }
}
