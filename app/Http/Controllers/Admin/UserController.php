<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Arr;
use Hash;
use Auth;
use App\Models\PaymentHistory;
use App\Models\Plan;
use App\Models\Order;
use App\Models\CustomRate;
use DB;
use App\Exports\ExportUser;
use App\Exports\UserExport;
use App\Models\Billing;
use App\Models\Setting;
use Maatwebsite\Excel\Facades\Excel;
use Mail;

class UserController extends Controller
{
    function __construct()
    {
        // $this->middleware('admin', ['only' => ['index']]);
    }
    public function index(Request $request)
    {
        // $users = User::where('user_type',0)->paginate(10);
        $users = new User;
        $users = $users->where('user_type',0)->select('users.*');
        $perpage = $request->perpage ? $request->perpage : 10;
        if($request->search)
        {
            // DB::raw('count(custom_rates.id) as count')
            $users = $users->where('users.id', 'like', '%' .$request->search . '%')->orWhere('users.username', 'like', '%' .$request->search . '%')->orWhere('users.email', 'like', '%' .$request->search . '%')->orWhere('users.phone','like', '%' .$request->search . '%')->orWhere('users.phone','like', '%' .$request->search . '%')->orWhere('users.amount','like', '%' .$request->search . '%')->orWhere('users.created_at','like', '%' .date('Y-m-d',strtotime($request->search)). '%')->orWhere('users.last_login','like', '%' .date('Y-m-d',strtotime($request->search)) . '%')->orWhere('users.last_login','like', '%' .$request->search . '%');

            if(strtolower($request->search) == 'active' || strtolower($request->search) == 'deactive')
            {
                $request->search = strtolower($request->search) == 'active' ? 'Activate' : 'Deactivate';
                $users = $users->orWhere('users.status','like', '%' .$request->search . '%');
            }
        }
        $users = $users->groupBy('users.id');
        if((isset($request->order) && $request->order) && (isset($request->orderType) && $request->orderType))
        {
            $order = $request->order;
            $orderType = $request->orderType;
            $order_by = "";
            switch ($order)
            {
                case 'no':
                    $order_by = 'users.id';
                    break;
                case 'username':
                    $order_by = 'users.username';
                    break;
                case 'email':
                    $order_by = 'users.email';
                    break;
                case 'phone':
                    $order_by = 'users.phone';
                    break;
                case 'status':
                    $order_by = 'users.status';
                    break;
                case 'current_balance':
                    $order_by = 'users.amount';
                    break;
                case 'created_at':
                    $order_by = 'users.created_at';
                    break;
                case 'last_login':
                    $order_by = 'users.last_login';
                    break;
                case 'count':
                    $order_by = 'count';
                    break;
                case 'spent':
                    $order_by = 'users.spent';
                    break;
                default:
                    $order_by = 'users.id';
                    break;
            }
            // dd($orderType);
            if($order_by)
            {
                $users = $users->orderBy($order_by, $orderType);
            }
        }
        $users = $users->paginate($perpage);


        return view('admin.users.index',compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|unique:users,email',
            'phone' => 'required',
            'password' => 'required', //|min:6|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/
            'confirm_password' => 'required|same:password',
            'username' => 'required',
            'status' => 'required',
        ]);
        $input = $request->all();

        $input['email_verified_at'] = now();

        if($request->password)
        {
            $input['password'] = Hash::make($request->password);
        }
        $user = User::create($input);
        return redirect()->route('admin.users.index')
                        ->with('User create successfully');
    }

    public function edit($id)
    {
        $user = User::find($id);
        return view('admin.users.edit',compact('user'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'email' => 'required|unique:users,email,'.$id,
            'username' => 'required',
            'phone' => 'required',
        ]);

        $input = $request->all();
        if(isset($input['password']) && $input['password'])
        {
            $input['password'] = Hash::make($input['password']);
        }
        else
        {
            unset($input['password']);
        }

        $input['email_verified_at'] = now();

        $user = User::find($id);

        $user->update($input);

        return redirect()->route('admin.users.index')
                        ->with('User update successfully');

    }

    public function destroy($id)
    {
        $user = User::find($id);
        if($user)
        {
            $user->delete($user);
            return redirect()->route('admin.users.index')
                            ->with('success','User delete successfully');
        }
        else {
            return redirect()->route('admin.user.index')
                    ->with('success', 'Something went wrong');
        }
    }

    public function myProfile()
    {
        $userId = Auth::guard('admin')->id();
        $user = User::find($userId);
        return view('admin.my_profile',compact('user'));
    }

    public function profileUpdate(Request $request,$id)
    {
        $this->validate($request, [
            'email' => 'required|unique:users,email,'.$id,
            'phone' => 'required',
            'username' => 'required',
        ]);

        $input = $request->all();
        $user = User::find($id);
        if($request->password && $user->password != $request->password)
        {
            $input['password'] = Hash::make($request->password);
        }
        $user->update($input);
        return redirect()->route('admin.my_profile')
            ->with('success', "Profile update sucessfully");
    }

    public function Export(Request $request)
    {
        ini_set('memory_limit', '-1');
        set_time_limit(1800);
        $search = "";
        if(isset($request->search))
        {
            $search = $request->search;
        }
        return Excel::download(new UserExport( $search), 'users.xlsx',\Maatwebsite\Excel\Excel::XLSX);
        // jp code end
    }

    public function depositHistory(Request $request, $id)
    {
        $perpage = [
            10=>"10 Per Page",
            25=>"25 Per Page",
            50=>"50 Per Page",
            100=>"100 Per Page",
        ];

        $page = 10;
        if(isset($request->perpage) && $request->perpage && is_numeric($request->perpage))
        {
            $page = $request->perpage;
        }

        $payment_histories = PaymentHistory::join('users','payment_histories.user_id','=','users.id')
        ->select('users.username','users.email as email','payment_histories.*')->where('user_id',$id);
        // ->where('status','=','Success');
        if(isset($request->start_date) && $request->start_date && isset($request->end_date) && $request->end_date)
        {
            if($request->start_date == $request->end_date)
            {
                $payment_histories = $payment_histories->whereDate('payment_histories.created_at',$request->start_date);
            }
            else
            {
                $payment_histories = $payment_histories->whereBetween('payment_histories.created_at',[$request->start_date,$request->end_date]);
            }
        }
        if(isset($request->search) && $request->search)
        {
            $search = $request->search;
            $payment_histories = $payment_histories->where(function($query) use($search){
                $query->where('payment_histories.created_at','LIKE','%'.$search.'%')
                    ->orwhere('users.email','LIKE','%'.$search.'%')
                    ->orwhere('payment_histories.bonus','LIKE','%'.$search.'%')
                    ->orwhere('payment_histories.amount','LIKE','%'.$search.'%')
                    ->orwhere('payment_histories.status','LIKE','%'.$search.'%');
            });
        }

        if((isset($request->order) && $request->order) && (isset($request->orderType) && $request->orderType))
        {
            $order = $request->order;
            $orderType = $request->orderType;
            $order_by = "";
            switch ($order)
            {
                case 'id':
                    $order_by = 'payment_histories.id';
                    break;
                case 'date':
                    $order_by = 'payment_histories.created_at';
                    break;
                case 'total':
                    $order_by = 'payment_histories.amount';
                    break;
                case 'bonus':
                    $order_by = 'payment_histories.bonus';
                    break;
                case 'email':
                    $order_by = 'users.email';
                    break;
            }

            if($order_by)
            {
                $payment_histories = $payment_histories->orderBy($order_by, $orderType);
            }
        }
        else
        {
            $payment_histories = $payment_histories->orderBy('payment_histories.id','DESC');
        }


        $payment_histories = $payment_histories->paginate($page);

        return view('admin.users.deposit_history',compact('perpage','payment_histories'));
    }

    public function spent()
    {
        $Alluser = User::select('*')->get();
        foreach($Alluser as $single)
        {
            $userId = $single->id;
            $user = User::find($userId);

            $setting = Setting::first();
            $conversionRate = $setting->usd_to_inr;
            $lifetimeAmount = Order::select(DB::raw("ROUND(SUM(CASE WHEN orders.currency_type = 'INR' THEN orders.total_cost / $conversionRate ELSE orders.total_cost END),2) AS total_cost"))
            ->where('user_id', $userId)
            ->where('orders.is_free', 0)
            ->groupBy('user_id')->first();

            if(isset($lifetimeAmount->total_cost) && $lifetimeAmount->total_cost && $lifetimeAmount->total_cost != 0)
            {
                $user->update(['spent' => $lifetimeAmount->total_cost]);
            }
        }
    }
}
