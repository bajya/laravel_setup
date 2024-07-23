<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;
use DB;

class Transaction extends Model
{
    use HasFactory;

     public $table = 'transactions';

      public function getNameAttribute($details)
    {
        $res = '';
        if (!empty($details)) {
            $res = $details;
        }
        return $res;
    }

     public function fetchTransactions($request, $columns) {
        $query = Transaction::where('status', '!=', 'delete');
        if (isset($request->from_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
        }
        if (isset($request->end_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
        }
        if (isset($request->search)) {
            $query->where(function ($q) use ($request) {
		        $q->where('transaction_no', 'like', '%' . $request->search . '%');
	            $q->orWhere('payment_type', 'like', '%' . $request->search . '%');
	            $q->orWhere('title', 'like', '%' . $request->search . '%');
	            $q->orWhere('amount', 'like', '%' . $request->search . '%');
            });
        }
        
        if (isset($request->status)) {
            $query->where('status', $request->status);
        }
         if (isset($request->order_column)) {
            if($request->order_column == 7){
                 $Brands = $query->orderBy('created_at', $request->order_dir);
            }else{
                $Brands = $query->orderBy($columns[$request->order_column], $request->order_dir);
            } 
        } else {
          $Brands = $query->orderBy('created_at', 'desc');
        }

        return $Brands;
    } 
    
     public function fetchvendorTransactions($request, $columns) {
        $users = Auth::id();
        $query = Transaction::where('status', '!=', 'delete');
        
        if (isset($request->from_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
        }
        if (isset($request->end_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
        }
        if (isset($request->search)) {
            $query->where(function ($q) use ($request) {
		        $q->where('transaction_no', 'like', '%' . $request->search . '%');
	            $q->orWhere('payment_type', 'like', '%' . $request->search . '%');
	            $q->orWhere('title', 'like', '%' . $request->search . '%');
	            $q->orWhere('amount', 'like', '%' . $request->search . '%');
            });
        }
        
        if (isset($request->status)) {
            $query->where('status', $request->status);
        }
        //  if (isset($request->order_column)) {
        //     if($request->order_column == 7){
        //          $Brands = $query->orderBy('created_at', $request->order_dir);
        //     }else{
        //         $Brands = $query->orderBy($columns[$request->order_column], $request->order_dir);
        //     } 
        // } else {
        //   $Brands = $query->orderBy('created_at', 'desc');
        // }

        $users = $query->where('user_id', $users)->where('status', '!=', 'delete')->orderBy('id', 'desc');
		
			foreach($users as $key=>$value){
				if ($value->type == 'subscription') {
					$value->order = UserSubscription::select('id', 'name', 'price', 'days', 'type', 'description', DB::raw("DATE_FORMAT(start_date,'%b %d, %Y') as start_date"), DB::raw("DATE_FORMAT(end_date,'%b %d, %Y') as end_date"), DB::raw("DATE_FORMAT(created_at,'%b %d, %Y') as created_date"))->where('status', 'active')->where('id', $value->order_id)->first();
				}else{
					$value->order = '';
				}
			}
		
		return $users;

        // return $Brands;
    }

    public function getUser() {
        return $this->belongsTo(User::class, "user_id", "id");
    }

   

}
