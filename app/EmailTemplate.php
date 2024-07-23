<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;
    public $table = 'email_templates';
    

	public function fetchEmailTemplate($request, $columns) {
		$query = EmailTemplate::where('status', "!=","delete");
		  if (isset($request->from_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
        }
        if (isset($request->end_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
        }
        
        
        if (isset($request->status)) {
            $query->where('status', $request->status);
        }

		if (isset($request->search) && !empty($request->search)) {
			$query->where(function ($q) use ($request) {
				$q->orWhere('name', 'like', '%' . $request->search . '%');
				$q->orWhere('subject', 'like', '%' . $request->search . '%');
				$q->orWhere('footer', 'like', '%' . $request->search . '%');
			});
		}

		if (isset($request->order_column)) {
            if($request->order_column == 6){
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
