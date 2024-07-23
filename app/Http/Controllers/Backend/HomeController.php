<?php

namespace App\Http\Controllers\Backend;

use App\Library\Helper;
use App\Library\Notify;    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\SplashScreen;
use Spatie\Permission\Models\Role;
use DB;
use Auth;
use Hash;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use App\Country;
use App\City;
use App\State;
use App\Region;
use App\Category;
use App\Transaction;
use App\ContestParticipant;
use App\Gift;
use App\BoxPrice;
use App\Offer;
use App\Item;
use App\Vouchar;
use App\VendorProduct;
use App\Brand;
use App\VendorAttribute;
use App\VendorItem;
use App\ItemCategory;
use App\ItemIngredient;
use App\VendorCustomer;
use App\VendorSalePerson;
use App\UserSubscription;
use App\RestaurantOrderItem;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request) {
        if((isset(Auth::user()->id))){
            if(Auth::user()->is_admin == 'Yes') {
                $message = '';
                $data_query = User::select(\DB::raw("COUNT(*) as count"), \DB::raw("DAYNAME(created_at) as day_name"), \DB::raw("DAY(created_at) as day"))->where('status', '!=', 'delete')->where("role","Customer")->where('is_admin', 'No')->where('id', '!=', 1);
                    
                $data_vendor_query = User::select(\DB::raw("COUNT(*) as count"), \DB::raw("DAYNAME(created_at) as day_name"), \DB::raw("DAY(created_at) as day"))->where('status', '!=', 'delete')->where("role","Vendor")->where('is_admin', 'No')->where('id', '!=', 1); 

                $total_transaction = 0;
                $total_splash_screen = 0;
                $total_user = 0;
                $total_user_query = User::where('status', '!=', 'delete')->where('is_admin', 'No')->where('id', '!=', 1)->where('role','User')->orderBy('id', 'desc')->limit(5)->get();
                
                $total_vendor_query = User::where('status', '!=', 'delete')->where('is_admin', 'No')->where('id', '!=', 1)->where('role','Vendor')->orderBy('id', 'desc')->limit(5)->get();
        
                $last_transtion = Transaction::where('status', '!=', 'delete')->orderBy('id', 'desc')->limit(5)->get();
                
            
                $total_male = User::where(['is_admin'=>'No','gender'=>"Male"])->where("status","!=","delete")->count();
                $total_female = User::where(['is_admin'=>'No','gender'=>"female"])->where("status","!=","delete")->count();
            
                if (isset($request->created_at) && !empty($request->created_at)) {
                    if ($request->created_at == 'custom') {
                        if (isset($request->from_date) && !empty($request->from_date)) {
                            $total_user_query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
                            $data_query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
                            $data_article_query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
                            $data_vendor_query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
                        }
                        if (isset($request->end_date) && !empty($request->end_date)) {
                            $total_user_query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
                        }
                        if (!empty($request->from_date) && empty($request->end_date)) {
                            $message = date("d-m-Y", strtotime($request->from_date)).' after added records';
                        }
                        if (empty($request->from_date) && !empty($request->end_date)) {
                            $message = date("d-m-Y", strtotime($request->end_date)).' before added records';
                        }
                        if (!empty($request->from_date) && !empty($request->end_date)) {
                            $message = date("d-m-Y", strtotime($request->from_date)).' to '.date("d-m-Y", strtotime($request->end_date)).' date added records';
                        }
                    }else{
                        if ($request->created_at == 'week') {

                            $total_user_query->where('created_at', '>', Carbon::today()->subDay(6));
                            $data_query->where('created_at', '>', Carbon::today()->subDay(6));
                            $data_vendor_query->where('created_at', '>', Carbon::today()->subDay(6));
                            $message = 'Last 7 Days added records';
                        }else if ($request->created_at == 'month') {

                            $total_user_query->where('created_at', '>', Carbon::today()->subDay(29));
                            $data_query->where('created_at', '>', Carbon::today()->subDay(29));
                            $data_vendor_query->where('created_at', '>', Carbon::today()->subDay(29));
                            $message = 'Last 30 Days added records';
                        }else if ($request->created_at == 'day') {
                            $total_user_query->whereDay('created_at', date('d'));
                            $data_query->whereDay('created_at', date('d'));
                            $data_vendor_query->whereDay('created_at', date('d'));
                            $message = 'Last 24 Hours added records';
                        }else{
                            $total_user_query->whereYear('created_at', date('Y'));
                            $total_splash_screen_query->whereYear('created_at', date('Y'));
                            $data_query->whereYear('created_at', date('Y'));
                            $data_vendor_query->whereYear('created_at', date('Y'));
                            $message = 'Last 365 Days added records';

                            $total_user_query->where('created_at', '>', Carbon::today()->subDay(364));
                            $data_query->where('created_at', '>', Carbon::today()->subDay(364));
                            $data_vendor_query->where('created_at', '>', Carbon::today()->subDay(364));
                            $message = 'Last 365 Days added records';
                        }
                    }
                }else{
                    $data_query->where('created_at', '>', Carbon::today()->subDay(6));
                    $message = 'Last 7 Days added records';
                }

                $array[] = ['Name', 'Number'];
                $data = $data_query->groupBy('day_name','day')->orderBy('day')->get();
                    foreach($data as $key => $value)
                    {
                        $array[++$key] = [$value->day_name, $value->count];
                    }
                $users = json_encode($array);
                
                $data_vendor = $data_vendor_query->groupBy('day_name','day')->orderBy('day')->get(); 
                    $array_vendor[] = ['Name', 'Number'];
                    foreach($data_vendor as $key_vendor => $value_vendor)
                    {
                        $array_vendor[++$key_vendor] = [$value_vendor->day_name, $value_vendor->count];
                    }
                    
                $vendors = json_encode($array_vendor);

                $total_user = User::where('is_admin', 'No')->where('id', '!=', 1)->where("status","!=","delete")->where("role","Customer")->count();
                $total_country =Country::where("status","!=","delete")->count();
                $total_state =State::where("status","!=","delete")->count();
                $total_city =City::where("status","!=","delete")->count();
                $total_region = Region::where("status","!=","delete")->count();
                $articles="";
                $total_vendor =User::where("status","!=","delete")->where("role","Vendor")->count();
                $total_item =VendorProduct::where("status","!=","delete")->count();
                $total_offer =Offer::where("status","!=","delete")->count();
                $total_voucher =Vouchar::where("status","!=","delete")->count();
                $total_transaction = Transaction::where("status","!=","delete")->sum('amount');
                $total_category =Category::where("status","!=","delete")->count();
                $total_brand =Brand::where("status","!=","delete")->count();
                $total_attribute =VendorAttribute::where("status","!=","delete")->count();

                return view('backend.dashboard', compact('users', 'total_user', 'vendors', 'articles', 'message', 'total_splash_screen',
                'total_country','total_state','total_city','total_region','total_male','total_female','total_user_query','last_transtion',
                'total_transaction','total_vendor','total_item','total_offer','total_vendor_query','total_voucher','total_category',
                'total_brand','total_attribute'));
                
            }elseif(Auth::user()->role == 'Vendor') {
               
                $total_category =Category::where("status","!=","delete")->count();
                $total_salesperson =VendorSalePerson::where("status","!=","delete")->where('vendor_id',Auth::user()->id)->count();
                $total_attribute =VendorAttribute::where("status","!=","delete")->where('vendor_id',Auth::user()->id)->count();
                $total_item =VendorProduct::where("status","!=","delete")->where('vendor_id',Auth::user()->id)->count();
                
                $users = User::where('status', 'active')->where('id',Auth::user()->id)->first();
                if (isset($users->id) && !empty($users)) {
                    $total_visitor = VendorCustomer::where('vendor_id', $users->id)->where('type', 'Visitor')->count();
                    $total_purchase = VendorCustomer::where('vendor_id', $users->id)->where('type', 'Purchase')->count();
                    $total_repet_order = VendorCustomer::where('vendor_id', $users->id)->where('type', 'Repeat')->count();
                    $total_receipt = VendorCustomer::where('vendor_id', $users->id)->where('type', 'Invoice')->count();

                    $total_sales = RestaurantOrderItem::where('vendor_id', $users->id)->sum('total_amount');

                }

                // $vendorcustomer = VendorCustomer::select('id', 'vendor_id', 'customer_id', 'type', 'category_id', 'feedback', DB::raw("DATE_FORMAT(created_at,'%b %d, %Y') as created_at"))->where('vendor_id', Auth::user()->id)
                //                 ->with([
                //                     'customer' => function ($q) {
                //                         $q->select('id', 'name', 'phone_code', 'mobile');
                //                     },
                //                     'vendor' => function ($q) {
                //                         $q->select('id', 'name');
                //                     },
                //                     'category' => function ($q) {
                //                         $q->select('id', 'name'); 
                //                     }
                //                 ])
                //                 ->orderBy('id','desc')
                //                 ->take(5)->get();
                
                 $vendorcustomer = VendorCustomer::select('id', 'vendor_id', 'customer_id', 'type', 'category_id', 'feedback', DB::raw("DATE_FORMAT(created_at,'%b %d, %Y') as created_at"))->where('vendor_id', Auth::user()->id)
                                ->with([
                                    'customer' => function ($q) {
                                        $q->select('id', 'name', 'phone_code', 'mobile');
                                    },
                                    'vendor' => function ($q) {
                                        $q->select('id', 'name');
                                    },
                                    'restaurantcategory' => function ($q) {
                                        $q->select('id', 'name'); 
                                    }
                                ])
                                ->orderBy('id','desc')
                                ->take(5)->get();
                $auth_user = Auth::user();            
                return view('backend.vendordashboard',compact('total_visitor',
                'total_purchase','total_repet_order','total_receipt','total_category','total_salesperson','total_attribute','total_item',
                'vendorcustomer', 'auth_user','total_sales'));
            } 
        }else {
            // Redirect to login
            return redirect()->route('login');
        }
    }
    public function changePassword(Request $request) {
    if ($request->isMethod('post')) {
        $user = Auth::user();
        if ($user) {
            if (Hash::check($request->input('old-pass'), $user->password)) {
                if ($request->input('pass') != $request->input('old-pass')) {
                    if ($request->input('pass') == $request->input('confirm-pass')) {
                        $user->password = Hash::make($request->input('pass'));
                        if ($user->save()) {
                            Auth::logout(); // Logout the user after changing the password.
                            $request->session()->flash('success', 'Password changed successfully. Please login again.');
                            return redirect('/admin');
                        } else {
                            $request->session()->flash('error', 'Password not changed! Try again later.');
                            return view('backend.changepassword');
                        }
                    } else {
                        $request->session()->flash('error', 'Passwords do not match.');
                        return view('backend.changepassword');
                    }
                } else {
                    $request->session()->flash('error', 'Please add diffrent password old password and new password same.');
                    return view('backend.changepassword');
                }
            } else {
                $request->session()->flash('error', 'Old Passwords do not match.');
                return view('backend.changepassword');
            }
        } else {
            $request->session()->flash('error', 'Access Denied');
            return view('backend.changepassword');
        }
    }

    $user = Auth::user();
    if ($user) {
        return view('backend.changepassword');
    } else {
        $request->session()->flash('error', 'Access Denied');
        return redirect('/login');
    }
}

    // Income chart
    public function incomeChart(Request $request){
        $year=\Carbon\Carbon::now()->year;
        $items = array();
        $items = Transaction::whereYear('created_at',$year)->get()
            ->groupBy(function($d){
                return \Carbon\Carbon::parse($d->created_at)->format('m');
            });
        $result=[];
        foreach($items as $month=>$item_collections){
            foreach($item_collections as $item){
                $amount=$item->amount;
                $m=intval($month);
                isset($result[$m]) ? $result[$m] += $amount :$result[$m]=$amount;
            }
        }
        // dd($result);
        $data=[];
        for($i=1; $i <=12; $i++){
            $monthName=date('F', mktime(0,0,0,$i,1));
            $data[$monthName] = (!empty($result[$i]))? number_format((float)($result[$i]), 2, '.', '') : 0.0;
        }
        return $data;


    }



}
