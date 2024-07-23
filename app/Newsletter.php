<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Newsletter extends Model
{
    use HasFactory;
    
    public $table = 'newsletters';
    public function getEmailAttribute($details)
    {
        $res = '';
        if (!empty($details)) {
            $res = $details;
        }
        return $res;
    }
    public function fetchNewsletter($request, $columns) {
        $query = Newsletter::where('status', 'active');
        if (isset($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if (isset($request->order_column)) {
            if($request->order_column == 4){
                 $Newsletter = $query->orderBy('created_at', $request->order_dir);
            }else{
                $Newsletter = $query->orderBy($columns[$request->order_column], $request->order_dir);
            } 
        } else {
            $Newsletter = $query->orderBy('created_at', 'desc');
        }
        return $Newsletter;
    }
}
