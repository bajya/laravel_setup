<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

use App\User;
use App\Message;
use App\Notificationuser;
use App\CMS;
use App\Article;
use App\Push;
use App\AdminSettings;
use App\Retailer;
use App\Testimonial;
use App\Vouchar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Pusher\Pusher;
use Carbon\Carbon;
use App\ContestParticipant;
use App\CheckMonthlyNotification;
use App\RestaurantOrderItem;
// use Auth;
use Illuminate\Support\Facades\Storage;


class KitchenController extends Controller
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
    public function index()
    {
        if((isset(Auth::user()->id))){
            
            $kitchen_orderlist = RestaurantOrderItem::with(['Orders','restaurantOrderItemIngredients' => function ($q1) {
		        $q1->select('id', 'restaurant_order_item_id','ingredient_id', 'amount')->with(['ingredient' => function ($q1) {
		            $q1->select('id', 'name','price');
		        }]);
		    }])
            ->select('restaurant_order_items.*', 'restaurant_order_items.status')
            ->where('status','!=','Done')
            ->get();
           
            return view('frontend.kitchen.index',compact('kitchen_orderlist'));
        }else {
            return redirect()->route('login');
        }
    }

    public function kitchenupdateStatus(Request $request) {

        $orderId = $request->input('id');
        $status = $request->input('status');

        // Update status in restaurant_order_items table
        $kitchen_orderlist = RestaurantOrderItem::find($orderId);

        if (!$kitchen_orderlist) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $kitchen_orderlist->status = $status;
        $kitchen_orderlist->save();

        // Update status in restaurant_orders table
        $restaurantOrder = $kitchen_orderlist->Orders;

        if (!$restaurantOrder) {
            return response()->json(['error' => 'Order not found in restaurant_orders table'], 404);
        }

        $restaurantOrder->status = $status;
        $restaurantOrder->save();

        return response()->json(['message' => 'Status updated successfully']);
    }




}
