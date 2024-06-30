<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Intrested;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function inquiryView()
    {
        $inquiry = Intrested::leftjoin('properties','intresteds.property_id','=','properties.id')
            ->select('intresteds.*','properties.name as property_name')
            ->paginate(10);
        return view('admin.inquiry',compact('inquiry'));
    }
}
