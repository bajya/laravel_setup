<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Region extends Model
{
    use HasFactory;
    
    public function getNameAttribute($details)
    {
        $res = '';
        if (!empty($details)) {
            $res = $details;
        }
        return $res;
    }
    public function getCountry() {
        return $this->belongsTo(Country::class, 'country_id', 'id')->where('status',"!=",'delete'); 
    }
    public function getState() {
        return $this->belongsTo(State::class, 'state_id', 'id')->where('status',"!=",'delete'); 
    }
    public function getCity() {
        return $this->belongsTo(City::class, 'city_id', 'id')->where('status',"!=",'delete'); 
    }
    public function fetchRegion($request, $columns) {
        $query = Region::where('id',"!=",0)->where("status","!=","delete");
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
            if($request->order_column == 6){
                 $city = $query->orderBy('created_at', $request->order_dir);
            }else{
                $city = $query->orderBy($columns[$request->order_column], $request->order_dir);
            } 
        } else {
            $city = $query->orderBy('created_at', 'desc');
        }
        return $city;
    }
}
