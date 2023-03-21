<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

class CalendarController extends Controller
{
    public function today()
    {
        $data = Carbon::now();

        return response([
            'year' => $data->year,
            'month' => $data->month,
            'day' => $data->day,
            'time' => $data->format('H:i:s'),
            'day of Year' => $data->dayOfYear,
        ], 200);
    }

    public function todayAuth()
    {
        $data = Carbon::now();

        return response([
            'year' => $data->year,
            'month' => $data->month,
            'day' => $data->day,
            'time' => $data->format('H:i:s'),
            'day of Year' => $data->dayOfYear,
        ], 200);
    }
}
