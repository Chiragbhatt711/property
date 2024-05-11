<?php

use App\Models\Category;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

if (!function_exists('otpGenerate')) {
    function otpGenerate($number = 6)
    {
        return random_int(100000, 999999);
    }
}

if (!function_exists('categories')) {
    function categories()
    {
        // $categories = Category::get()->toArray();
        $categories = Plan::join('categories', 'plans.category_id', '=', 'categories.id')
            ->groupBy('plans.category_id')
            ->select('categories.*')
            ->get()->toArray();

        return $categories;
    }
}

if (!function_exists('service')) {
    function service($id)
    {
        // $service = Plan::select('id', 'name','slug', 'category_id', 'service_id')->where('category_id', $id)->groupBy('name')->get()->toArray();
        $service = Plan::join('categories', 'plans.category_id', '=', 'categories.id','LEFT')
            ->join('services', 'plans.category_id', '=', 'services.id','LEFT')
            ->select('plans.id', 'plans.name', 'plans.slug', 'plans.category_id', 'plans.service_id', 'categories.slug as category_slug', 'services.slug as service_slug')
            ->where('category_id', $id)
            ->groupBy('name')->get()->toArray();

        return $service;
    }
}

if (!function_exists('refNumberGenerate')) {
    function refNumberGenerate()
    {
        $rand = mt_rand(1000000, 9999999);

        if ($rand) {
            $ans = cehckRefNumberExist($rand);

            if ($ans) {
                return $rand;
            }
        }
    }
}

if (!function_exists('cehckRefNumberExist')) {
    function cehckRefNumberExist($rand)
    {
        $exist = User::where('ref_number', $rand)->exists();


        if ($exist) {
            refNumberGenerate();
        } else {
            return true;
        }
    }
}

if (!function_exists('inProgressOrder')) {
    function inProgressOrder()
    {
        $userId = auth()->guard('web')->user()->id;

        $orderCount = Order::where('user_id', $userId)->where('status', '=', 'In Progress')->get()->count();

        return $orderCount;
    }
}

if (!function_exists('completeOrder')) {
    function completeOrder()
    {
        $userId = auth()->guard('web')->user()->id;

        $orderCount = Order::where('user_id', $userId)->where('status', '=', 'Completed')->get()->count();

        return $orderCount;
    }
}

if (!function_exists('cancelOrder')) {
    function cancelOrder()
    {
        $userId = auth()->guard('web')->user()->id;

        $orderCount = Order::where('user_id', $userId)->where('status', '=', 'Canceled')->get()->count();

        return $orderCount;
    }
}

if (!function_exists('ticketIdGenerate')) {
    function ticketIdGenerate()
    {
        $rand = mt_rand(1000, 9999);

        if ($rand) {
            $ans = cehckTicketIdExist($rand);

            if ($ans) {
                return $rand;
            }
        }
    }
}

if (!function_exists('cehckTicketIdExist')) {
    function cehckTicketIdExist($rand)
    {
        $exist = SupportTicket::where('ticket_id', $rand)->exists();


        if ($exist) {
            ticketIdGenerate();
        } else {
            return true;
        }
    }
}

if (!function_exists('usdToInr')) {
    function usdToInr($amount)
    {
        $setting = Setting::first();

        $usdToInr = $setting->usd_to_inr;

        $inr = $amount * $usdToInr;

        return $inr;
    }
}

if (!function_exists('inrToUsd')) {
    function inrToUsd($amount)
    {
        $setting = Setting::first();

        $usdToInr = $setting->usd_to_inr;

        $usd = $amount / $usdToInr;

        return $usd;
    }
}

if (!function_exists('s_encrypt')) {
    function s_encrypt($text, $key, $type)
    {
        // $enc = MCRYPT_RIJNDAEL_128;
        // $mode = MCRYPT_MODE_CBC;
        // $iv = "0123456789abcdef";
        // $size = mcrypt_get_block_size($enc, $mode);
        // $pad = $size - (strlen($text) % $size);
        // $padtext = $text . str_repeat(chr($pad), $pad);
        // $crypt = mcrypt_encrypt($enc, base64_decode($key), $padtext, $mode, $iv);
        // return base64_encode($crypt);

        $iv = "0123456789abcdef";
        $size = 16;
        $pad = $size - (strlen($text) % $size);
        $padtext = $text . str_repeat(chr($pad), $pad);
        $crypt = openssl_encrypt($padtext, "AES-256-CBC", base64_decode($key), OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv);
        return base64_encode($crypt);
    }
}
if (!function_exists('e_decrypt')) {
    function e_decrypt($crypt, $key, $type)
    {
        $iv = "0123456789abcdef";
        $crypt = base64_decode($crypt);
        $padtext = openssl_decrypt($crypt,"AES-256-CBC", base64_decode($key), OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING,$iv);
        $pad = ord($padtext
                    [
                        strlen($padtext) - 1]);
        if ($pad > strlen($padtext)) return false;
        if (strspn($padtext, $padtext
                    [
                        strlen($padtext) - 1], strlen($padtext) - $pad) != $pad)
        {
            $text = "Error";
        }

        $text = substr($padtext, 0, -1 * $pad);
        return $text;

    }
}


if (!function_exists('lifetime')) {
    function lifetime()
    {
        // $userId = auth()->guard('web')->user()->id;
        // $setting = Setting::first();
        // $conversionRate = $setting->usd_to_inr;
        // $lifetimeAmount = Order::select(DB::raw("ROUND(SUM(CASE WHEN orders.currency_type = 'INR' THEN orders.total_cost / $conversionRate ELSE orders.total_cost END),2) AS total_cost"))
        // ->where('user_id', $userId)
        // ->where('orders.is_free', 0)
        // ->groupBy('user_id')->first();
        // if (isset($lifetimeAmount->total_cost)) {
        //     return $lifetimeAmount->total_cost;
        // } else {
        //     return 0;
        // }

        $lifetimeAmount = auth()->guard('web')->user()->spent;

        if (isset($lifetimeAmount)) {
            return $lifetimeAmount;
        } else {
            return 0;
        }
    }
}

if (!function_exists('numberToWord')) {
    function numberToWord($number)
    {
        $ones = array('', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen');
        $tens = array('', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety');

        if ($number == 0) {
            return 'zero';
        } elseif ($number < 0) {
            return 'minus ' . numberToWord(abs($number));
        } elseif ($number < 20) {
            return $ones[$number];
        } elseif ($number < 100) {
            return $tens[(int) ($number / 10)] . ' ' . $ones[$number % 10];
        } elseif ($number < 1000) {
            return $ones[(int) ($number / 100)] . ' hundred ' . numberToWord($number % 100);
        } elseif ($number < 1000000) {
            return numberToWord((int) ($number / 1000)) . ' thousand ' . numberToWord($number % 1000);
        } elseif ($number < 1000000000) {
            return numberToWord((int) ($number / 1000000)) . ' million ' . numberToWord($number % 1000000);
        } else {
            return 'number out of range';
        }
    }
}


if (!function_exists('urlfriendly')) {
    function urlfriendly($url)
    {
        $url = str_replace(' ', '-', $url);
        $url = str_replace('.', '-', $url);
        $url = str_replace('#', '', $url);
        $url = str_replace(':', '', $url);
        $url = str_replace(',', '-', $url);
        $url = str_replace("'", '', $url);
        $url = str_replace('"', '', $url);
        $url = str_replace('----', '-', $url);
        $url = str_replace('---', '-', $url);
        $url = str_replace('--', '-', $url);
        $url = str_replace('/', '_', $url);
        $url = str_replace('&', '-', $url);
        $url = str_replace('(', '-', $url);
        $url = str_replace(')', '-', $url);
        $url = str_replace('.', '-', $url);
        $url = str_replace(';', '-', $url);
        $url = str_replace(',', '-', $url);
        $url = str_replace('--', '-', $url);
        $url = str_replace('-_-', '-', $url);
        $url = str_replace('_-_', '-', $url);
        $url = str_replace(' ', '-', $url);

        return $url;
    }
}

if (!function_exists('get_slug')) {
    function get_slug($table, $slug)
    {
        $slug = urlfriendly($slug);

        $slug = strtolower($slug);
        $slug = trim($slug);

        $slug = str_replace(' ', '-', $slug); // Replaces all spaces with hyphens.
        $slug = preg_replace('/[^A-Za-z0-9\-]/', '', $slug); // Removes special chars.
        $slug = preg_replace('/-+/', '-', $slug); // Replaces multiple hyphens with single one.
        $slug = explode(" ", $slug);
        $slug = implode("-", $slug);

        $checkExist = DB::table($table)->where('slug', $slug)->get()->count();

        if ($checkExist > 0) {
            $slug = $slug . "-" . $checkExist;
        }

        return strtolower($slug);
    }
}
