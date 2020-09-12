<?php

namespace App\Http\Controllers\API;

use Auth;
use Excel;
use App\Patient;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PatientController extends Controller
{
    public function export(Request $request)
    {
        if (Auth::user()) {
            $today = date('Ymd');
            $filename = 'Patendo_Kontakte_' . $today;
            $page = $request->get('page');
            $all_patients = Patient::with('patientmeta')->paginate(400);
            $patients = $all_patients->items();
            $merged_patients = [];
            $excludes = [
                'id',
                'created_at',
                'updated_at',
                'patient_id',
            ];

            $folder = base_path('storage/exports/' . $today);

            if (!is_dir($folder)) {
                mkdir($folder, 0755);
            }

            $combined_folder = base_path('storage/exports/combined');

            if (!is_dir($combined_folder)) {
                mkdir($combined_folder, 0755);
            }

            foreach ($patients as $patient) {
                $temp_array = [];

                $patient = $patient->toArray();

                foreach ($patient as $key => $value) {
                    if ($key != 'patientmeta') {
                        $temp_array[$key] = $value;
                    }

                    if ($key == 'patientmeta' && $value != null) {
                        foreach ($value as $pm_key => $pm_value) {
                            if (!in_array($pm_key, $excludes)) {
                                $temp_array[$pm_key] = $pm_value;
                            }
                        }
                    }
                }
                array_push($merged_patients, $temp_array);
            }

            Excel::create($today . '/' . $filename . '_' . $page, function ($excel) use ($merged_patients) {
                $excel->sheet('Kontakte', function ($sheet) use ($merged_patients) {
                    $sheet->fromArray($merged_patients);
                });
            })->save('csv');

            if ($page == $all_patients->lastPage()) {
                $my_file = $filename . '_combined';
                $my_file_ext = $my_file . '.csv';

                $combined = fopen(base_path('storage/exports/combined/' . $my_file_ext), 'w') or die('Cannot open file:  ' . $my_file); //implicitly creates file

                if ($handle = opendir(base_path('storage/exports/' . $today))) {
                    $i = 0;
                    while (false !== ($entry = readdir($handle))) {
                        if ($entry != "." && $entry != ".." && !is_dir($entry)) {
                            $file2 = file_get_contents(base_path('storage/exports/' . $today . '/') . $entry);
                            if ($i != 0) {
                                $file2 = preg_replace('/^.+\r\n/', '', $file2);
                            }
                            fwrite($combined, $file2);
                            $i++;
                        }
                    }

                    closedir($handle);
                }

                return [
                    'filename' => $my_file,
                ];
            }

            return $all_patients;
        }
    }

    public function downloadExport($filename)
    {
        if (Auth::user()) {
            Excel::load('storage/exports/combined/' . $filename . '.csv', function ($excel) {
            })->export('csv');
        }
    }
}
