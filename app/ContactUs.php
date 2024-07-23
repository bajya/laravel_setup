<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactUs extends Model
{
    use HasFactory;
    
    public $table = 'contact_us';
    public function getNameAttribute($details)
    {
        $res = '';
        if (!empty($details)) {
            $res = $details;
        }
        return $res;
    }

    public function fetchContactUs($request, $columns) {
        $query = ContactUs::where('status', 'active');
        if (isset($request->search) && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->orWhere('name', 'like', '%' . $request->search . '%');
            });
        }

        if (isset($request->order_column)) {
            if($request->order_column == 6){
                 $ContactUs = $query->orderBy('created_at', $request->order_dir);
            }else{
                $ContactUs = $query->orderBy($columns[$request->order_column], $request->order_dir);
            } 
        } else {
            $ContactUs = $query->orderBy('created_at', 'desc');
        }
        return $ContactUs;
    }
}
