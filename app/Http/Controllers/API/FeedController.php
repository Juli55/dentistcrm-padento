<?php

namespace App\Http\Controllers\API;

use App\Services\Pagination;
use Auth;
use App\Lab;
use DateTime;
use App\Date;
use FeedReader;
use App\Patient;
use Carbon\Carbon;
use App\EmployeeDate;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class FeedController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (Auth::check()) {
            $data            = [];
            $feed            = FeedReader::read('http://padento.de/wissen');
            $data['padento'] = [
                'title'     => $feed->get_title(),
                'permalink' => $feed->get_permalink(),
                'items'     => [],
            ];
            foreach ($feed->get_items(0, 5) as $item) {
                $data['padento']['items'][] = [
                    'title'     => $item->get_title(),
                    'permalink' => $item->get_permalink(),
                    // 'description' => $item->get_description(),
                ];
            }
            $feed                 = FeedReader::read('http://www.rainerehrich.de/blog/rss.xml');
            $data['rainerehrich'] = [
                'title'     => $feed->get_title(),
                'permalink' => $feed->get_permalink(),
                'items'     => [],
            ];
            foreach ($feed->get_items(0, 5) as $item) {
                $data['rainerehrich']['items'][] = [
                    'title'     => $item->get_title(),
                    'permalink' => $item->get_permalink(),
                    // 'description' => $item->get_description(),
                ];
            }

            return response()->json($data);
        }
    }
}












































































