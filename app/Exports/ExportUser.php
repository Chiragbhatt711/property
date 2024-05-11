<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ExportUser implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $users;

    public function __construct($users,$PaymentHistory,$customrate)
    {
        $this->users = $users;
    }

    public function view(): View
    {
        return view('export.user_export', [
            'users' => $this->users,
        ]);
    }
}
