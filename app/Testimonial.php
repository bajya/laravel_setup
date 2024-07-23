<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;
    protected $table = 'testimonials';

    public function fetchTestimonials($request, $columns) {
        
        $query = Testimonial::where('status', '!=', 'delete');

        if (isset($request->from_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
        }
        if (isset($request->end_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
        }
        if (isset($request->search) && !empty($request->search)) {
            /*$query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
                $q->orWhere('last_name', 'like', '%' . $request->search . '%');
                $q->orWhere('email', 'like', '%' . $request->search . '%');
                $q->orWhere('mobile',  $request->search);
            });*/

            $string = $request->search;

            $query->where(function ($q) use ($string) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower(trim($string)) . '%'])
                    ->orWhereRaw('LOWER(designation) LIKE ?', ['%' . strtolower(trim($string)) . '%'])
                    ->orWhere('company_name', 'ILIKE', '%' . trim($string) . '%') // ILIKE for case-insensitive search
                    ->orWhere('description', 'LIKE', '%' . trim($string) . '%');
            });
        }
        if (isset($request->status)) {
            $query->where('status', $request->status);
        }
         if (isset($request->order_column)) {
            if($request->order_column == 7){
                 $users = $query->orderBy('created_at', $request->order_dir);
            }else{
                $users = $query->orderBy($columns[$request->order_column], $request->order_dir);
            } 
        } else {
          $users = $query->orderBy('created_at', 'desc');
        }

        
        return $users;
    }

    public function getImageAttribute($details)
    {
         if ($details != '') {
            return asset('uploads/testimonials').'/'.$details;
        }
        return asset('images/no_avatar.jpg');
    }

}
