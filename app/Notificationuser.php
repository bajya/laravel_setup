<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificationuser extends Model
{
    use HasFactory;
    protected $table = 'notification_users';
    protected $fillable=['sender_id','receiver_id','title','description','notification_type','is_read','status','created_at','updated_at'];


    public function user()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

     public function fetchNotifications($request, $columns) {
        $query =  Notificationuser::where("status","!=","delete");
       
         if (isset($request->from_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
        }
        if (isset($request->end_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
        }
        if (isset($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }
        if (isset($request->order_column)) {
            $users = $query->orderBy($columns[$request->order_column], $request->order_dir);
        } else {
            $users = $query->orderBy('created_at', 'desc');
        }
        return $users;
    }
}
