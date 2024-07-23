<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

        public function fetchBanners($request, $columns) {
        $query = Banner::where('status', '!=', 'delete');
        if (isset($request->from_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
        }
        if (isset($request->end_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
        }
        if (isset($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }
        if (isset($request->name)) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->name . '%');
            });
        }
        if (isset($request->status)) {
            $query->where('status', $request->status);
        }
        if (isset($request->order_column)) {
            if($request->order_column == 5){
                 $Brands = $query->orderBy('created_at', $request->order_dir);
            }else{
                $Brands = $query->orderBy($columns[$request->order_column], $request->order_dir);
            } 
        } else {
          $Brands = $query->orderBy('created_at', 'desc');
        }

        return $Brands;
    }    
    public function getNameAttribute($details)
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
    public function getCreateDateAttribute($details)
    {
        $res = '';
        if (!empty($details)) {
            $res = date('M d, Y', strtotime($details));

        }
        return $res;
    }
    public function getImageAttribute($details)
    {
        if ($details != '') {
            //if (@getimagesize($details)) {
                return asset('uploads/banners').'/'.$details;
            //}
        }
        return asset('images/blank.png');
    }

    
}
