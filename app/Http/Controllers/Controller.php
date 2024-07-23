<?php

namespace App\Http\Controllers;
use App\Library\Helper;
use App\Library\Notify;
use App\Library\ResponseMessages;
use App\User;
use App\UserOTP;
use App\UserDevice;
use App\Notificationuser;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Auth;
use Config;
use DB;
use Carbon\Carbon;
use DOMDocument;
use Log;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
	public function checkUserDevice($id, $device_id, $device_token) {
		if (stripos(url()->current(), 'api') !== false) {
			$device = UserDevice::where('user_id', $id)->where('device_id', $device_id)->whereStatus('active')->first();
			if (!isset($device->id)) {
				return false;
			}
			return true;
		} else {
			return true;
		}
	}

	public function pushNotificationSendActive($user, $push, $type) {
		try
		{
          	$notification = new Notificationuser();
          	$notification->sender_id = $push['sender_id'];
          	$notification->receiver_id = $user->id;
          	$notification->notification_type = $push['notification_type'];
          	$notification->title = $push['title'];
          	$notification->type = $type;
          	$notification->description = $push['description'];
          	$notification->status = 'active';
          	$notification->save();
          	$sound = true;
          	$alert = true;
	        /*if ($user->sound == 'Yes') {
	            $sound = 'true';
	        }
          	if ($user->alert == 'Yes') {
              	$alert = 'true';
          	}*/
          	$headtitle = ucfirst($push['title']);
		    $extramessage = ucfirst($push['description']);
		    $notification_type = ucfirst($push['notification_type']);
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

	public function pushNotification($userId, $push, $type, $contestId) {
		try
		{
          	$notification = new Notificationuser();
          	$notification->sender_id = $push['sender_id'];
          	$notification->receiver_id = $userId;
			$notification->order_id = $contestId;
          	$notification->notification_type = $push['notification_type'];
          	$notification->title = $push['title'];
          	$notification->type = $type;
          	$notification->description = $push['description'];
          	$notification->status = 'active';
          	$notification->save();
          	$sound = true;
          	$alert = true;
	        /*if ($user->sound == 'Yes') {
	            $sound = 'true';
	        }
          	if ($user->alert == 'Yes') {
              	$alert = 'true';
          	}*/
          	$headtitle = ucfirst($push['title']);
		    $extramessage = ucfirst($push['description']);
		    $notification_type = ucfirst($push['notification_type']);
		    $user = User::select('id', 'notification', 'email_alert')->where('id', $userId)->first();
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

	public function pushNotificationRegister($users, $push, $type) {
		try
		{
          	$notification = new Notificationuser();
          	$notification->sender_id = $push['sender_id'];
          	$notification->receiver_id = $users->id;
          	$notification->notification_type = $push['notification_type'];
          	$notification->title = $push['title'];
          	$notification->type = $type;
          	$notification->description = $push['description'];
          	$notification->status = 'active';
          	$notification->save();
          	$sound = true;
          	$alert = true;
	        /*if ($user->sound == 'Yes') {
	            $sound = 'true';
	        }
          	if ($user->alert == 'Yes') {
              	$alert = 'true';
          	}*/
          	$headtitle = ucfirst($push['title']);
		    $extramessage = ucfirst($push['description']);
		    $notification_type = ucfirst($push['notification_type']);
          	if (isset($users->devices)) {
          		foreach ($users->devices as $k => $v) {
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


	public function pushNotificationAdmin($user_id, $push, $type) {
		try
		{
          	$notification = new Notificationuser();
          	$notification->sender_id = $push['sender_id'];
          	$notification->receiver_id = $user_id;
          	$notification->notification_type = $push['notification_type'];
          	$notification->title = $push['title'];
          	$notification->type = $type;
          	$notification->description = $push['description'];
          	$notification->status = 'active';
          	$notification->save();

			return [];
		} catch (\Exception $ex) {
			return [];
		}
	}


	public function generateReferralCode($user_id) {
		
		$letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

		$code = substr($letters, mt_rand(0, 24), 2) . mt_rand(1000, 9999) . substr($letters, mt_rand(0, 23), 3) . mt_rand(10, 99).$user_id;

		return $code;
	}

	public function pushNotificationEveryuser($userids,$push, $type, $contestId) {
		try
		{
          	$notification = new Notificationuser();
          	$notification->sender_id = $push['sender_id'];
          	$notification->receiver_id = $userids;
			$notification->order_id = $contestId;
          	$notification->notification_type = $push['notification_type'];
          	$notification->title = $push['title'];
          	$notification->type = $type;
          	$notification->description = $push['description'];
          	$notification->status = 'active';
          	$notification->save();
          	$sound = true;
          	$alert = true;
	        /*if ($user->sound == 'Yes') {
	            $sound = 'true';
	        }
          	if ($user->alert == 'Yes') {
              	$alert = 'true';
          	}*/
          	$headtitle = ucfirst($push['title']);
		    $extramessage = ucfirst($push['description']);
		    $notification_type = ucfirst($push['notification_type']);
		    $user = User::select('id', 'notification', 'email_alert')->where('id', $userids)->first();
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

    public function androidPushNotification($token, $title, $extramessage, $sound, $alert, $type, $notification_type)
    {
        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
        $notification = [
            'title' => $title,
            'sound' => $sound,
            'body' => $extramessage,
            'vibrate' => $alert,
            'type' => $notification_type,
        ];
        $extraNotificationData = ["message" => $notification, "moredata" => $extramessage, 'type' => $notification_type];
        $fcmNotification = [
            //'registration_ids' => $tokenList, 
            'to'        => $token, 
            'notification' => $notification,
            'data' => $notification
        ];
       // echo '<pre>'; print_r($fcmNotification); die;
        if ($type == 'user') {
	    	$serverKey = env('FCM_LEGACY_KEY');
	    	// $serverKey = 'AAAA5kDkEW4:APA91bGg79-bDEU7OOLo9QWC9bQLIKuaJxt0wWv1vOEKjyTj6sKHIpi1lu5y6neXlBUOuayYvuLpdT4UVMaYBv4ksiKU3Bxq1yvdMO9sRwnyKZg8kcp8k4p_6Qho4jv48YwqFy9rzSiX';
	    }else{
	    	$serverKey = env('FCM_LEGACY_KEY_CHILDREN');
	    	// $serverKey = 'AAAA5kDkEW4:APA91bGg79-bDEU7OOLo9QWC9bQLIKuaJxt0wWv1vOEKjyTj6sKHIpi1lu5y6neXlBUOuayYvuLpdT4UVMaYBv4ksiKU3Bxq1yvdMO9sRwnyKZg8kcp8k4p_6Qho4jv48YwqFy9rzSiX';
	    }
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key='.$serverKey;
        $data = json_encode($fcmNotification);
       // dd($extraNotificationData);
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
	    if ($type == 'user') {
	    	$serverKey = env('FCM_LEGACY_KEY');
	    	// $serverKey = 'AAAA5kDkEW4:APA91bGg79-bDEU7OOLo9QWC9bQLIKuaJxt0wWv1vOEKjyTj6sKHIpi1lu5y6neXlBUOuayYvuLpdT4UVMaYBv4ksiKU3Bxq1yvdMO9sRwnyKZg8kcp8k4p_6Qho4jv48YwqFy9rzSiX';
	    }else{
	    	$serverKey = env('FCM_LEGACY_KEY_CHILDREN');
	    	// $serverKey = 'AAAA5kDkEW4:APA91bGg79-bDEU7OOLo9QWC9bQLIKuaJxt0wWv1vOEKjyTj6sKHIpi1lu5y6neXlBUOuayYvuLpdT4UVMaYBv4ksiKU3Bxq1yvdMO9sRwnyKZg8kcp8k4p_6Qho4jv48YwqFy9rzSiX';
	    }
	    
	    $body = $extramessage;
	    $notification = array('title' =>$title , 'body' => $body, 'text' => $body, 'sound' => $sound, 'type' => $notification_type);
	    $arrayToSend = array('to' => $registrationIds, 'notification'=>$notification,'priority'=>'high');
	    $json = json_encode($arrayToSend);
	    $headers = array();
	    $headers[] = 'Content-Type: application/json';
	    $headers[] = 'Authorization: key='.env('FCM_LEGACY_KEY');
	    // $headers[] = 'Authorization: key=AAAA5kDkEW4:APA91bGg79-bDEU7OOLo9QWC9bQLIKuaJxt0wWv1vOEKjyTj6sKHIpi1lu5y6neXlBUOuayYvuLpdT4UVMaYBv4ksiKU3Bxq1yvdMO9sRwnyKZg8kcp8k4p_6Qho4jv48YwqFy9rzSiX';
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

	public function checkUserActive($userId, $device_id) {
		if ($user = User::where("id", $userId)->first()) {
			if ($user->status == "active") {
				if ($user->is_verified == 1) {
					$count = UserDevice::where('user_id', $userId)->where('device_id', $device_id)->whereStatus('active')->count();
					if ($count <= 0) {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(240),
							"data" => null,
							"logout" => 1,
						);
						$this->shut_down();
						exit;
					}else{
						return true;
					}
					
					$count = UserDevice::where('user_id', $userId)->where('device_id', $device_id)->whereStatus('active')->count();
					if ($count <= 0) {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(240),
							"data" => null,
							"logout" => 1,
						);
						$this->shut_down();
						exit;
					}
				} else {
					$currenttime = date('Y-m-d H:i:s');
	            	$otp_expire_time = date("Y-m-d H:i:s",strtotime("+1 minutes", strtotime($currenttime)));
					if ($user->is_verified == 0) {
						$otp = new UserOTP;
						$otp->user_id = $user->id;
						$otp->email = $user->email;
						$otp->otp_expire_time = $otp_expire_time;
						$verify_code = Helper::generateCode();
						$otp->code = $verify_code;
						if ($otp->save()) {
							$user->verify_code = $otp->code;
							//Notify::sendMail("emails.resent_otp", $user->toArray(), "Laravel - Verify Otp");
							$this->response = array(
								"status" => 200,
								"timer" => 3,
								"message" => ResponseMessages::getStatusCodeMessages(106),
								"data" => null,
							);
						} else {
							$this->response = array(
								"status" => 300,
								"message" => ResponseMessages::getStatusCodeMessages(102),
								"data" => null,
							);
						}
					} else {
						$this->response = array(
							"status" => 105,
							"message" => ResponseMessages::getStatusCodeMessages(105),
							"data" => null,
						);
					}
					$this->shut_down();
					exit;
				}
			} else {
				if ($user->status == 'inactive') {
					$this->response = array(
						"status" => 666,
						"message" => ResponseMessages::getStatusCodeMessages(216),
						"data" => null,
					);
				} elseif ($user->status == 'delete') {
					$this->response = array(
						"status" => 403,
						"message" => ResponseMessages::getStatusCodeMessages(217),
						"data" => null,
						"logout" => 1,
					);
				}
				$this->shut_down();
				exit;
			}
		} else {
			$this->response = array(
				"status" => 403,
				"message" => ResponseMessages::getStatusCodeMessages(5),
				"data" => null,
				"logout" => 1,
			);
			$this->shut_down();
			exit;
		}
	}

	public function logoutUserDevice($id, $device_id = null, $device_token = null) {
		Auth::user()->token()->revoke();
		if (isset($device_id) && $device_id != null) {
			$device = UserDevice::where('user_id', $id)->where('device_id', $device_id)->update(['status' => 'delete']);
			return true;
		} else { 
			UserDevice::where('user_id', $id)->whereStatus('active')->update(['status' => 'delete']);
		}
	}

   	public $response = array(
		"status" => 500,
		"message" => "Internal server error",
		"data" => null,
	);

	public $paginate = 10;

    public function checkKeys($input = array(), $required = array()) {
		$existance = implode(", ", array_diff($required, $input));
		if (!empty($existance)) {
			if (count(array_diff($required, $input)) == 1) {
				$this->response = array(
					"status" => 401,
					"message" => $existance . " key is missing",
					"data" => null,
				);
			} else {
				$this->response = array(
					"status" => 401,
					"message" => $existance . " keys are missing",
					"data" => null,
				);
			}
			$this->shut_down();
			exit;
		}
	}

	public function updateUserDevice($id, $device_id, $device_token, $device_type) {
		if (stripos(url()->current(), 'api') !== false) {
			if (($device_type == 'android') || ($device_type == 'ios') || ($device_type == 'web')) {
				if (($device_type == 'android') || ($device_type == 'ios') || ($device_type == 'web')) {
					$device = UserDevice::where('user_id', $id)->where('device_id', $device_id)->first();
					if (empty($device)) { 
						$user = User::where('id', $id)->first();
						if (!empty($user)) {
							if ($user->status == 'active') {
								
								$notification_count = 0;
								
								$push = array('sender_id' => 1, 'notification_type' => 'logout', 'notification_count' => $notification_count, 'title' => 'You are login another device', 'description' => 'You are login another device same login details');
								$type = 'user';
								//$this->pushNotificationSendActiveLogout($user, $push, $type);
								//UserDevice::where('user_id', $id)->update(['status' => 'delete']);
								$device = new UserDevice;
								$device->user_id = $id;
								$device->device_id = $device_id;
								$device->device_token = $device_token;
								$device->device_type = $device_type;
							    return $device->save();
							    
							} else { 
								if ($user->status == 'inactive') {
									$this->response = array(
										"status" => 666,
										"message" => ResponseMessages::getStatusCodeMessages(216),
										"data" => null,
										
									);
									$this->shut_down();
								exit;
								} else{
									$this->response = array(
										"status" => 666,
										"message" => ResponseMessages::getStatusCodeMessages(217),
										"data" => null,
										//"logout" => 1,
									);
									$this->shut_down();
								exit;
								}
							}
							
						}else{
							/*$push = array('sender_id' => $user->id, 'notification_type' => 'logout', 'title' => 'You are login another device', 'description' => 'You are login another device same login details');
							$type = 'user';
							$this->pushNotificationSendActiveLogout($user, $push, $type);
							UserDevice::where('user_id', $id)->where('status', 'active')->update(['status' => 'delete']);*/
							$this->response = array(
								"status" => 403,
								"message" => ResponseMessages::getStatusCodeMessages(5),
								"data" => null,
								"logout" => 1,
							);
							$this->shut_down();
								exit;
						}
					}else{
						//echo 'hh'; die;
						$user = User::where('id', $id)->first();
						if (!empty($user)) {
							if ($user->status == 'active') {
								$device->user_id = $id;
								$device->device_id = $device_id;
								$device->device_token = $device_token;
								$device->device_type = $device_type;
								$device->status = 'active';
							    return $device->save();
								/*$push = array('sender_id' => $user->id, 'notification_type' => 'logout', 'title' => 'You are login another device', 'description' => 'You are login another device same login details');
								$type = 'user';
								$this->pushNotificationSendActiveLogout($user, $push, $type);
								UserDevice::where('user_id', $id)->where('status', 'active')->update(['status' => 'delete']);*/
							} else { 
								if ($user->status == 'inactive') {
									$this->response = array(
										"status" => 666,
										"message" => ResponseMessages::getStatusCodeMessages(216),
										"data" => null,
										
									);
									$this->shut_down();
								exit;
								} else{
									$this->response = array(
										"status" => 403,
										"message" => ResponseMessages::getStatusCodeMessages(217),
										"data" => null,
										"logout" => 1,
									);
									$this->shut_down();
								exit;
								}
							}
						}else{
							$this->response = array(
								"status" => 403,
								"message" => ResponseMessages::getStatusCodeMessages(5),
								"data" => null,
								"logout" => 1,
							);
							$this->shut_down();
							exit;
							/*$push = array('sender_id' => $user->id, 'notification_type' => 'logout', 'title' => 'You are login another device', 'description' => 'You are login another device same login details');
							$type = 'user';
							$this->pushNotificationSendActiveLogout($user, $push, $type);
							UserDevice::where('user_id', $id)->where('status', 'active')->update(['status' => 'delete']);*/
						}
					}
				}else{
					return true;
				}
			}else{
				$this->response = array(
					"status" => 515,
					"message" => ResponseMessages::getStatusCodeMessages(515),
					"data" => null,
					"logout" => 1,
				);
				$this->shut_down();
				exit;
			}
		} else {
			return true;
		}
	}

	
	
	function shut_down(Request $request = null,$userid=null) {
		if ((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
			if ($user->status == 'active') {
				//$this->response['unread_notification'] = 0;
				//$this->response['logout'] = 0;
			}else{
				$this->response['status'] = 403;
				$this->response['message'] = ResponseMessages::getStatusCodeMessages(5);
				$this->response['data'] = null;
				//$this->response['logout'] = 1;
				//$this->response['unread_notification'] = 0;
			}
			$currentDate = date('Y-m-d');
	    	if ($user->subscription_expire_date != '') {
	    		if (strtotime($user->subscription_expire_date) >= strtotime($currentDate)) {
	    			$this->response['isSubscription'] = true;
	    		}else{
	    			$this->response['isSubscription'] = false;
	    		}
	    		
	    	}else{
	    		$this->response['isSubscription'] = false;
	    	}
		}else{
			$this->response['isSubscription'] = false;
		}
		//$this->response['logout'] = 0;
		//$this->response['unread_notification'] = 0;
		echo json_encode($this->response);
		
	}



	public function pushNotificationSendActiveLogout($user, $push, $type) {
		try
		{
          	$sound = 'true';
          	$alert = 'true';
	        /*if ($user->sound == 'Yes') {
	            $sound = 'true';
	        }
          	if ($user->alert == 'Yes') {
              	$alert = 'true';
          	}*/
          	$headtitle = ucfirst($push['title']);
		    $extramessage = ucfirst($push['description']);
          	if (isset($user->devices)) {
          		foreach ($user->devices as $k => $v) {
	          		$device_type = isset($v) && !empty($v->device_type) ? $v->device_type : 'android' ;
		          	$apptoken = isset($v) && !empty($v->device_token) ? $v->device_token : '' ;
		          	
					if ($device_type == 'android') {
		              	$this->androidPushNotificationLogout($apptoken, $headtitle, $extramessage, $sound, $alert);
		          	}
		          	if ($device_type == 'ios') {
		              	$this->sendIosNotificationLogout($apptoken, $headtitle, $extramessage, $sound, $alert);
		          	}
	          	}
          	}
			return [];
		} catch (\Exception $ex) {
			
			return [];
		}
	} 

	
	


    public function otpSend($url)
    {
       	$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POST, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
        if ($output === FALSE) {
           // die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);
        return $output;
    }

    public function androidPushNotificationLogout($token, $title, $extramessage, $sound, $alert)
    {

        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

        $notification = [
            'title' => $title,
            'sound' => $sound,
            'body' => $extramessage,
            'vibrate' => $alert,
        ];
        
        $extraNotificationData = ["message" => $notification, "moredata" => $extramessage, 'type' => 'logout'];

        $fcmNotification = [
            //'registration_ids' => $tokenList, 
            'to'        => $token, 
            'notification' => $notification,
            'data' => $extraNotificationData
        ];
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key='.env('FCM_LEGACY_KEY');
        // $headers[] = 'Authorization: key=AAAA5kDkEW4:APA91bGg79-bDEU7OOLo9QWC9bQLIKuaJxt0wWv1vOEKjyTj6sKHIpi1lu5y6neXlBUOuayYvuLpdT4UVMaYBv4ksiKU3Bxq1yvdMO9sRwnyKZg8kcp8k4p_6Qho4jv48YwqFy9rzSiX';
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
        if ($result === FALSE) {
           // die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }

	public function sendIosNotificationLogout($token, $title, $extramessage, $sound, $alert)
	{
	    $url = "https://fcm.googleapis.com/fcm/send";
	    $registrationIds = $token;
	    $serverKey =env('FCM_LEGACY_KEY');
	    // $serverKey ='AAAA5kDkEW4:APA91bGg79-bDEU7OOLo9QWC9bQLIKuaJxt0wWv1vOEKjyTj6sKHIpi1lu5y6neXlBUOuayYvuLpdT4UVMaYBv4ksiKU3Bxq1yvdMO9sRwnyKZg8kcp8k4p_6Qho4jv48YwqFy9rzSiX';
	    $body = $extramessage;
	    $notification = array('title' =>$title , 'body' => $body, 'text' => $body, 'sound' => $sound, 'logout' => $logout);

	    $arrayToSend = array('to' => $registrationIds, 'notification'=>$notification,'priority'=>'high');
	    $json = json_encode($arrayToSend);
	    $headers = array();
	    $headers[] = 'Content-Type: application/json';
	    $headers[] = 'Authorization: key='.env('FCM_LEGACY_KEY');
	    // $headers[] = 'Authorization: key=AAAA5kDkEW4:APA91bGg79-bDEU7OOLo9QWC9bQLIKuaJxt0wWv1vOEKjyTj6sKHIpi1lu5y6neXlBUOuayYvuLpdT4UVMaYBv4ksiKU3Bxq1yvdMO9sRwnyKZg8kcp8k4p_6Qho4jv48YwqFy9rzSiX';
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
	public function checkUser($userId, $device_id) {
		if ($user = User::where("id", $userId)->first()) {
			if ($user->status == "active") {
				if ($user->is_verified == '1') {
					if ($user->role == 'shop') {
						return true;
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(403),
							"data" => null,
							"logout" => 1,
						);

					}
					$count = UserDevice::where('user_id', $userId)->where('device_id', $device_id)->where('status', 'active')->count();
					if ($count <= 0) {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(240),
							"data" => null,
							"logout" => 1,
						);

					}
				}else{
					$currenttime = date('Y-m-d H:i:s');
	            	$otp_expire_time = date("Y-m-d H:i:s",strtotime("+1 minutes", strtotime($currenttime)));
					if ($user->is_verified == 0) {
						$otp = new UserOTP;
						$otp->user_id = $user->id;
						$otp->email = $user->email;
						$otp->otp_expire_time = $otp_expire_time;
						$verify_code = Helper::generateCode();
						$otp->code = $verify_code;
						if ($otp->save()) {
							$user->verify_code = $otp->code;
							//Notify::sendMail("emails.resent_otp", $user->toArray(), "Laravel - Verify Otp");
							$this->response = array(
								"status" => 999,
								"timer" => 60,
								"message" => 'User not verify',
							);

						} else {
							$this->response = array(
								"status" => 300,
								"message" => ResponseMessages::getStatusCodeMessages(102),
								"data" => null,
							);
						}
					} else {
						$this->response = array(
							"status" => 105,
							"message" => ResponseMessages::getStatusCodeMessages(105),
							"data" => null,
						);
					}
				}
			} else { 
				if ($user->status == 'inactive') {
					$this->response = array(
						"status" => 666,
						"message" => ResponseMessages::getStatusCodeMessages(216),
						"data" => null,
						
					);
				} elseif ($user->status == 'delete') {
					$this->response = array(
						"status" => 403,
						"message" => ResponseMessages::getStatusCodeMessages(217),
						"data" => null,
						"logout" => 1,
					);
				}
			}
		} else {
			$this->response = array(
				"status" => 403,
				"message" => ResponseMessages::getStatusCodeMessages(5),
				"data" => null,
				"logout" => 1,
			);
		}
		$this->shut_down();
			exit;
	}
	function dateDifference($start_date, $end_date)
	{
	    // calulating the difference in timestamps 
	    $diff = strtotime($start_date) - strtotime($end_date);
	     
	    // 1 day = 24 hours 
	    // 24 * 60 * 60 = 86400 seconds
	    return ceil(abs($diff));
	}


}
