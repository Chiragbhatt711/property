<?php

namespace App\Exports;

use App\Models\PaymentHistory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class DepositeExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $payment_histories;

    public function __construct($payment_histories)
    {
        $this->payment_histories = $payment_histories;
    }

    public function view(): View
    {
        return view('export.admin_payment_histories', [
            'payment_histories' => $this->payment_histories,
        ]);
    }
}
