<?php

namespace App\Http\Controllers;

use App\Models\AccountActivity;
use App\Models\AffiliatesHistory;
use App\Models\Category;
use App\Models\Plan;
use App\Models\Service;
use App\Models\Faq;
use App\Models\NewsManagement;
use App\Models\Order;
use App\Models\Setting;
use App\Models\State;
use App\Models\RatingReview;
use App\Models\ReviewHelpful;
use App\Models\PaymentHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use DB;
use Illuminate\Support\Facades\Auth;
use Mail;
use Carbon\Carbon;
use App\Models\CustomRate;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $servicesPricing = Plan::join('rating_reviews', 'plans.id', '=', 'rating_reviews.service_id', 'LEFT')
            ->join('categories', 'plans.category_id', 'categories.id', 'LEFT')
            ->select('plans.id', 'plans.name', 'plans.start_time', 'plans.min_order', 'plans.max_order', 'plans.speed', 'plans.price', 'plans.try_free', 'plans.description', DB::raw("IFNULL(avg(rating_reviews.rating), 0) as avg_rating"), DB::raw("COUNT(rating_reviews.id) as total_rating"))
            ->groupBy('plans.id');

        if ($request->search) {
            $servicesPricing->where('plans.name', 'LIKE', '%' . $request->search . '%');
        }
        if ($request->category) {
            $servicesPricing->where('plans.category_id', $request->category);
        }
        if ($request->service) {
            $servicesPricing->where('plans.service_id', $request->service);
        }

        $servicesPricing = $servicesPricing->where('plans.active_or_deactivate', 'active');
        if ((isset($request->order) && $request->order) && (isset($request->orderType) && $request->orderType)) {
            $order = $request->order;
            $orderType = $request->orderType;
            $order_by = "";
            switch ($order) {
                case 'id':
                    $order_by = 'plans.id';
                    break;
                case 'service':
                    $order_by = 'plans.name';
                    break;
                case 'rated':
                    $order_by = 'avg_rating';
                    break;
                case 'start_time':
                    $order_by = 'plans.start_time';
                    break;
                case 'min_order':
                    $order_by = 'plans.min_order';
                    break;
                case 'max_order':
                    $order_by = 'plans.max_order';
                    break;
                case 'price':
                    $order_by = 'plans.price';
                    break;
                case 'speed':
                    $order_by = 'plans.speed';
                    break;
            }

            if ($order_by) {
                // $servicesPricing = $servicesPricing->orderBy('plans.try_free','desc');
                $servicesPricing = $servicesPricing->orderBy($order_by, $orderType);
            }
        } else {
            $servicesPricing = $servicesPricing->orderBy('plans.try_free', 'desc');
            $servicesPricing = $servicesPricing->orderBy('categories.sort_order');
        }

        $servicesPricing = $servicesPricing->where('plans.active_or_deactivate', '=', 'active')->get()->count();


        // $categoryId = $servicesPricing->pluck('category_id')->toArray();
        // $categoryId = array_unique($categoryId);
        // $categories = [];
        $categories = Category::get()->pluck('name', 'slug')->toArray();
        $categyIcon = Category::get();

        $serviceID = Plan::select('*');

        if ($request->category) {
            $serviceID->where('category_id', $request->category);
        }
        $serviceID = $serviceID->get()->pluck('service_id')->toArray();

        $serviceID = array_unique($serviceID);
        $service = [];
        if ($serviceID) {
            $service = Service::whereIn('id', $serviceID)->get()->pluck('name', 'slug')->toArray();
        }
        $setting = Setting::first();
        $usdToInr = "";
        if ($setting) {
            $usdToInr = $setting->usd_to_inr;
        }

        $currency = session()->get('currency');
        if (!$currency) {
            $ipAddress = $request->ip();
            $currentUserInfo = \Location::get($ipAddress);
            $currency = "USD";
            if ($currentUserInfo) {
                $country = $currentUserInfo->countryName;

                if ($country == "India") {
                    $currency = "INR";
                } else {
                    $currency = "USD";
                }
            }

            session()->put('currency', $currency);
        }

        // $blog = Http::withOptions([
        //     'verify' => false,
        // ])->get('https://blog.socialking.in/wp-json/wp/v2/posts?per_page=5&orderby=date&order=desc');

        $response = Http::withOptions([
            'verify' => false,
        ])->get('https://blog.socialking.in/wp-json/wp/v2/posts', [
            'per_page' => 6,
            'orderby' => 'date',
            'order' => 'desc',
            '_embed' => true,
        ]);

        // Access the response
        $blogs = $response->json();
        // dd($blogs);

        return view('home', compact('servicesPricing', 'categories', 'service', 'categyIcon', 'usdToInr', 'currency', 'blogs'));
    }

    public function accountActivity(Request $request)
    {
        $userId = auth()->guard('web')->user()->id;
        $accountActivity = AccountActivity::where('user_id', $userId);
        if (isset($request->search) && $request->search) {
            $accountActivity->where('action', 'LIKE', '%' . $request->search . '%')
                ->orWhere('country', 'LIKE', '%' . $request->search . '%')
                ->orWhere('city', 'LIKE', '%' . $request->search . '%');
        }

        $accountActivity->orderBy('id', 'DESC');

        if (isset($request->perpage) && $request->perpage) {
            switch ($request->perpage) {
                case '10':
                    $accountActivity = $accountActivity->paginate(10);
                    break;
                case '25':
                    $accountActivity = $accountActivity->paginate(25);
                    break;
                case '50':
                    $accountActivity = $accountActivity->paginate(50);
                    break;
                case '100':
                    $accountActivity = $accountActivity->paginate(100);
                    break;

                default:
                    $accountActivity = $accountActivity->paginate(10);
                    break;
            }
        } else {
            $accountActivity = $accountActivity->paginate(10);
        }

        $perpage = [
            10 => "10 Per Page",
            25 => "25 Per Page",
            50 => "50 Per Page",
            100 => "100 Per Page",
        ];

        return view('user.account_activity', compact('accountActivity', 'perpage'));
    }

    public function services(Request $request, $s1 = NULL)
    {
        $catId = '';
        if ($s1) {
            $catId = Category::select('id')->where('slug', $s1)->first()->toArray()['id'];
        }
        $servicesPricing = Plan::join('rating_reviews', 'plans.id', '=', 'rating_reviews.service_id', 'LEFT')
            ->select('plans.id', 'plans.name', 'plans.start_time', 'plans.min_order', 'plans.max_order', 'plans.speed', 'plans.price', 'plans.try_free', 'plans.description', DB::raw("IFNULL(avg(rating_reviews.rating), 0) as avg_rating"), DB::raw("COUNT(rating_reviews.id) as total_rating"))
            ->groupBy('plans.id');

        if ($request->search) {
            $servicesPricing->where('plans.name', 'LIKE', '%' . $request->search . '%');
        }
        if ($catId) {
            $servicesPricing->where('plans.category_id', $catId);
        }
        if ($request->service) {
            $servicesPricing->where('rating_reviews.service_id', $request->service);
        }

        $servicesPricing = $servicesPricing->get()->count();


        // $categoryId = $servicesPricing->pluck('category_id')->toArray();
        // $categoryId = array_unique($categoryId);
        // $categories = [];
        $categories = Category::get()->pluck('name', 'slug')->toArray();

        $categories_seo_data_for_meta_tag = '';
        if ($s1) {
            $categories_seo_data_for_meta_tag = Category::where('slug', $s1)->first()->toArray();
        }

        $serviceID = Plan::select('*');

        if ($request->category) {
            $serviceID->where('category_id', $request->category);
        }
        $serviceID = $serviceID->get()->pluck('service_id')->toArray();

        $serviceID = array_unique($serviceID);
        $service = [];
        if ($serviceID) {
            $service = Service::whereIn('id', $serviceID)->get()->pluck('name', 'slug')->toArray();
        }
        $setting = Setting::first();
        $usdToInr = "";
        if ($setting) {
            $usdToInr = $setting->usd_to_inr;
        }

        $ipAddress = $request->ip();
        $currentUserInfo = \Location::get($ipAddress);
        $currancy = "";
        if ($currentUserInfo) {
            $country = $currentUserInfo->countryName;
            if ($country == "India") {
                $currancy = "INR";
            } else {
                $currancy = "USD";
            }
        }

        return view('services', compact('servicesPricing', 'categories', 'service', 'usdToInr', 'currancy', 'categories_seo_data_for_meta_tag', 's1'));
    }

    public function faq()
    {
        $faq = Faq::join('categories', 'faqs.category_id', '=', 'categories.id')
            ->select('faqs.category_id', 'faqs.service_id', 'faqs.question', 'faqs.answer', 'categories.name as category')->orderBy('faqs.category_id')
            ->get();

        $faqCategory = Faq::join('categories', 'faqs.category_id', '=', 'categories.id')
            ->select('faqs.category_id', 'categories.name as category')
            ->groupBy('faqs.category_id')->get();

        $faqSubCategory = Faq::join('services', 'faqs.service_id', '=', 'services.id')
            ->select('faqs.service_id', 'faqs.category_id', 'faqs.question', 'faqs.answer', 'services.name as service')
            ->groupBy('faqs.category_id', 'faqs.service_id')->get();

        // dd($faqSubCategory);

        return view('faq', compact('faqCategory', 'faqSubCategory', 'faq'));
    }

    public function contactUs(Request $request)
    {
        $name = $request->name;
        $email = $request->email;
        $help = $request->help;

        Mail::send('mail.contact_us', ['name' => $name, 'email' => $email, 'help' => $help], function ($message) {
            $message->from(env("MAIL_FROM_ADDRESS"));
            $message->to('support@socialking.in');
            $message->subject('Contact Us');
        });

        return redirect()->back()->with('success', 'message successfully send');
    }

    public function servicesAutocomplete(Request $request)
    {
        $data = Plan::select("name", "slug")
            ->where("name", "LIKE", "%" . $request->get('query') . "%")
            ->get();
        $dataArray = [];
        if ($data) {
            foreach ($data as $key => $value) {
                $dataArray['data'][] = array("value" => $value->name, "lable" => $value->slug);
            }
        }

        return response()->json($dataArray);
    }

    public function myOrder(Request $request)
    {
        $userId = auth()->guard('web')->user()->id;
        $myorders = Order::where('orders.user_id', $userId)
            ->where('orders.is_free', 0)
            ->join('plans', 'orders.plan_id', 'plans.id')
            ->leftJoin('rating_reviews', function ($join) use ($userId) {
                $join->on('plans.id', '=', 'rating_reviews.service_id')
                    ->where('rating_reviews.user_id', '=', $userId);
            });
        if (isset($request->status) && $request->status) {
            $myorders->where('orders.status', $request->status);
        }
        if (isset($request->search) && $request->search) {
            $searchValue = $request->search;
            $myorders->where(function ($query) use ($searchValue) {
                $query->where('orders.id', 'LIKE', '%' . $searchValue ? str_replace("SK", "", $searchValue) : $searchValue  . '%');
                $query->orWhere('orders.completed_at', 'LIKE', '%' . $searchValue . '%');
                $query->orWhere('orders.link', 'LIKE', '%' . $searchValue . '%');
                $query->orWhere('plans.name', 'LIKE', '%' . $searchValue . '%');
                $query->orWhere('orders.completed_at', 'LIKE', '%' . $searchValue . '%');
                $query->orWhere('orders.total_cost', 'LIKE', '%' . $searchValue . '%');
                $query->orWhere('orders.quantity', 'LIKE', '%' . $searchValue . '%');
                $query->orWhere('orders.start_api', 'LIKE', '%' . $searchValue . '%');
                $query->orWhere('orders.status', 'LIKE', '%' . $searchValue . '%');
            });
            // $myorders->where('orders.status','LIKE','%'.$request->search.'%')
            //     ->orWhere('plans.name','LIKE','%'.$request->search.'%');
        }
        $myorders->select('plans.name as plan', 'plans.start_time', 'rating_reviews.rating', 'orders.*');

        if ((isset($request->order) && $request->order) && (isset($request->orderType) && $request->orderType)) {
            $order = $request->order;
            $orderType = $request->orderType;
            $order_by = "";
            switch ($order) {
                case 'id':
                    $order_by = 'orders.id';
                    break;
                case 'date':
                    $order_by = 'orders.created_at';
                    break;
                case 'link':
                    $order_by = 'orders.link';
                    break;
                case 'details':
                    $order_by = 'plans.name';
                    break;
                case 'price':
                    $order_by = 'orders.total_cost';
                    break;
                case 'wanted':
                    $order_by = 'orders.quantity';
                    break;
                case 'start':
                    $order_by = 'orders.start_api';
                    break;
                case 'status':
                    $order_by = 'orders.status';
                    break;
            }
            // dd($orderType);
            if ($order_by) {
                $myorders = $myorders->orderBy($order_by, $orderType);
            }
        } else {
            $myorders = $myorders->orderBy('orders.id', 'desc');
        }
        $myorders = $myorders->groupBy('orders.id');
        if (isset($request->perpage) && $request->perpage) {
            switch ($request->perpage) {
                case '10':
                    $myorders = $myorders->paginate(10);
                    break;
                case '25':
                    $myorders = $myorders->paginate(25);
                    break;
                case '50':
                    $myorders = $myorders->paginate(50);
                    break;
                case '100':
                    $myorders = $myorders->paginate(100);
                    break;

                default:
                    $myorders = $myorders->paginate(10);
                    break;
            }
        } else {
            $myorders = $myorders->paginate(10);
        }

        $perpage = [
            10 => "10 Per Page",
            25 => "25 Per Page",
            50 => "50 Per Page",
            100 => "100 Per Page",
        ];

        $status = [
            'Pending' => 'Pending',
            'In Progress' => 'In Progress',
            'Completed' => 'Completed',
            'Canceled' => 'Canceled',
            'Partially Completed' => 'Partially Completed'
        ];

        // echo "<pre>";
        // print_r($myorders);
        // exit;

        return view('user.my_orders', compact('myorders', 'perpage', 'status'));
    }

    public function get_my_order(Request $request)
    {
        $draw = $request->post('draw');
        $start = $request->post("start");
        $rowperpage = $request->post("length");
        $columnIndex_arr = $request->post('order');
        $columnName_arr = $request->post('columns');
        $order_arr = $request->post('order');
        $search_arr = $request->post('search');
        $columnIndex = $columnIndex_arr[0]['column'];
        $columnName = $columnName_arr[$columnIndex]['data'];
        $columnSortOrder = $order_arr[0]['dir'];
        $searchValue = $search_arr['value'];

        $userId = auth()->guard('web')->user()->id;
        $totalRecords = Order::where('orders.user_id', $userId)
            ->where('orders.is_free', 0)
            ->join('plans', 'orders.plan_id', 'plans.id')
            ->leftJoin('rating_reviews', function ($join) use ($userId) {
                $join->on('plans.id', '=', 'rating_reviews.service_id')
                    ->where('rating_reviews.user_id', '=', $userId);
            });
        $totalRecords->select('plans.name as plan', 'plans.start_time', 'rating_reviews.rating', 'orders.*')
            ->orderBy('orders.id', 'desc')->count();

        $totalRecordswithFilter = Order::where('orders.user_id', $userId)
            ->where('orders.is_free', 0)
            ->join('plans', 'orders.plan_id', 'plans.id')
            ->leftJoin('rating_reviews', function ($join) use ($userId) {
                $join->on('plans.id', '=', 'rating_reviews.service_id')
                    ->where('rating_reviews.user_id', '=', $userId);
            });
        if (isset($request->status) && $request->status) {
            $totalRecords->where('orders.status', $request->status);
        }
        if (isset($request->search) && $request->search) {
            $totalRecords->where('orders.status', 'LIKE', '%' . $request->search . '%')
                ->orWhere('plans.name', 'LIKE', '%' . $request->search . '%');
        }
        $totalRecords->select('plans.name as plan', 'plans.start_time', 'rating_reviews.rating', 'orders.*')
            ->orderBy('orders.id', 'desc')->count();

        $totalRecordswithFilter = Order::where('orders.user_id', $userId)
            ->where('orders.is_free', 0)
            ->join('plans', 'orders.plan_id', 'plans.id')
            ->leftJoin('rating_reviews', function ($join) use ($userId) {
                $join->on('plans.id', '=', 'rating_reviews.service_id')
                    ->where('rating_reviews.user_id', '=', $userId);
            })
            ->where(function ($query) use ($searchValue) {
                $query->where('orders.id', 'LIKE', '%' . $request->search . '%');
                $query->orWhere('orders.completed_at', 'LIKE', '%' . $request->search . '%');
                $query->orWhere('orders.link', 'LIKE', '%' . $request->search . '%');
                $query->orWhere('plans.name', 'LIKE', '%' . $request->search . '%');
                $query->orWhere('orders.completed_at', 'LIKE', '%' . $request->search . '%');
                $query->orWhere('orders.total_cost', 'LIKE', '%' . $request->search . '%');
                $query->orWhere('orders.quantity', 'LIKE', '%' . $request->search . '%');
                $query->orWhere('orders.start_api', 'LIKE', '%' . $request->search . '%');
                $query->orWhere('orders.status', 'LIKE', '%' . $request->search . '%');
            });
        if (isset($request->status) && $request->status) {
            $totalRecordswithFilter->where('orders.status', $request->status);
        }
        if (isset($request->search) && $request->search) {
            $totalRecordswithFilter->where('orders.status', 'LIKE', '%' . $request->search . '%')
                ->orWhere('plans.name', 'LIKE', '%' . $request->search . '%');
        }
        $totalRecordswithFilter->select('plans.name as plan', 'plans.start_time', 'rating_reviews.rating', 'orders.*')
            ->orderBy('orders.id', 'desc')->count();

        $records = Order::where('orders.user_id', $userId)
            ->where('orders.is_free', 0)
            ->join('plans', 'orders.plan_id', 'plans.id')
            ->leftJoin('rating_reviews', function ($join) use ($userId) {
                $join->on('plans.id', '=', 'rating_reviews.service_id')
                    ->where('rating_reviews.user_id', '=', $userId);
            })
            ->where(function ($query) use ($searchValue) {
                $query->where('orders.id', 'LIKE', '%' . $request->search . '%');
                $query->orWhere('orders.completed_at', 'LIKE', '%' . $request->search . '%');
                $query->orWhere('orders.link', 'LIKE', '%' . $request->search . '%');
                $query->orWhere('plans.name', 'LIKE', '%' . $request->search . '%');
                $query->orWhere('orders.completed_at', 'LIKE', '%' . $request->search . '%');
                $query->orWhere('orders.total_cost', 'LIKE', '%' . $request->search . '%');
                $query->orWhere('orders.quantity', 'LIKE', '%' . $request->search . '%');
                $query->orWhere('orders.start_api', 'LIKE', '%' . $request->search . '%');
                $query->orWhere('orders.status', 'LIKE', '%' . $request->search . '%');
            });
        if (isset($request->status) && $request->status) {
            $records->where('orders.status', $request->status);
        }
        $records->select('plans.name as plan', 'plans.start_time', 'rating_reviews.rating', 'orders.*')
            ->orderBy('orders.id', 'desc')
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();
        foreach ($records as $record) {
            $id = '<strong class="ht">ID</strong><p>' . $record->id . '</p>';
            $date_added = '<strong class="ht">Date Added</strong><p>' . $record->created_at . '</p>';
            $link = '<strong class="ht">Link</strong><p>' . $record->link . '</p>';
            $service_name = '<strong class="ht">Service Name</strong><p>' . $record->plan . '</p>';
            $completed_at = '<strong class="ht">Completed at</strong><p>' . $record->completed_at . '</p>';
            $Price = '<strong class="ht">Price</strong><p>' . $record->currency_type == 'USD' ? '$' : '₹';
            $Price .= ' ' . number_format((float)$record->total_cost, 2, '.', '') . '</p>';
            $wanted = '<strong class="ht">Wanted</strong><p>' . $record->quantity . '</p>';
            $start = '<strong class="ht">Start</strong><p>' . $record->start_api . '</p>';
            $status = '<strong class="ht">Status</strong><p>' . $record->status . '</p>';;
            $report = '<strong class="ht">Report</strong><p></p>';

            $data_arr[] = array(
                "id" => $id,
                "date_added" => $date_added,
                "link" => $link,
                "service_name" => $service_name,
                "completed_at" => $completed_at,
                "price" => $price,
                "wanted" => $wanted,
                "start" => $start,
                "status" => $status,
                "report" => $report,
            );
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );
        echo json_encode($response);
    }


    public function myFreeOrder(Request $request)
    {
        $userId = auth()->guard('web')->user()->id;
        $myorders = Order::where('orders.user_id', $userId)
            ->where('orders.is_free', 1)
            ->join('plans', 'orders.plan_id', 'plans.id')
            ->leftJoin('rating_reviews', function ($join) use ($userId) {
                $join->on('plans.id', '=', 'rating_reviews.service_id')
                    ->where('rating_reviews.user_id', '=', $userId);
            });
        if (isset($request->status) && $request->status) {
            $myorders->where('orders.status', $request->status);
        }
        if (isset($request->search) && $request->search) {
            $searchValue = $request->search;
            $myorders->where(function ($query) use ($searchValue) {
                $query->where('orders.id', 'LIKE', '%' . $searchValue ? str_replace("SK", "", $searchValue) : $searchValue . '%');
                $query->orWhere('orders.completed_at', 'LIKE', '%' . $searchValue . '%');
                $query->orWhere('orders.link', 'LIKE', '%' . $searchValue . '%');
                $query->orWhere('plans.name', 'LIKE', '%' . $searchValue . '%');
                $query->orWhere('orders.completed_at', 'LIKE', '%' . $searchValue . '%');
                $query->orWhere('orders.total_cost', 'LIKE', '%' . $searchValue . '%');
                $query->orWhere('orders.quantity', 'LIKE', '%' . $searchValue . '%');
                $query->orWhere('orders.start_api', 'LIKE', '%' . $searchValue . '%');
                $query->orWhere('orders.status', 'LIKE', '%' . $searchValue . '%');
            });
        }
        $myorders->select('plans.name as plan', 'plans.start_time', 'rating_reviews.rating', 'orders.*');

        if ((isset($request->order) && $request->order) && (isset($request->orderType) && $request->orderType)) {
            $order = $request->order;
            $orderType = $request->orderType;
            $order_by = "";
            switch ($order) {
                case 'id':
                    $order_by = 'orders.id';
                    break;
                case 'date':
                    $order_by = 'orders.created_at';
                    break;
                case 'link':
                    $order_by = 'orders.link';
                    break;
                case 'details':
                    $order_by = 'plans.name';
                    break;
                case 'price':
                    $order_by = 'orders.total_cost';
                    break;
                case 'wanted':
                    $order_by = 'orders.quantity';
                    break;
                case 'start':
                    $order_by = 'orders.start_api';
                    break;
                case 'status':
                    $order_by = 'orders.status';
                    break;
            }
            // dd($orderType);
            if ($order_by) {
                $myorders = $myorders->orderBy($order_by, $orderType);
            }
        } else {
            $myorders = $myorders->orderBy('orders.id', 'desc');
        }

        if (isset($request->perpage) && $request->perpage) {
            switch ($request->perpage) {
                case '10':
                    $myorders = $myorders->paginate(10);
                    break;
                case '25':
                    $myorders = $myorders->paginate(25);
                    break;
                case '50':
                    $myorders = $myorders->paginate(50);
                    break;
                case '100':
                    $myorders = $myorders->paginate(100);
                    break;

                default:
                    $myorders = $myorders->paginate(10);
                    break;
            }
        } else {
            $myorders = $myorders->paginate(10);
        }

        $perpage = [
            10 => "10 Per Page",
            25 => "25 Per Page",
            50 => "50 Per Page",
            100 => "100 Per Page",
        ];

        $status = [
            'Pending' => 'Pending',
            'In Progress' => 'In Progress',
            'Completed' => 'Completed',
        ];

        return view('user.my_free_orders', compact('myorders', 'perpage', 'status'));
    }

    public function affiliate()
    {
        $setting = Setting::select('level_1', 'level_2')->first();
        $leval1 = $setting->level_1;
        $leval2 = $setting->level_2;

        $ref_history = AffiliatesHistory::where('user_id', auth()->guard('web')->user()->id)
            ->join('users', 'affiliates_histories.affiliate_user_id', 'users.id')
            ->select('affiliates_histories.*', 'users.username as username')
            ->orderBy('id', 'DESC')
            ->paginate(10);

        $earning = AffiliatesHistory::where('user_id', auth()->guard('web')->user()->id)->select(DB::raw("SUM(amount) as total_earn"))->first();
        $total_earning = $earning->total_earn;

        $refNumber = Auth()->guard('web')->user()->ref_number;

        return view('user.affiliate', compact('refNumber', 'leval1', 'leval2', 'ref_history', 'total_earning'));
    }

    public function getState(Request $request)
    {
        $countryId = $request->countryId;

        $state = State::where('country_id', $countryId)->select('id', 'name')->get()->pluck('name', 'id')->toArray();

        return response()->json($state);
    }

    public function userDashboard(Request $request)
    {
        $userId = auth()->guard('web')->user()->id;
        $myorders = Order::where('orders.user_id', $userId)
            ->join('plans', 'orders.plan_id', 'plans.id')
            ->leftJoin('rating_reviews', function ($join) use ($userId) {
                $join->on('plans.id', '=', 'rating_reviews.service_id')
                    ->where('rating_reviews.user_id', '=', $userId);
            });
        if (isset($request->status) && $request->status) {
            $myorders->where('orders.status', $request->status);
        }
        if (isset($request->search) && $request->search) {
            $searchValue = $request->search;
            $myorders->where(function ($query) use ($searchValue) {
                $query->where('orders.id', 'LIKE', '%' . $searchValue ? str_replace("SK", "", $searchValue) : $searchValue . '%');
                $query->orWhere('orders.completed_at', 'LIKE', '%' . $searchValue . '%');
                $query->orWhere('orders.link', 'LIKE', '%' . $searchValue . '%');
                $query->orWhere('plans.name', 'LIKE', '%' . $searchValue . '%');
                $query->orWhere('orders.completed_at', 'LIKE', '%' . $searchValue . '%');
                $query->orWhere('orders.total_cost', 'LIKE', '%' . $searchValue . '%');
                $query->orWhere('orders.quantity', 'LIKE', '%' . $searchValue . '%');
                $query->orWhere('orders.start_api', 'LIKE', '%' . $searchValue . '%');
                $query->orWhere('orders.status', 'LIKE', '%' . $searchValue . '%');
            });
            // $myorders->where('orders.status','LIKE','%'.$request->search.'%')
            //     ->orWhere('plans.name','LIKE','%'.$request->search.'%');
        }

        $myorders->select('plans.name as plan', 'plans.start_time', 'rating_reviews.rating', 'orders.*')
            ->groupBY('orders.id');

        if ((isset($request->order) && $request->order) && (isset($request->orderType) && $request->orderType)) {
            $order = $request->order;
            $orderType = $request->orderType;
            $order_by = "";
            switch ($order) {
                case 'id':
                    $order_by = 'orders.id';
                    break;
                case 'date':
                    $order_by = 'orders.created_at';
                    break;
                case 'link':
                    $order_by = 'orders.link';
                    break;
                case 'details':
                    $order_by = 'plans.name';
                    break;
                case 'price':
                    $order_by = 'orders.total_cost';
                    break;
                case 'wanted':
                    $order_by = 'orders.quantity';
                    break;
                case 'start':
                    $order_by = 'orders.start_api';
                    break;
                case 'status':
                    $order_by = 'orders.status';
                    break;
            }
            // dd($orderType);
            if ($order_by) {
                $myorders = $myorders->orderBy($order_by, $orderType);
            }
        } else {
            $myorders = $myorders->orderBy('orders.id', 'desc');
        }

        if (isset($request->perpage) && $request->perpage) {
            switch ($request->perpage) {
                case '10':
                    $myorders = $myorders->paginate(10);
                    break;
                case '25':
                    $myorders = $myorders->paginate(25);
                    break;
                case '50':
                    $myorders = $myorders->paginate(50);
                    break;
                case '100':
                    $myorders = $myorders->paginate(100);
                    break;

                default:
                    $myorders = $myorders->paginate(10);
                    break;
            }
        } else {
            $myorders = $myorders->paginate(10);
        }

        $news = NewsManagement::orderBy('id', 'DESC')->limit(5)->get();

        // $myorder_order_id =$myorders->pluck('api_order_id');

        // $all_order_id = '';
        // foreach($myorder_order_id as $single_order_id)
        // {
        //     if($single_order_id)
        //     {
        //         $all_order_id .= $single_order_id.',';
        //     }
        // }
        // $response = '';
        // if($all_order_id)
        // {
        //     $response = Http::withOptions([
        //         'verify' => false,
        //     ])->get('https://socialking.co.in/api', [
        //         'apiKey' => '22bc8dc9fcc0a6bb57b96514a2d4fb68',
        //         'actionType' => 'mass_status',
        //         'orderID' => $all_order_id,
        //     ]);
        // }

        // $order_id_data = [];
        // if($response)
        // {
        //     $order_id_data = $response->json();
        // }


        $perpage = [
            10 => "10 Per Page",
            25 => "25 Per Page",
            50 => "50 Per Page",
            100 => "100 Per Page",
        ];

        $status = [
            'Pending' => 'Pending',
            'In Progress' => 'In Progress',
            'Completed' => 'Completed',
            'Canceled' => 'Canceled',
        ];
        return view('user.dashboard', compact('myorders', 'news', 'perpage', 'status'));
    }

    public function AllNews()
    {
        $news = NewsManagement::orderBy('id', 'DESC')->paginate(5);

        return view('user.all_news', compact('news'));
    }

    public function userService(Request $request)
    {
        $servicesPricing = Plan::join('rating_reviews', 'plans.id', '=', 'rating_reviews.service_id', 'LEFT')
            ->join('categories', 'plans.category_id', 'categories.id', 'LEFT')
            ->select('plans.id', 'plans.name', 'plans.start_time', 'plans.min_order', 'plans.max_order', 'plans.speed', 'plans.price', 'plans.try_free', 'plans.description', DB::raw("IFNULL(avg(rating_reviews.rating), 0) as avg_rating"), DB::raw("COUNT(rating_reviews.id) as total_rating"))
            ->groupBy('plans.id');

        // dd($request->all());
        if ($request->search) {
            $servicesPricing->where('plans.name', 'LIKE', '%' . $request->search . '%');
        }
        if ($request->category) {
            $servicesPricing->where('plans.category_id', $request->category);
        }
        if ($request->service) {
            $servicesPricing->where('plans.service_id', $request->service);
        }
        $servicesPricing = $servicesPricing->where('plans.active_or_deactivate', 'active');
        if ((isset($request->order) && $request->order) && (isset($request->orderType) && $request->orderType)) {
            $order = $request->order;
            $orderType = $request->orderType;
            $order_by = "";
            switch ($order) {
                case 'id':
                    $order_by = 'plans.id';
                    break;
                case 'service':
                    $order_by = 'plans.name';
                    break;
                case 'rating':
                    $order_by = 'avg_rating';
                    break;
                case 'start_time':
                    $order_by = 'plans.start_time';
                    break;
                case 'min_order':
                    $order_by = 'plans.min_order';
                    break;
                case 'max_order':
                    $order_by = 'plans.max_order';
                    break;
                case 'price':
                    $order_by = 'plans.price';
                    break;
                case 'speed':
                    $order_by = 'plans.speed';
                    break;
            }

            if ($order_by) {
                $servicesPricing = $servicesPricing->orderBy($order_by, $orderType);
            }
        } else {
            $servicesPricing = $servicesPricing->orderBy('plans.try_free', 'desc');
            $servicesPricing = $servicesPricing->orderBy('categories.sort_order');
        }
        $servicesPricing = $servicesPricing->paginate(5);

        // $categoryId = $servicesPricing->pluck('category_id')->toArray();
        // $categoryId = array_unique($categoryId);
        // $categories = [];
        $categories = Category::get()->pluck('name', 'id')->toArray();


        $serviceID = Plan::select('*');

        if ($request->category) {
            $serviceID->where('category_id', $request->category);
        }
        $serviceID = $serviceID->get()->pluck('service_id')->toArray();

        $serviceID = array_unique($serviceID);
        $service = [];
        if ($serviceID) {
            $service = Service::whereIn('id', $serviceID)->get()->pluck('name', 'id')->toArray();
        }
        $setting = Setting::first();
        $usdToInr = "";
        if ($setting) {
            $usdToInr = $setting->usd_to_inr;
        }

        $ipAddress = $request->ip();
        $currentUserInfo = \Location::get($ipAddress);
        $currancy = "";
        if ($currentUserInfo) {
            $country = $currentUserInfo->countryName;
            if ($country == "India") {
                $currancy = "INR";
            } else {
                $currancy = "USD";
            }
        }

        $customrate_table_data = CustomRate::where('user_id', auth()->guard('web')->user()->id)->get()->keyBy('service_id')->toArray();

        return view('user.services', compact('servicesPricing', 'categories', 'service', 'usdToInr', 'currancy', 'customrate_table_data'));
    }

    public function depositHistory(Request $request)
    {
        $perpage = [
            10 => "10 Per Page",
            25 => "25 Per Page",
            50 => "50 Per Page",
            100 => "100 Per Page",
        ];

        $page = 10;
        if (isset($request->perpage) && $request->perpage && is_numeric($request->perpage)) {
            $page = $request->perpage;
        }

        $payment_histories = PaymentHistory::where('user_id', auth()->guard('web')->user()->id)
            ->where(function($query){
                $query->where('status', '=', 'Success')
                    ->orWhere('paid_by','=','offline');

            });
        if (isset($request->search) && $request->search) {
            $search = $request->search;
            $payment_histories = $payment_histories->where(function ($query) use ($search) {
                $query->where('created_at', 'LIKE', '%' . $search . '%')
                    ->orwhere('bonus', 'LIKE', '%' . $search . '%')
                    ->orwhere('amount', 'LIKE', '%' . $search . '%')
                    ->orwhere('status', 'LIKE', '%' . $search . '%');
            });
        }
        if(isset($request->start_date) && $request->start_date && isset($request->end_date) && $request->end_date)
        {
            $payment_histories = $payment_histories->whereBetween('created_at',[$request->start_date,$request->end_date]);
        }

        if ((isset($request->order) && $request->order) && (isset($request->orderType) && $request->orderType)) {
            $order = $request->order;
            $orderType = $request->orderType;
            $order_by = "";
            switch ($order) {
                case 'id':
                    $order_by = 'id';
                    break;
                case 'date':
                    $order_by = 'created_at';
                    break;
                case 'total':
                    $order_by = 'amount';
                    break;
                case 'bonus':
                    $order_by = 'bonus';
                    break;
            }

            if ($order_by) {
                $payment_histories = $payment_histories->orderBy($order_by, $orderType);
            }
        } else {
            $payment_histories = $payment_histories->orderBy('id', 'DESC');
        }

        $payment_histories = $payment_histories->paginate($page);

        return view('user.deposit_history', compact('perpage', 'payment_histories'));
    }

    public function getServiceDetails(Request $request)
    {
        $id = $request->id;

        $service = Order::where('orders.id', $id)
            ->join('plans', 'orders.plan_id', 'plans.id', 'LEFT')
            ->select(DB::raw("CONCAT(orders.quantity,' ', plans.name, ' - [', plans.start_time, ']  [', plans.speed, ' /day] [ID', plans.id, ']') AS plan"))
            ->first();

        return response()->json($service->plan);
    }

    public function RatingStore(Request $request)
    {
        $create = RatingReview::create([
            'service_id' => $request->id,
            'user_id' => auth()->guard('web')->user()->id,
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        return redirect()->route('my_order');
    }

    public function getRatingReview(Request $request)
    {

        $description = Plan::find($request->id);
        $description = $description->description;

        $userIp = $request->ip();
        $location = \Location::get($userIp);

        if ($location && $location->countryCode === 'IN') {
            $setting = Setting::first();
            $usdToInr = 0;
            if ($setting) {
                $usdToInr = $setting->usd_to_inr;
            }
            $conversionRate = $usdToInr; // Replace with the current conversion rate
            $lable = Plan::where(['id' => $request->id])
                ->select('id', DB::raw("CONCAT(name, ' - [', start_time, ']  [Speed:', speed, ' /Day] -- ₹', ROUND(price * $conversionRate), ' per 1000') AS plan"))
                ->first();
        } else {
            $lable = Plan::where(['id' => $request->id])
                ->select('id', DB::raw("CONCAT(name, ' - [', start_time, ']  [Speed:', speed, ' /Day] -- $', price, ' per 1000') AS plan"))
                ->first();
        }

        $lable = $lable->plan;

        $averageRating = RatingReview::where('service_id', $request->id)
            ->avg('rating');
        $averageRating = round($averageRating, 2);
        $totalRating = RatingReview::where('service_id', $request->id)->count();
        $progress = RatingReview::where('service_id', $request->id)
            ->select('rating', DB::raw("COUNT(id) as total"))
            ->groupBY('rating')
            ->get()->toArray();

        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 5);
        $query = RatingReview::where('service_id', $request->id)
            ->join('users', 'rating_reviews.user_id', '=', 'users.id', 'LEFT')
            ->leftjoin('review_helpfuls as helpful', function ($join) {
                $join->on('helpful.review_id', '=', 'rating_reviews.id')
                    ->where('helpful.user_id', isset(auth()->guard('web')->user()->id) && auth()->guard('web')->user()->id ? auth()->guard('web')->user()->id : 0);
            })
            ->select(
                'rating_reviews.*',
                'users.username',
                'helpful.review_id as helpful_id',
                DB::raw('(SELECT COUNT(review_helpfuls.id) FROM review_helpfuls WHERE review_helpfuls.review_id = rating_reviews.id) AS review_helpful_count'),
                DB::raw('CASE
                WHEN rating_reviews.user_id IS NULL THEN rating_reviews.name
                ELSE users.username
                END AS username')
            )
            ->offset($offset)
            ->limit($limit);

        switch ($request->orderBy) {
            case "most_recent":
                $query->orderBy('rating_reviews.created_at', 'ASC');
                break;
            case "most_helpful":
                $query->orderBy('review_helpful_count', 'DESC');
                break;
        }

        $ratingReview = $query->get()->map(function ($review) {
            $review['created_at_human'] = Carbon::parse($review['created_at'])->diffForHumans();
            return $review;
        })->toArray();

        $mainArray = [
            'description' => $description,
            'lable' => $lable,
            'average_rating' => $averageRating,
            'total_rating' => $totalRating,
            'progress' => $progress,
            'rating_review' => $ratingReview,
        ];

        return response()->json($mainArray);
    }

    public function loadMoreReview(Request $request)
    {
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 5);
        $ratingReview = RatingReview::where('service_id', $request->id)
            ->join('users', 'rating_reviews.user_id', '=', 'users.id', 'LEFT')
            ->leftjoin('review_helpfuls as helpful', function ($join) {
                $join->on('helpful.review_id', '=', 'rating_reviews.id')
                    ->where('helpful.user_id', isset(auth()->guard('web')->user()->id) && auth()->guard('web')->user()->id ? auth()->guard('web')->user()->id : 0);
            })
            ->select(
                'rating_reviews.*',
                'users.username',
                'helpful.review_id as helpful_id',
                DB::raw('(SELECT COUNT(review_helpfuls.id) FROM review_helpfuls WHERE review_helpfuls.review_id = rating_reviews.id) AS review_helpful_count'),
                DB::raw('CASE
                WHEN rating_reviews.user_id IS NULL THEN rating_reviews.name
                ELSE users.username
                END AS username')
            )
            ->offset($offset)
            ->limit($limit);
        switch ($request->orderBy) {
            case "most_recent":
                $ratingReview->orderBy('rating_reviews.created_at', 'DESC');
                break;
            case "most_helpful":
                $ratingReview->orderBy('review_helpful_count', 'DESC');
                break;
        }
        $ratingReview = $ratingReview->get()->map(function ($review) {
            $review['created_at_human'] = Carbon::parse($review['created_at'])->diffForHumans();
            return $review;
        })->toArray();

        return response()->json($ratingReview);
    }

    public function reviewHelpful(Request $request)
    {
        $create = ReviewHelpful::create([
            'review_id' => $request->id,
            'user_id' => auth()->guard('web')->user()->id
        ]);

        $total = ReviewHelpful::where('review_id', $request->id)->count();

        return response()->json($total);
    }

    public function ratingOrderChange(Request $request)
    {
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 5);
        $ratingReview = RatingReview::where('service_id', $request->id)
            ->join('users', 'rating_reviews.user_id', '=', 'users.id', 'LEFT')
            ->leftjoin('review_helpfuls as helpful', function ($join) {
                $join->on('helpful.review_id', '=', 'rating_reviews.id')
                    ->where('helpful.user_id', isset(auth()->guard('web')->user()->id) && auth()->guard('web')->user()->id ? auth()->guard('web')->user()->id : 0);
            })
            ->select(
                'rating_reviews.*',
                'users.username',
                'helpful.review_id as helpful_id',
                DB::raw('(SELECT COUNT(review_helpfuls.id) FROM review_helpfuls WHERE review_helpfuls.review_id = rating_reviews.id) AS review_helpful_count'),
                DB::raw('CASE
                WHEN rating_reviews.user_id IS NULL THEN rating_reviews.name
                ELSE users.username
                END AS username')
            )
            ->offset($offset)
            ->limit($limit);
        switch ($request->orderBy) {
            case "most_recent":
                $ratingReview->orderBy('rating_reviews.created_at', 'DESC');
                break;
            case "most_helpful":
                $ratingReview->orderBy('review_helpful_count', 'DESC');
                break;
        }
        // if($request->orderBy == 'most_recent')
        // {
        //     $ratingReview->orderBy('rating_reviews.created_at','ASC');
        // }
        $ratingReview = $ratingReview->get()->map(function ($review) {
            $review['created_at_human'] = Carbon::parse($review['created_at'])->diffForHumans();
            return $review;
        })->toArray();

        return response()->json($ratingReview);
    }

    public function getService(Request $request)
    {
        $service = Plan::where('category_id', $request->id)
            ->select('id', 'name')
            ->get()->pluck('name', 'id')->toArray();

        return response()->json($service);
    }

    public function getFaq(Request $request)
    {
        $plan = Plan::find($request->id);

        $faq = Faq::where(['category_id' => $plan['category_id'], 'service_id' => $plan['service_id']])->get();

        $faqs = [];
        if ($faq) {
            $faqs = $faq->toArray();
        }

        return response()->json($faqs);
    }

    public function categoryWiseService(Request $request, $s1, $s2 = NULL)
    {
        // echo $s1."<br/>";
        // echo $s2;
        // dd($request->all);
        $serviceName="";
        $__catSlug="";
        $__servicesSlug="";

        if ($s1 && !$s2) {
            if (!count($request->all())) {
                $request = json_decode(json_encode([
                    "search" => '',
                    "ip" => '',
                    "category" => '',
                    "service" => ''
                ]));
            }
            $serviceData = Plan::select('category_id', 'service_id')->where('slug', '=', $s1)->first()->toArray();
            if ($serviceData) {
                $request->category = $serviceData['category_id'];
                $request->service = $serviceData['service_id'];


                $__cat = Category::select('id', 'slug')->where('id', $request->category)->first()->toArray();
                $__catSlug = $__cat['slug'];

                $__services = Service::select('id', 'slug')->where('id', $request->service)->first()->toArray();
                $__servicesSlug = $__services['slug'];
            }
            $serviceName = $s1;
        }
        if ($s1 && $s2) {
            $__cat = Category::select('id', 'slug')->where('slug', $s1)->first()->toArray();
            $request->category = $__cat['id'];
            $__catSlug = $__cat['slug'];

            $__services = Service::select('id', 'slug')->where('slug', $s2)->first()->toArray();
            $request->service = $__services['id'];
            $__servicesSlug = $__services['slug'];
            $serviceName = $s1 . ' ' . $s2;
        }
        $servicesPricing = Plan::join('rating_reviews', 'plans.id', '=', 'rating_reviews.service_id', 'LEFT')
            ->select('plans.id', 'plans.name', 'plans.start_time', 'plans.min_order', 'plans.max_order', 'plans.speed', 'plans.price', 'plans.try_free', 'plans.description', DB::raw("IFNULL(avg(rating_reviews.rating), 0) as avg_rating"), DB::raw("COUNT(rating_reviews.id) as total_rating"))
            ->groupBy('plans.id');

        if ($request->search) {
            $servicesPricing->where('plans.name', 'LIKE', '%' . $request->search . '%');
        }
        if ($request->category) {
            $servicesPricing->where('plans.category_id', $request->category);
        }
        if ($request->service) {
            $servicesPricing->where('plans.service_id', $request->service);
        }

        $servicesPricing = $servicesPricing->get()->count();

        // dd($servicesPricing);

        // $categoryId = $servicesPricing->pluck('category_id')->toArray();
        // $categoryId = array_unique($categoryId);
        // $categories = [];

        $service_id_data_for_seo = '';
        if($s1)
        {
            $service_id_data_for_seo = Plan::where('slug',$s1)->first();
        }
        $categories = Category::get()->pluck('name', 'id')->toArray();
        $categories = Category::get()->pluck('name', 'slug')->toArray();
        $categyIcon = Category::get();


        $serviceID = Plan::select('*');
        if ($request->category) {
            $serviceID->where('category_id', $request->category);
        }
        $serviceID = $serviceID->get()->pluck('service_id')->toArray();

        $serviceID = array_unique($serviceID);
        $service = [];
        if ($serviceID) {
            $service = Service::whereIn('id', $serviceID)->get()->pluck('name', 'slug')->toArray();
        }
        $setting = Setting::first();
        $usdToInr = "";
        if ($setting) {
            $usdToInr = $setting->usd_to_inr;
        }

        $currancy = "";
        // $ipAddress = $request->ip();
        // $currentUserInfo = \Location::get($ipAddress);
        // if ($currentUserInfo) {
        //     $country = $currentUserInfo->countryName;

        //     if ($country == "India") {
        //         $currancy = "INR";
        //     } else {
        //         $currancy = "USD";
        //     }
        // }

        $faq = Faq::where(['category_id' => $request->category, 'service_id' => $request->service])->get();

        $servicePlanId = Plan::where(['category_id' => $request->category, 'service_id' => $request->service])->select('id')->pluck('id')->toArray();

        $reviews = RatingReview::whereIn('service_id', $servicePlanId)
            ->join('users', 'rating_reviews.user_id', '=', 'users.id', 'LEFT')
            ->select(
                'rating_reviews.review',
                'users.username',
                DB::raw('CASE
                WHEN rating_reviews.user_id IS NULL THEN rating_reviews.name
                ELSE users.username
                END AS username')
            )
            ->get();

        return view('category_wise_service', compact('servicesPricing', 'categories', 'service', 'categyIcon', 'usdToInr', 'currancy', 'serviceName', 'faq', 'reviews', 's1', 's2', '__catSlug', '__servicesSlug', 'service_id_data_for_seo'));
    }

    public function currencySessionChange(Request $request)
    {
        session()->put('currency', $request->currency);

        return response()->json($request->currency);
    }
    public function oldOrders(Request $request)
    {
        $email = Auth::user()->email;
        // $email = 'anil.d@openmedianetwork.in';
        // $email = 'baruahshraban@gmail.com';
        // $email = 'harneet.kgulati@gmail.com';

        $order1 = DB::table('old_website_seoplan')->join('old_user_traffic_order', 'old_website_seoplan.id', '=', 'old_user_traffic_order.plan_id')
            ->select('old_website_seoplan.name', 'old_user_traffic_order.prefix', 'old_user_traffic_order.id', 'old_user_traffic_order.date_time', 'old_user_traffic_order.url', 'old_user_traffic_order.amount', 'old_user_traffic_order.status', 'old_user_traffic_order.start_from', 'old_user_traffic_order.end_to', 'old_user_traffic_order.est_time', 'old_user_traffic_order.reason')
            ->where('old_user_traffic_order.email', $email);

        if ($request->status) {
            $order1 = $order1->where('old_user_traffic_order.status', $request->status);
        }

        if ($request->search) {
            $search = $request->search;
            $order1 = $order1->where(function ($query) use ($search) {
                $query->where('old_website_seoplan.name', 'Like', '%' . $search . '%');
                $query->orWhere('old_user_traffic_order.id', 'Like', '%' . $search . '%');
                $query->orWhere('old_user_traffic_order.date_time', 'Like', '%' . date('Y-m-d H:i:s', strtotime($search)) . '%');
                $query->orWhere('old_user_traffic_order.url', 'Like', '%' . $search . '%');
                $query->orWhere('old_user_traffic_order.amount', 'Like', '%' . $search . '%');
                $query->orWhere('old_user_traffic_order.status', 'Like', '%' . $search . '%');
                $query->orWhere('old_user_traffic_order.start_from', 'Like', '%' . $search . '%');
                $query->orWhere('old_user_traffic_order.end_to', 'Like', '%' . $search . '%');
                $query->orWhere('old_user_traffic_order.est_time', 'Like', '%' . date('Y-m-d', strtotime($search)) . '%');
            });
        }

        $order1 = $order1->orderBy('old_user_traffic_order.id', 'DESC')->get()->toArray();
        $order1 = json_decode(json_encode($order1), true);



        // $order1 = DB::select("SELECT old_website_seoplan.name, old_user_traffic_order.prefix, old_user_traffic_order.id, old_user_traffic_order.date_time, old_user_traffic_order.url,old_user_traffic_order.amount, old_user_traffic_order.status, old_user_traffic_order.start_from, old_user_traffic_order.end_to, old_user_traffic_order.est_time, old_user_traffic_order.reason FROM old_website_seoplan INNER JOIN old_user_traffic_order ON old_website_seoplan.id = old_user_traffic_order.plan_id where old_user_traffic_order.email='" . $email . "' order by old_user_traffic_order.id DESC");
        // $order1 = json_decode(json_encode($order1),true);

        $order2 = DB::table('old_website_seoplan')->join('old_traffic_order', 'old_website_seoplan.id', '=', 'old_traffic_order.subservice')
            ->where('old_traffic_order.email', $email)
            ->select('old_website_seoplan.name', 'old_traffic_order.*');

        if ($request->status) {
            $order2 = $order2->where('old_traffic_order.status', $request->status);
        }

        if ($request->search) {
            $search = $request->search;
            $order2 = $order2->where(function ($query) use ($search) {
                $query->where('old_website_seoplan.name', 'Like', '%' . $search . '%');
                $query->orWhere('old_traffic_order.id', 'Like', '%' . $search . '%');
                $query->orWhere('old_traffic_order.date_time', 'Like', '%' . date('Y-m-d H:i:s', strtotime($search)) . '%');
                $query->orWhere('old_traffic_order.url', 'Like', '%' . $search . '%');
                $query->orWhere('old_traffic_order.price', 'Like', '%' . $search . '%');
                $query->orWhere('old_traffic_order.status', 'Like', '%' . $search . '%');
                $query->orWhere('old_traffic_order.start_from', 'Like', '%' . $search . '%');
                $query->orWhere('old_traffic_order.end_to', 'Like', '%' . $search . '%');
                $query->orWhere('old_traffic_order.est_time', 'Like', '%' . date('Y-m-d', strtotime($search)) . '%');
                $query->orWhere('old_traffic_order.order_status', 'Like', '%' . $search . '%');
            });
        }

        $order2 = $order2->orderBy('old_traffic_order.id', 'desc')->get()->toArray();
        $order2 = json_decode(json_encode($order2), true);

        // $order2 = DB::select("SELECT old_website_seoplan.name, old_traffic_order.* FROM old_website_seoplan INNER JOIN old_traffic_order ON old_website_seoplan.id = old_traffic_order.subservice where old_traffic_order.email='" . $email . "' order by old_traffic_order.id DESC");
        // $order2 = json_decode(json_encode($order2),true);

        $order3 = DB::table('old_packages')->join('old_orders', 'old_packages.id', '=', 'old_orders.plan_id')->where('old_orders.email', $email)
            ->select('old_packages.name', 'old_orders.prefix', 'old_orders.order_id', 'old_orders.amount', 'old_orders.date_time', 'old_orders.status', 'old_orders.url', 'old_orders.start_from', 'old_orders.end_to', 'old_orders.est_time', 'old_orders.reason');

        if ($request->status) {
            $order3 = $order3->where('old_orders.status', $request->status);
        }

        if ($request->search) {
            $search = $request->search;
            $order3 = $order3->where(function ($query) use ($search) {
                $query->where('old_packages.name', 'Like', '%' . $search . '%');
                $query->orWhere('old_orders.order_id', 'Like', '%' . $search . '%');
                $query->orWhere('old_orders.date_time', 'Like', '%' . date('Y-m-d H:i:s', strtotime($search)) . '%');
                $query->orWhere('old_orders.url', 'Like', '%' . $search . '%');
                $query->orWhere('old_orders.amount', 'Like', '%' . $search . '%');
                $query->orWhere('old_orders.status', 'Like', '%' . $search . '%');
                $query->orWhere('old_orders.start_from', 'Like', '%' . $search . '%');
                $query->orWhere('old_orders.end_to', 'Like', '%' . $search . '%');
                $query->orWhere('old_orders.est_time', 'Like', '%' . date('Y-m-d', strtotime($search)) . '%');
            });
        }

        $order3 = $order3->orderBy('old_orders.order_id', 'desc')->get()->toArray();
        $order3 = json_decode(json_encode($order3), true);



        // $order3 = DB::select("SELECT old_packages.name, old_orders.prefix , old_orders.order_id,old_orders.amount, old_orders.date_time, old_orders.status, old_orders.url, old_orders.start_from, old_orders.end_to, old_orders.est_time, old_orders.reason FROM old_packages INNER JOIN old_orders ON old_packages.id = old_orders.plan_id where old_orders.email='" . $email . "' order by old_orders.order_id DESC");
        // $order3 = json_decode(json_encode($order3),true);

        $order4 = DB::table('old_packages')->leftJoin('old_payments', 'old_packages.id', '=', 'old_payments.packages_id')->where('old_payments.email', $email)
            ->select('old_packages.name', 'old_payments.id', 'old_payments.url', 'old_payments.order_status', 'old_payments.status', 'old_payments.amount', 'old_payments.txnid', 'old_payments.date', 'old_payments.start_from', 'old_payments.end_to', 'old_payments.est_time', 'old_payments.reason');

        if ($request->status) {
            $order4 = $order4->where('old_payments.status', $request->status);
        }


        if ($request->search) {
            $search = $request->search;
            $order4 = $order4->where(function ($query) use ($search) {
                $query->where('old_packages.name', 'Like', '%' . $search . '%');
                $query->orWhere('old_payments.id', 'Like', '%' . $search . '%');
                $query->orWhere('old_payments.date', 'Like', '%' . date('Y-m-d H:i:s', strtotime($search)) . '%');
                $query->orWhere('old_payments.url', 'Like', '%' . $search . '%');
                $query->orWhere('old_payments.amount', 'Like', '%' . $search . '%');
                $query->orWhere('old_payments.status', 'Like', '%' . $search . '%');
                $query->orWhere('old_payments.start_from', 'Like', '%' . $search . '%');
                $query->orWhere('old_payments.end_to', 'Like', '%' . $search . '%');
                $query->orWhere('old_payments.est_time', 'Like', '%' . date('Y-m-d', strtotime($search)) . '%');
                $query->orWhere('old_payments.order_status', 'Like', '%' . $search . '%');
            });
        }

        $order4 = $order4->orderBy('old_payments.id', 'desc')->get()->toArray();
        $order4 = json_decode(json_encode($order4), true);

        // echo "<pre>";
        // print_r($order4);
        // exit;

        // $order4 = DB::select("SELECT old_packages.name, old_payments.id, old_payments.url, old_payments.order_status, old_payments.status, old_payments.amount, old_payments.txnid, old_payments.date, old_payments.order_status, old_payments.start_from, old_payments.end_to, old_payments.est_time, old_payments.reason FROM old_packages LEFT JOIN old_payments ON old_packages.id = old_payments.packages_id where old_payments.email='" . $email . "' order by old_payments.id DESC");
        // $order4 = json_decode(json_encode($order4),true);




        $orders = array_merge($order1, $order2, $order3, $order4);
        $perpage = [
            10 => "10 Per Page",
            25 => "25 Per Page",
            50 => "50 Per Page",
            100 => "100 Per Page",
        ];

        $status = [
            'Pending' => 'Pending',
            'Progress' => 'Progress',
            'Completed' => 'Completed',
        ];

        return view('user.old_orders_history', compact('orders', 'perpage', 'status'));
    }

    public function getServicesAjax(Request $request)
    {
        $offset = $request->offset;
        $limit = $request->limit;
        $catId = '';
        if ($request->category) {
            $catId = Category::select('id')->where('slug', $request->category)->first()->toArray()['id'];
        }
        $serviceId ="";
        if($request->service)
        {
            $serviceId = Service::select('id', 'slug')->where('slug', $request->service)->first()->toArray()['id'];

        }
        $servicesPricing = Plan::join('rating_reviews', 'plans.id', '=', 'rating_reviews.service_id', 'LEFT')
            ->join('categories', 'plans.category_id', 'categories.id', 'LEFT')
            ->select('plans.id', 'plans.name', 'plans.start_time', 'plans.min_order', 'plans.max_order', 'plans.speed', 'plans.price', 'plans.try_free', 'plans.description', DB::raw("IFNULL(avg(rating_reviews.rating), 0) as avg_rating"), DB::raw("COUNT(rating_reviews.id) as total_rating"))
            ->groupBy('plans.id');
        if ($request->search) {
            $servicesPricing->where('plans.name', 'LIKE', '%' . $request->search . '%');
        }
        if ($catId) {
            $servicesPricing->where('plans.category_id', $catId);
        }
        if ($serviceId) {
            $servicesPricing->where('plans.service_id', $serviceId);
        }

        $servicesPricing = $servicesPricing->where('plans.active_or_deactivate', 'active');
        if ((isset($request->order) && $request->order) && (isset($request->order_type) && $request->order_type)) {
            $order = $request->order;
            $orderType = $request->order_type;
            $order_by = "";
            switch ($order) {
                case 'id':
                    $order_by = 'plans.id';
                    break;
                case 'service':
                    $order_by = 'plans.name';
                    break;
                case 'rated':
                    $order_by = 'avg_rating';
                    break;
                case 'start_time':
                    $order_by = 'plans.start_time';
                    break;
                case 'min_order':
                    $order_by = 'plans.min_order';
                    break;
                case 'max_order':
                    $order_by = 'plans.max_order';
                    break;
                case 'price':
                    $order_by = 'plans.price';
                    break;
                case 'speed':
                    $order_by = 'plans.speed';
                    break;
            }

            if ($order_by) {
                // $servicesPricing = $servicesPricing->orderBy('plans.try_free','desc');
                $servicesPricing = $servicesPricing->orderBy($order_by, $orderType);
            }
        } else {
            $servicesPricing = $servicesPricing->orderBy('plans.try_free', 'desc');
            $servicesPricing = $servicesPricing->orderBy('categories.sort_order');
        }

        $servicesPricing = $servicesPricing->where('plans.active_or_deactivate', '=', 'active')
            ->offset($offset)
            ->limit($limit)
            ->get();
        // if($servicesPricing)
        // {
        //     $servicesPricing = $servicesPricing->toArray();
        // }

        $dataHtml = "";
        if($servicesPricing)
        {
            foreach ($servicesPricing as $single) {
                $dataHtml .= '<tr>';

                $dataHtml .= '<td><strong class="ht">ID : </strong>'.$single->id.'</td>"';
                $dataHtml .= '<td><strong class="ht">Service : </strong>'.$single->name.'</td>"';
                $dataHtml .= '<td id="description_td_'.$single->id.'"><strong class="ht">Description : </strong><button onclick="show('.$single->id.')"
                class="button-border">show</button></td>"';
                $dataHtml .= '<td><strong class="ht">Rated : </strong>';
                    $fullStars = floor($single->avg_rating);
                    for ($i = 0; $i < $fullStars; $i++) {
                        $dataHtml .= '<i class="fas fa-star"></i>'; // Font Awesome full star icon
                    }

                    if ($single->avg_rating - $fullStars >= 0.25 && $single->avg_rating - $fullStars < 0.75) {
                        $dataHtml .= '<i class="fas fa-star-half-alt"></i>'; // Font Awesome half star icon
                    } elseif ($single->avg_rating - $fullStars >= 0.75) {
                        $dataHtml .= '<i class="far fa-star"></i>'; // Font Awesome empty star icon
                    }

                    $emptyStars = 5 - ceil($single->avg_rating);
                    for ($i = 0; $i < $emptyStars; $i++) {
                        $dataHtml .= '<i class="far fa-star"></i>'; // Font Awesome empty star icon
                    }
                $dataHtml .= '<p onclick="ratingReview('.$single->id.');">'.$single->total_rating.'
                Reviews</p>';
                $dataHtml .= '<td><strong class="ht">Start Time : </strong>'.$single->start_time.'</td>';
                $dataHtml .= '<td><strong class="ht">Min. Order : </strong>'.$single->min_order.'</td>';
                $dataHtml .= '<td><strong class="ht">Max Order : </strong>'.$single->max_order.'</td>';
                $dataHtml .= '<td><strong class="ht">Speed : </strong>'.$single->speed.'</td>';
                $dataHtml .= '<td><strong class="ht">Price per 1,000 : </strong>';
                $dataHtml .=    '<p class="usd-price">';
                $dataHtml .=        '$'.number_format((float) $single->price, 2, '.', '');
                $dataHtml .=    '</p>';
                $dataHtml .=    '<p class="inr-price" style="display: none;">';
                $dataHtml .=        '₹ ' . number_format((float) usdToInr($single->price) , 2, '.', '');
                $dataHtml .=    '</p>';
                $dataHtml .= '</td>';
                $dataHtml .= '<td class="justify-content-start">';
                if ($single->try_free == 1)
                {
                    $try_free_url = route('try_free_order', $single->id);
                    $dataHtml .= '<a href="'.$try_free_url .'" class="btn button button-border me-lg-0 me-2">Try Free</a>';
                }
                $url = route('place_order', $single->id);
                $dataHtml .= '<a href="'. $url .'" class="btn button">Create
                campaign</a>';
                $dataHtml .= '</td>';

                $dataHtml .= "</tr>";
                $dataHtml .= '<tr class="discription" id="description_'.$single->id.'" style="display:none;">';
                $dataHtml .= '<td class="w-100 d-block">'. $single->description .'</td>';
                $dataHtml .= '</tr>';
            }
        }


        return response($dataHtml);
    }
}
