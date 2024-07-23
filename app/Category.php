<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    public $table = 'categories';
    public function getNameAttribute($details)
    {
        $res = '';
        if (!empty($details)) {
            $res = $details;
        }
        return $res;
    }
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
    public function getImageAttribute($details)
    {
         if ($details != '') {
            return asset('img/categories').'/'.$details;
        }
        return asset('images/no_avatar.jpg');
    }
     public function fetchCategory($request, $columns) {
        $query = Category::where('id',"!=",0)->where("status","!=","delete");
        // dd($query);
         if (isset($request->from_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
        }
        if (isset($request->end_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
        }
        
        if (isset($request->name)) {
            // $query->where('name', $request->name);
            $query->where(function ($q) use ($request) {
                
                $q->orWhere('name', 'like', '%' . $request->name . '%');
            });
        }
        if (isset($request->status)) {
            $query->where('status', $request->status);
        }
         if (isset($request->order_column)) {
            if($request->order_column == 3){
                 $cms = $query->orderBy('created_at', $request->order_dir);
            }else{
                $cms = $query->orderBy($columns[$request->order_column], $request->order_dir);
            } 
        }
        else {
            $cms = $query->orderBy('name', 'asc');
        }
   
        return $cms;
    }

     public function users()
    {
        return $this->belongsToMany(User::class, 'store_category');
    }
}
