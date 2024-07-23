<?php

namespace App\Http\Controllers;

use App\User;
use App\Message;
use App\Notificationuser;
use App\CMS;
use App\Article;
use App\Push;
use App\AdminSettings;
use App\Testimonial;
use App\Vouchar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Pusher\Pusher;
use Carbon\Carbon;
use App\ContestParticipant;
use App\CheckMonthlyNotification;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function welcome()
    {
        return view('welcome');
    }

    public function index()
    {
        
        return view('home');
    }
    public function downloadFile($filename)
    {
        $headers = [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        return response()->download(public_path('uploads/invoice_pdf/' . $filename), $filename, $headers);
    }

    private function uploadImage($request)
    {
        $file = $request->file('files');
        $filename = md5(uniqid()) . "." . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/message'), $filename);
        return $filename;
    }
    public function cronJob(Request $request) {
        try {
            $current_time = date('Y-m-d H:i:s');
            $pushs = Push::where('is_send', 0)->get();

            if (!empty($pushs)) {
                foreach ($pushs as $key => $push) {
                    foreach ($push->push_user as $key => $push_user){
                        $user = User::select('id', 'notification', 'email_alert')->where('id', $push_user->user_id)->first();
                        
                        if (!empty($user)) {
                            $this->pushNotificationSend($user, $push);
                        }
                    }
                    $push->is_send = 1;
                    $push->save();
                }
            }


            $currentDate = Carbon::now();	
            $firstDay = $currentDate->firstOfMonth();	
			$lastDay = $currentDate->lastOfMonth();

            if($contest = ContestParticipant::where('start_date', '<=', $firstDay)->where('end_date', '>=', $lastDay)->where("status","active")->orderBy('id', 'desc')->first())
            {
                $contestId = $contest->id;
            
            }else
            {   
				$contest = new ContestParticipant;
                $contest->name = $firstDay->format('F');
				$contest->start_date = $firstDay;
				$contest->end_date = $lastDay;
                $contest->save();

            }
            if(!CheckMonthlyNotification::where('contest_id', $contest->id)->first()){
                $montlynotification = new CheckMonthlyNotification;
                $montlynotification->contest_id = $contestId;
                $montlynotification->date = $lastDay;
                $montlynotification->save();

                // $users = User::all();

                $users = User::whereIn('id', function ($query) use ($contestId) {
                            $query->select('user_id')
                                ->from('contest_joins')
                                ->where('id', $contestId);
                        })->get();

                foreach ($users as $user) {

                    $userId = $user->id;
                    $title = 'Hey, New Contest Is Live';
                    $des = 'New contest is live now you can participants by clicking on the JOIN button';
                    $push = array('sender_id' => 1, 'notification_type' => 'contest_live', 'title' => $title, 'description' => $des);
                    $account_type = 'user';

                    $this->pushNotification($userId,$push, $account_type,$contestId);
                }
            }

            

        } catch (\Exception $ex) {
            
        }
    }

    public function pushNotificationwinner($user,$push) {
		try
		{
            $notification=new Winner();
            $notification->sender_id = 1;
            $notification->receiver_id = $user->id;
            if (isset($push['notification_type']) && !empty($push['notification_type'])) {
                $notification->notification_type = $push['notification_type'];
            }else{
                $notification->notification_type = 'admin';
            }
            $notification->title = $push['title'];
            $notification->description = $push['description'];
            $notification->status = 'active';
            $notification->save();
            $type= '';
            $notification_type = '';
            $sound = true;
            
            $alert = true;
            
            $headtitle = ucfirst($push['title']);
            $extramessage = ucfirst($push['description']);
            if (isset($user->devices)) {
                foreach ($user->devices as $k => $v) {
                    $device_type = isset($v) && !empty($v->device_type) ? $v->device_type : 'android' ;
                    $apptoken = isset($v) && !empty($v->device_token) ? $v->device_token : '' ;
                    if ($device_type == 'android') {
                        $this->androidPushNotification($apptoken, $headtitle, $extramessage, $sound, $alert, $type, $notification_type);
                    }
                    if ($device_type == 'ios') {
                        $this->sendIosNotification($apptoken, $headtitle, $extramessage, $sound, $alert, $type, $notification_type);
                    }
                }
            }
            return [];
		} catch (\Exception $ex) {
			return [];
		}
	}

    public function getPage(Request $request, $slug)
    {
        $cms = CMS::where('status', 'active')->where('slug', $slug)->first();
        if ($cms) {
            return view('frontend.pages.page', compact('cms'));
        }else{
            return view('error.404');
        }
    }
    public function pushNotificationSend($user, $push) {
        try
        {
            $notification=new Notificationuser();
            $notification->sender_id = 1;
            $notification->receiver_id = $user->id;
            if (isset($push['notification_type']) && !empty($push['notification_type'])) {
                $notification->notification_type = $push['notification_type'];
            }else{
                $notification->notification_type = 'admin';
            }
            $notification->title = $push['title'];
            $notification->description = $push['description'];
            $notification->status = 'active';
            $notification->save();
            $type= '';
            $notification_type = '';
            $sound = true;
            
            $alert = true;
            
            $headtitle = ucfirst($push['title']);
            $extramessage = ucfirst($push['description']);
            if (isset($user->devices)) {
                foreach ($user->devices as $k => $v) {
                    $device_type = isset($v) && !empty($v->device_type) ? $v->device_type : 'android' ;
                    $apptoken = isset($v) && !empty($v->device_token) ? $v->device_token : '' ;
                    if ($device_type == 'android') {
                        $this->androidPushNotification($apptoken, $headtitle, $extramessage, $sound, $alert, $type, $notification_type);
                    }
                    if ($device_type == 'ios') {
                        $this->sendIosNotification($apptoken, $headtitle, $extramessage, $sound, $alert, $type, $notification_type);
                    }
                }
            }
            return [];
        } catch (\Exception $ex) {
            // dd($ex);
            return [];
        }
    }
    public function androidPushNotification($token, $title, $extramessage, $sound, $alert, $type, $notification_type)
    {
        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
        $notification = [
            'title' => $title,
            'sound' => $sound,
            'body' => $extramessage,
            'vibrate' => $alert,
        ];
        $extraNotificationData = ["message" => $notification, "moredata" => $extramessage, 'type' => ''];
        $fcmNotification = [
            //'registration_ids' => $tokenList, 
            'to'        => $token, 
            'notification' => $notification,
            'data' => $extraNotificationData
        ];
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key='.env('FCM_LEGACY_KEY');
        $data = json_encode($fcmNotification);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $fcmUrl);
        //curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch,CURLOPT_POST, true );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        $result = curl_exec($ch);
        // dd($result);
        if ($result === FALSE) {
           // die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }
    public function sendIosNotification($token, $title, $extramessage, $sound, $alert, $type, $notification_type)
    {
        $url = "https://fcm.googleapis.com/fcm/send";
        $registrationIds = $token;
        $serverKey =env('FCM_LEGACY_KEY');
        // $serverKey ='AAAA5kDkEW4:APA91bGg79-bDEU7OOLo9QWC9bQLIKuaJxt0wWv1vOEKjyTj6sKHIpi1lu5y6neXlBUOuayYvuLpdT4UVMaYBv4ksiKU3Bxq1yvdMO9sRwnyKZg8kcp8k4p_6Qho4jv48YwqFy9rzSiX';
        $body = $extramessage;
        $notification = array('title' =>$title , 'body' => $body, 'text' => $body, 'sound' => $sound);
        $arrayToSend = array('to' => $registrationIds, 'notification'=>$notification,'priority'=>'high');
        $json = json_encode($arrayToSend);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key='.$serverKey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        //curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch,CURLOPT_POST, true );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        $result = curl_exec($ch);
        if ($result === FALSE) 
        {
          //  die('FCM Send Error: ' . curl_error($ch));
        }
        curl_close( $ch );
        return $result;
    }    

}
