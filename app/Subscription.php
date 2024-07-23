<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Subscription extends Model
{
    use HasFactory;

      public $table = 'subscriptions';
    public function getNameAttribute($details)
    {
        $res = '';
        if (!empty($details)) {
            $res = $details;
        }
        return $res;
    }
    public function getPriceAttribute($details)
    {
        $res = '';
        if (!empty($details)) {
            $res = $details;
        }
        return $res;
    }
    public function getDaysAttribute($details)
    {
        $res = '';
        if (!empty($details)) {
            $res = $details;
        }
        return $res;
    }
    public function getTypeAttribute($details)
    {
        $res = '';
        if (!empty($details)) {
            $res = $details;
        }
        return $res;
    }
    public function getDescriptionAttribute($details)
    {
        $res = '';
        if (!empty($details)) {
            $res = $details;
        }
        return $res;
    }
     public function fetchSubscription($request, $columns) {
        $query = Subscription::where('id',"!=",0)->where("status","!=","delete")->orderBy('created_at', 'desc');
        // dd($query);
         if (isset($request->from_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
        }
        if (isset($request->end_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
        }

        if (isset($request->name)) {
            $query->where(function ($q) use ($request) {
                
                $q->orWhere('name', 'like', '%' . $request->name . '%');
            });
        }
        if(isset($request->status)) {
            $query->where('status', $request->status);
        }
        if(isset($request->order_column)) {
            if($request->order_column == 5){
                 $cms = $query->orderBy('created_at', $request->order_dir);
            }else{
                $cms = $query->orderBy($columns[$request->order_column], $request->order_dir);
            } 
        }else {
            $cms = $query->orderBy('name', 'asc');
        }
   
        return $cms;
    }
    

    

     public function getImageAttribute($details)
    {
        if ($details != '') {
            return asset('img/subscriptionimages').'/'.$details;
        }
        return asset('images/no_avatar.jpg');
    } 
    public function getSubscriptionImageAttribute($details)
    {
         if ($details != '') {
            return asset('img/subscriptionimages').'/'.$details;
        }
        return asset('images/no_avatar.jpg');
    }

}
