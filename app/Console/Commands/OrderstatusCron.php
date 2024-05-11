<?php

namespace App\Console\Commands;
use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use App\Models\PaymentHistory;
use Illuminate\Support\Facades\Http;

use Illuminate\Console\Command;

class OrderstatusCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Orderstatus:cron';

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
        \Log::info("Order status cron start!");

        $order_data = Order::select('orders.api_order_id')->join('plans','plans.id','orders.plan_id')->where('plans.service_type','=','api')->where([['orders.status','!=','Completed'],['orders.status','!=','partially completed'],['orders.status','!=','Canceled'],['orders.api_order_id','!=',0]])->pluck('orders.api_order_id');

        $all_order_id = '';
        foreach($order_data as $single_order_id)
        {
            if($single_order_id)
            {
                $all_order_id .= $single_order_id.',';
            }
        }

        $response = '';
        if($all_order_id)
        {
            $response = Http::withOptions([
                'verify' => false,
            ])->get('https://socialking.co.in/api', [
                'apiKey' => '22bc8dc9fcc0a6bb57b96514a2d4fb68',
                'actionType' => 'mass_status',
                'orderID' => $all_order_id,
            ]);
        }

        $order_id_data = [];

        if($response)
        {
            $order_id_data = $response->json();
            $plan_table_data = Plan::get()->keyBy('id')->toArray();
            if ($order_id_data) {
                foreach($order_id_data as $key => $single)
                {
                    $order_data_for_single_user = Order::where('api_order_id',$key)->first();
                    if($single['orderStatus'] == 'Canceled')
                    {
                        if($order_data_for_single_user->status != 'Canceled')
                        {
                            $user_data = User::find($order_data_for_single_user->user_id);

                            $order_price = $order_data_for_single_user->total_cost;
                            if($order_data_for_single_user->currency_type == 'INR')
                            {
                                $order_price = inrToUsd($order_data_for_single_user->total_cost);
                            }

                            $final_amount = $user_data->amount + $order_price;

                            $user_data->update(['amount' => $final_amount]);

                            PaymentHistory::create([
                                'user_id' => $order_data_for_single_user->user_id,
                                'amount' => $order_price,
                                'paid_by' => 'Refund for order '.$order_data_for_single_user->api_order_id,
                                'status' => 'success'
                            ]);
                        }
                    }

                    if($single['orderStatus'] == 'Partially Completed')
                    {
                        if($order_data_for_single_user->status != 'Partially Completed')
                        {
                            $user_data = User::find($order_data_for_single_user->user_id);

                            if(array_key_exists($order_data_for_single_user->plan_id,$plan_table_data))
                            {
                                $price_for_1000 = $plan_table_data[$order_data_for_single_user->plan_id]['price'];

                                $per_one_data = $price_for_1000/1000;

                                $remaing_amount = $per_one_data * $single['remaining_amount'];

                                $amount = $user_data->amount;
                                $final_amount = $remaing_amount + $amount;

                                $user_data->update(['amount' => $final_amount]);

                                PaymentHistory::create([
                                    'user_id' => $order_data_for_single_user->user_id,
                                    'amount' => $remaing_amount,
                                    'paid_by' => 'Partially refund for order '.$order_data_for_single_user->api_order_id. ' remaining '.$single['remaining_amount'],
                                    'status' => 'success',
                                ]);
                            }
                        }
                    }

                    Order::where('api_order_id',$key)->update([
                        'start_api' => $single['startCount'],
                        'status' => $single['orderStatus'],
                        'api_remaining' => $single['remaining_amount'],
                    ]);
                }
            }
        }

        \Log::info("Order status cron end!");

        return 0;
    }
}
