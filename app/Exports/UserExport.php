<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\RemembersChunkOffset;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Models\User;
use DB;
// WithChunkReading
class UserExport implements FromQuery, WithHeadings, ShouldAutoSize, WithMapping
{
	use Exportable;
    use RemembersChunkOffset;
    /**
    * @return \Illuminate\Support\Collection
    */

    private $search;
    private $PaymentHistory;
    private $customrate;
    public function __construct( $search)
    {
        $this->search = $search;
    }

    // public function collection()
    // {
    //     return collect($this->users);
    // }
    public function query()
    {
        $u = User::query()
        ->where('user_type',0)
        ->select('users.*')
        ->groupBy('users.id')
        ->where('users.id', 'like', '%' .$this->search . '%')
        ->orWhere('users.username', 'like', '%' .$this->search . '%')
        ->orWhere('users.email', 'like', '%' .$this->search . '%')
        ->orWhere('users.phone','like', '%' .$this->search . '%')
        ->orWhere('users.created_at','like', '%' .date('Y-m-d',strtotime($this->search)). '%');

        return $u;
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->username,
            $user->email,
            $user->phone,
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'User Name',
            'E-mail',
            'Phone',
        ];
    }

    // public function chunkSize(): int
    // {
    //     return 1000;
    // }

}

