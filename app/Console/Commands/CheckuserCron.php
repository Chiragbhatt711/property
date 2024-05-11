<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ripple;
use App\Models\RippleMember;
use App\Models\RippleTurn;
use DB;
use Illuminate\Support\Str;

class CheckuserCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checkuser:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Log::info("Ripple turn cron job start!");
        $ripple_ids = Ripple::select('id', 'frequency', 'day')->get();
        if ($ripple_ids) 
        {
            // $currentDate = date('Y-m-d',strtotime('2023-03-21'));
            $currentDate = date('Y-m-d');
            // dd($currentDate);
            foreach ($ripple_ids as $ripple_id) 
            {
                // $date = now();
                // $today_name = $date->format('l');

                $ripple = $ripple_id->id;
                $frequency = $ripple_id->frequency;
                // $frequency = Str::lower($frequency);
                $currentTurnMember = RippleTurn::where(['ripple_id' => $ripple,'current_user' => 1])->select('id','created_at')->first();
                
                $currentMemberTurnDate = null;
                $day_count = 1; 
                $selected_day = $ripple_id->day;
                switch (strtolower($frequency)) 
                {
                    case 'every 3 weeks':
                        $day_count = 21;
                        break;
                    case 'bi-weekly':
                        $day_count = 14;
                        break;
                    case 'weekly':
                        $day_count = 7;
                        break;
                    default:
                        $day_count = 1;
                        break;
                }
                if($currentTurnMember)
                {
                    $currentTurnDate = date('Y-m-d', strtotime($currentTurnMember->created_at));
                    $currentMemberTurnDate = date('Y-m-d', strtotime($currentTurnDate. ' + '.$day_count.' day'));
                }
                else
                {
                    if(strtolower($selected_day) == strtolower(date('l')))
                    {
                        $currentMemberTurnDate = date('Y-m-d');
                    }
                }
                // dd($currentMemberTurnDate);
                
                // $date_check = date("Y-m-d",strtolower(''))
                // dd($date_check);
                // if ($frequency == 'by-three weekly') {
                //     $selected_days = $ripple_id->day;
                //     $days = explode(',', $selected_days);
                // } elseif ($frequency == 'by-monthly') {
                //     $selected_days = $ripple_id->day;
                //     $days = explode(',', $selected_days);
                // } else {
                //     $selected_days = $ripple_id->day;
                //     $days = explode(',', $selected_days);
                // }
                // $today_name = Str::lower($today_name);

                // if ($days && in_array($today_name, $days))
                if ($currentMemberTurnDate && $currentDate == $currentMemberTurnDate) 
                {
                    $ripple_members = DB::table('ripple_members')->where(['ripple_id' => $ripple, "role" => 'member'])->get()->keyBy('member_id')->toArray();
                    // dd($ripple_members);
                    $ripple_members = json_decode(json_encode($ripple_members), true);

                    $total = count($ripple_members);

                    $memberTurnCount = RippleTurn::where('ripple_id',$ripple)->get()->count();
                    if ($memberTurnCount == $total) 
                    {
                        RippleTurn::where('ripple_id', '=', $ripple)->delete();
                    }

                    $rand_number = rand(0, $total - 1);
                    $final_key = 0;
                    $check_key = 0;
                    foreach ($ripple_members as $key => $ripple_member) 
                    {
                        if ($check_key == $rand_number) 
                        {
                            $final_key = $key;
                        }
                        $check_key++;
                    }
                    // dd($final_key);
                    $total_data_ripple_turn = RippleTurn::where(['ripple_id' => $ripple])->get();
                    // if ($total_data_ripple_turn) 
                    // {
                    //     $total_get_data = count($total_data_ripple_turn);

                        // if ($total_get_data == $total) 
                        // {
                        //     RippleTurn::where('ripple_id', '=', $ripple)->delete();
                        // }
                    // }

                    $check_ripple_turn_data = RippleTurn::where(['ripple_id' => $ripple, 'current_user' => 1])->get();

                    RippleTurn::where(['ripple_id' => $ripple, 'current_user' => 1])
                        ->update(['current_user' => 0]);

                    if ($check_ripple_turn_data->isEmpty()) 
                    {
                        RippleTurn::create([
                            'ripple_id' => $ripple,
                            'user_id' => $ripple_members[$final_key]['member_id'],
                            'current_user' => 1,
                        ]);
                    } 
                    else 
                    {
                        foreach ($total_data_ripple_turn as $check_ripple) 
                        {
                            unset($ripple_members[$check_ripple->user_id]);
                        }

                        $total = count($ripple_members);
                        $rand_number = rand(0, $total - 1);

                        $check_key = 0;
                        foreach ($ripple_members as $key => $ripple_member) 
                        {
                            if ($check_key == $rand_number) 
                            {
                                $final_key = $key;
                            }
                            $check_key++;
                        }

                        $ripple_turn = RippleTurn::create([
                            'ripple_id' => $ripple,
                            'user_id' => $ripple_members[$final_key]['member_id'],
                            'current_user' => 1,
                        ]);
                    }
                    \Log::info("Ripple turn Data insted successfully");
                }
                // exit;
            }
        }
        \Log::info("Ripple turn cron job end!");
        
        // return 0;
    }
}
