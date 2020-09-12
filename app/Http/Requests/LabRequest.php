<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class LabRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'lab.user.email'                 => 'required|email|unique:users,email',
            // 'lab.user.email_confirmation'    => 'required|email',
            'lab.user.password'              => 'required|min:6|confirmed',
            // 'lab.user.password_confirmation' => 'required|min:6',
            'lab.lab.name'                   => 'required',
            'lab.lab.labmeta.contact_person' => 'required',
            'lab.lab.labmeta.street'         => 'required',
            'lab.lab.labmeta.zip'            => 'required',
            'lab.lab.labmeta.city'           => 'required',
        ];
    }
}
