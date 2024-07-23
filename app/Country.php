<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Country extends Model
{
    use HasFactory;
 public $table = 'countries';
    public function getNameAttribute($details)
    {
        $res = '';
        if (!empty($details)) {
            $res = $details;
        }
        return $res;
    }

    public function fetchCountry($request, $columns) {
        $query = Country::where('id',"!=",0)->where("status","!=","delete");
         if (isset($request->from_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
        }
        if (isset($request->end_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
        }
        
        if (isset($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->orWhere('name', 'like', '%' . $request->search . '%');
            });
        }
        if (isset($request->status)) {
            $query->where('status', $request->status);
        }
       if (isset($request->order_column)) {
            if($request->order_column == 5){
                 $cms = $query->orderBy('created_at', $request->order_dir);
            }else{
                $cms = $query->orderBy($columns[$request->order_column], $request->order_dir);
            } 
        }  else {
            $cms = $query->orderBy('name', 'asc');
        }
        return $cms;
    }

}
