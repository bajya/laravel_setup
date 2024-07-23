<?php
    
namespace App\Http\Controllers\Backend;
    
use App\Library\Helper;
use App\Library\Notify;    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
// use App\Push;
use App\Notificationuser;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Mail;

use Illuminate\Support\Arr;
    
class NotificationController extends Controller
{
    public $notification;
    public $columns;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->notification = new Notificationuser;
        $this->columns = [
            "select", "description", "created_at", "status", "activate", "action",
        ];
       
    }

    public function index(Request $request) {
        // $vendors = User::all()->where('status', 'active')->where('id', '!=', 1)->where('role', 'Vendor')->pluck('name', 'id');
       
        return view('backend.notifications.index');
    }

    public function notificationsAjax(Request $request) {
        if (isset($request->search['value'])) {
            $request->search = $request->search['value'];
        }else{
            $request->search = '';
        }
        if (isset($request->order[0]['column'])) {
            $request->order_column = $request->order[0]['column'];
            $request->order_dir = $request->order[0]['dir'];
        }
        $records = $this->notification->fetchNotifications($request, $this->columns);
        $total = $records->get();
        if (isset($request->start)) {
            $notifications = $records->offset($request->start)->limit($request->length)->get();
        } else {
            $notifications = $records->offset($request->start)->limit(count($total))->get();
        }

        $result = [];
        $i = 1;
        foreach ($notifications as $notification) {
            $data = [];
            $data['select'] = '<div class="form-check form-check-flat"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="notification_id[]" value="' . $notification->id . '"><i class="input-helper"></i></label></div>';
			
            $data['sno'] = $i++;
            $data['title'] = ($notification->title != null) ? ucfirst($notification->title) : '-'; 
            $data['notification_type'] = ($notification->notification_type != null) ? $notification->notification_type : '-'; 
            $data['description'] = ($notification->description != null) ? ucfirst($notification->description) : '-'; 
            $data['created_at'] = date('d-m-Y H:i:s', strtotime($notification->created_at));
            $result[] = $data;
        }
        $data = json_encode([
            'data' => $result,
            'recordsTotal' => count($total),
            'recordsFiltered' => count($total),
        ]);
        echo $data;

    }
    public function create() {

    }

    public function destroy(Request $request) {
		if (isset($request->deleteid) && $request->deleteid != null) {
			$recommendedNotification = Notificationuser::find($request->deleteid);
			if (isset($recommendedNotification->id)) {
				$recommendedNotification->status = 'delete';
				if ($recommendedNotification->save()) {
					echo json_encode(['status' => 1, 'message' => 'Notification deleted successfully.']);
				} else {
					echo json_encode(['status' => 0, 'message' => 'Unable to delete Notification. Please try again later.']);
				}
			} else {
				echo json_encode(['status' => 0, 'message' => 'Invalid Notification']);
			}
		} else {
			echo json_encode(['status' => 0, 'message' => 'Invalid Notification']);
		}
	}

	
	/**
	 * Remove multiple resource from storage.
	 *
	 * @param  \Illuminate\Http\Request
	 * @return \Illuminate\Http\Response
	 */
	public function bulkdelete(Request $request) {
		if (isset($request->deleteid) && $request->deleteid != null) {
			$deleteid = explode(',', $request->deleteid);
			$ids = count($deleteid);
			$count = 0;
			foreach ($deleteid as $id) {
				$recommendedNotification = Notificationuser::find($id);
				if (isset($recommendedNotification->id)) {
					$recommendedNotification->status = 'delete';
					if ($recommendedNotification->save()) {
						$count++;
					}
				}
			}
			if ($count == $ids) {
				echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Notification deleted successfully.']);
			} else {
				echo json_encode(["status" => 0, 'message' => 'Not all Notification were deleted. Please try again later.']);
			}
		} else {
			echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
		}
	}
    
   
    public function sendMail($subject,$description,$email){
          $record = (object)[];
            $record->description= $description;
            $record->subject = $subject;
             Mail::send('emails.pushNotification', compact('record'), function ($message) use ($email, $subject) {
                $message->to($email, config('app.name'))->subject($subject);
                $message->from(env('MAIL_FROM_ADDRESS'), config('app.name'));
            });
    }

    public function readNotification($id) {
        $notificationData = Notificationuser::find($id);
        $inp = ['is_read' => 1];

        if(!empty($notificationData)) {

            if ($notificationData->update($inp)) {

                $result['message'] = 'Notification read successfully';
                $result['status'] = 1;

            }else{
                $result['message'] = 'Something went wrong!!';
                $result['status'] = 0;
            }
        }else{
            $result['message'] = 'Invaild notification id!!';
            $result['status'] = 0;
        }
        return response()->json($result);
    }

    public function clearAllNotification(Request $request) {

        if ($request->userId) {
            Notificationuser::where('receiver_id', $request->userId)->delete();
            $result['message'] = 'Notification deleted successfully';
            $result['status'] = 1;

        } else{
            $result['message'] = 'Something went wrong!!';
            $result['status'] = 0;
        }
        return response()->json($result);
    }
}