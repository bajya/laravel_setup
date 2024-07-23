<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\State;
class State extends Model
{
    use HasFactory;
 public $table = 'states';
    public function getNameAttribute($details)
    {
        $res = '';
        if (!empty($details)) {
            $res = $details;
        }
        return $res;
    }

     public function getCountry() {
        return $this->belongsTo(Country::class, 'country_id', 'id')->where('status','!=', 'delete'); 
    }

    public function fetchStates($request, $columns) {
      
        $query = State::where('id',"!=",0)->where("status","!=","delete");

        if (isset($request->from_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
        }
        if (isset($request->end_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
        }

        if (isset($request->search) && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->orWhere('name', 'like', '%' . $request->search . '%');
                 
            });
        }
        if (isset($request->status)) {
            $query->where('status', $request->status);
        }
        
        if (isset($request->order_column)) {
            if($request->order_column == 4){
                 $cms = $query->orderBy('created_at', $request->order_dir);
            }else{
                $cms = $query->orderBy($columns[$request->order_column], $request->order_dir);
            } 
        } else {
            $cms = $query->orderBy('created_at', 'desc');
        }
        return $cms;
    }
}
