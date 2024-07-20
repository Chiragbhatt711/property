<?php

use App\Models\Property;
use App\Models\Intrested;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

if (!function_exists('totalPeoperty')) {
    function totalPeoperty()
    {
        $count = Property::count();

        return $count;
    }
}

if (!function_exists('TodayInquery')) {
    function TodayInquery()
    {
        $count = Intrested::whereDate('created_at', Carbon::today())->count();

        return $count;
    }
}
