<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Library\Helper;
use App\Library\Notify;
use App\Library\ResponseMessages;
use App\User;
use App\UserOTP;
use App\UserDevice;
use App\EmailTemplate;
use App\Church;
use App\SplashScreen;
use App\Article;
use App\Country;
use App\Color;
use App\State;
use App\BusRuleRef;
use App\City;
use App\Language;
use App\Ethnicity;
use App\Ministrie;
use App\Family;
use App\FamiliesMember;
use App\Size;
use App\CMS;
use App\Item;
use App\PurchaseItem;
use App\VendorCustomer;
use App\Newsletter;
use App\UserInvite;
use App\ContactUs;
use App\Subscription;
use App\UserSubscription;
use App\Category;
use Auth;
use Config;
use DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Session;
use URL;
use Image;
use File;
use PDF;
use Carbon\Carbon;
use Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Log;

class ApiController extends Controller {


	protected function guard() {
		return auth()->guard('web');
	}

	// UNAUTHORIZED ACCESS
	public function appLogin()
	{
		$this->response = array(
			"status" => 403,
			"message" => ResponseMessages::getStatusCodeMessages(214),
			"data" => null,
			"access_token" => ''
		);
	}
	//function call to vendor login
	public function login(Request $request) {
		$this->checkKeys(array_keys($request->all()), ['email', 'password', 'device_id', 'device_token', 'device_type']);
		$access_token = '';
		try {
			if (in_array($request->device_type, ['android', 'ios', 'web'])) {  
				$rules = [
					'email' => 'required|email',
					'password' => 'required',
				];
				$validator = Validator::make($request->all(), $rules);
				$attr = [
					'email' => 'Email',
					'password' => 'Password'
				];
				$validator->setAttributeNames($attr);
	
				if ($validator->fails()) {
					$errors = $validator->errors();
					$this->response = [
						"status" => 300,
						"message" => $errors->first(),
						"data" => null,
						"errors" => $errors,
					];
				} else {
					$user = User::where("email", $request->email)->where('status', '!=', 'delete')->first();
					if (isset($user->id) && Hash::check($request->password, $user->password)) {
						if ($user->is_verified == '1') {
							if ($user->status == 'delete') {
								$this->response = [
									"status" => 300,
									"message" => ResponseMessages::getStatusCodeMessages(573),
									"data" => null,
									"access_token" => $access_token,
								];
							} else {
								$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
								Auth::login($user);
								$user->is_verified = '1';
								$user->save();
								$access_token = auth()->user()->createToken('Token')->accessToken;
								$userData = $this->userProfileDetails($user);
								unset($userData->devices);
								// $notification_count = 0;
								// $des = 'Logged in successfully';
								// $push = [
								// 	'sender_id' => 1,
								// 	'notification_type' => 'login',
								// 	'notification_count' => $notification_count,
								// 	'title' => 'Laravel Login',
								// 	'description' => $des

								// ];
								// $account_type = 'user';
								// $this->pushNotificationSendActive($userData, $push, $account_type);
								$this->response = [
									"status" => 200,
									"message" => ResponseMessages::getStatusCodeMessages(7),
									"data" => !empty($userData) ? $userData : null,
									"access_token" => $access_token,
								];
							}
						} else {
							$this->response = [
								"status" => 300,
								"message" => ResponseMessages::getStatusCodeMessages(584),
								"data" => null,
								"access_token" => '',
							];
						}
					} else {
						$this->response = [
							"status" => 300,
							"message" => ResponseMessages::getStatusCodeMessages(108),
							"data" => null,
							"access_token" => $access_token,
						];
					}
				}
			} else {
				$this->response = [
					"status" => 300,
					"message" => ResponseMessages::getStatusCodeMessages(515),
					"data" => null,
				];
			}
		} catch (\Exception $ex) {
			$this->response = [
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
				"data" => null,
			];
		}
		$this->shut_down();
		exit;
	}
	//function call to signup
	public function signUp(Request $request){
		$this->checkKeys(array_keys($request->all()), array('store_name', 'GST_number', 'store_category', 'address', 'lat', 'lng', 'name', 'email', 'password', 'phone_code', 'mobile', "device_id", "device_token", "device_type"));
		try{
			$input = $request->all();
				$validate = Validator($request->all(), [
					// 'device_id'=>'required',
					// 'device_token'=>'required',
					// 'device_type'=>'required',
	            'name' => 'required',
				'store_name' => 'required',
	            'email' => 'required|email',
				'password' => 'required',
				'phone_code' => 'required',
				//'address' => 'required',
	            'mobile' => 'required|min:8|numeric',
				'store_category' =>'required|numeric',
				//'GST_number' => 'required'
	        ]);
	        $attr = [
	        	// 'device_id'=>"Device Id",
	        	// 'device_token'=>"Device Token",
	        	// 'device_type'=>"Device Type",
	            'name' => 'Contact Person Name',
				'store_name' => 'Store Name',
	            'email' => 'Email',
				'password' => 'Password',
				'phone_code' =>'Country Code',
				//'address' =>'Address',
	            'mobile' => 'Phone No',
				'store_category' => 'Store Type',
				//'GST_number' => 'GST Number',
	        ];
	        $validate->setAttributeNames($attr);
	        if ($validate->fails()) {
				$errors = $validate->errors();
				$this->response = array(
					"status" => 300,
					"message" => $errors->first(),
					"data" => null,
					"errors" => $errors,
				);
			}else{
				if (($request->device_type == 'android') || ($request->device_type == 'ios') || ($request->device_type == 'web')) {
					if ($users = User::where("email", $request->email)->where('status', '!=', 'delete')->first()) {
						$this->response = array(
							"status" => 300,
							"message" => ResponseMessages::getStatusCodeMessages(2),
							"data" => null,
						);
					}else{
						$users = new User;
						$users->name = $input['name'];
						$users->email = $input['email'];
						$users->password =Hash::make($input['password']);
						$users->temp_password = $input['password'];
						$users->phone_code = $input['phone_code'];
						$users->mobile = $input['mobile'];
						$users->store_name = $input['store_name'];
						$users->store_category = $input['store_category'];
						$users->GST_number = $input['GST_number'];
						$users->address = $input['address'];
						$users->latitude = $input['lat'];
						$users->longitude = $input['lng'];
						if ($request->device_type == 'web') {
							$users->is_verified = '0';
						}else{
							$users->is_verified = '1';
						}
						$users->role = 'Vendor';	
						if ($request->hasfile('store_logo')) {
							$file = $request->file('store_logo');
							$filename = time() . $file->getClientOriginalName();
							$filename = str_replace(' ', '', $filename);
							$filename = str_replace('.jpeg', '.jpg', $filename);
							$file->move(public_path('img/storelogos'), $filename);
							if ($users->store_logo != null && file_exists(public_path('img/storelogos/' . $users->store_logo))) {
								if ($users->store_logo != 'no_avatar.jpg') {
									unlink(public_path('img/storelogos/' . $users->store_logo));
								}
							}
							$users->store_logo = $filename;
						}
						if($users->save()){
							if ($subscription = Subscription::where('id', 1)->first()) {
								$day = $subscription->days;
								UserSubscription::where('user_id', $users->id)->update(['status'=>'out']);
								$newSubscription = new UserSubscription;
								$newSubscription->user_id = $users->id;
								$newSubscription->subscription_id = $subscription->id;
								$newSubscription->name = $subscription->name;
								$newSubscription->price = $subscription->price;
								$newSubscription->days = $subscription->days;
								$newSubscription->type = $subscription->type;
								$newSubscription->description = $subscription->description;
								$newSubscription->status = 'active';
								$newSubscription->created_at = date('Y-m-d H:i:s');
							    $newSubscription->updated_at = date('Y-m-d H:i:s');
							    if ($newSubscription->save()) {
							    	$currentDate = date('Y-m-d');
							    	if ($users->subscription_expire_date != '') {
							    		if (strtotime($users->subscription_expire_date) >= strtotime($currentDate)) {
							    			$currentDate = date('Y-m-d', strtotime($users->subscription_expire_date)); 
											$final_date = date('Y-m-d', strtotime($currentDate . ' + '.$day.' days'));
							    		}else{
							    			$final_date = date('Y-m-d', strtotime($currentDate . ' + '.$day.' days'));
							    		}
							    		
							    	}else{
							    		$final_date = date('Y-m-d', strtotime($currentDate . ' + '.$day.' days'));
							    	}
							    	$users->subscription_id = $subscription->id;
							    	$users->subscription_expire_date = $final_date;
							    	$users->save();

							    	$newSubscription->start_date = $currentDate;
							    	$newSubscription->end_date = $final_date;
							    	$newSubscription->save();
							    }
							}

							$this->updateUserDevice($users->id, $request->device_id, $request->device_token, $request->device_type);

							if ($request->device_type != 'web') {
								$getEmailTemplate = EmailTemplate::where('id', 2)->select(['name', 'subject', 'description','footer'])->first();
								$record = (object)[];
								$description = $getEmailTemplate->description;
								$description = str_replace("{name}", ucfirst($users->name), $description);
								$description = str_replace("{email}", $users->email, $description);
								$description = str_replace("{password}", $input['password'], $description);
								$description = str_replace("{phone_code}", $users->phone_code, $description);
	              				$description = str_replace("{mobile}", $users->mobile, $description);
								$record->description= $description;
								$record->footer = $getEmailTemplate->footer;
								$record->subject = "Login Password Code";
								$subject = "Sign Up  Password Code";
							
								if ($users->email != '') {
									Mail::send('emails.welcome', compact('record'), function ($message) use ($users, $subject) {
										$message->to(trim($users->email), config('app.name'))->subject($subject);
										$message->from(env('MAIL_FROM_ADDRESS'), config('app.name'));
									});
								}
							}else{
								if ($users->email != '') {
									$subject = "Registration Successful ".config('app.name')."- Awaiting Admin Approval";
									Mail::send('emails.vendor_register', compact('users'), function ($message) use ($users, $subject) {
										$message->to(trim($users->email), config('app.name'))->subject($subject);
										$message->from(env('MAIL_FROM_ADDRESS'), config('app.name'));
									});
		
								}
							}
							
							$notification_count = 0;
							$des = 'Welcome to Laravel! We are excited to have you on board.';
							$push = [
								'sender_id' => 1,
								'notification_type' => 'User Register',
								'notification_count' => $notification_count,
								'title' => 'Welcome to Laravel',
								'description' => $des
							];
							$account_type = 'user';
							$this->pushNotificationRegister($users, $push, $account_type);

							$des_admin = 'New Vendor '.ucfirst($users->name).' Register.';
							$push_admin = [
								'sender_id' => $users->id,
								'notification_type' => 'Vendor Register',
								'notification_count' => $notification_count,
								'title' => 'Vendor Register',
								'description' => $des_admin
							];
							$user_id = 1;
							$this->pushNotificationAdmin($user_id, $push_admin, $account_type);
							if ($request->device_type == 'web') {
								$this->response = array(
									"status" => 200,
									"message" => ResponseMessages::getStatusCodeMessages(583),
									"data" => null,
								);
							}else{
								$this->response = array(
									"status" => 200,
									"message" => ResponseMessages::getStatusCodeMessages(558),
									"data" => null,
								);
							}
						}
					}
				}else{
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(515),
						"data" => null
					);
				}
			}
		}catch (\Exception $ex) {
			dd($ex);
			$this->response = array(
				"status" => 300,
				"message" => ResponseMessages::getStatusCodeMessages(501),
				"data" => $ex,
			);
		}
	  	$this->shut_down();
		exit;
	}


	public function categoryList(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("page"));
		try {
			$input = $request->all();
			$validate = Validator($request->all(), [
			 	'page' => 'required|numeric'
			]);
			$attr = [
				'page' => 'Page No',
			];
			$validate->setAttributeNames($attr);
			if ($validate->fails()) {
				$errors = $validate->errors();
				$this->response = array(
					"status" => 300,
					"message" => $errors->first(),
					"data" => null,
					"errors" => $errors,
				);
			}else{	
				if (isset($request->page) && !empty($request->page)) {
					$page_no = $request->page - 1;
				}else{
					$page_no = 0;
				}
				$query = Category::select('id', 'name')->where("status","active");
				if (isset($request->search) && !empty($request->search)) {
		            $query->where(function ($q1) use ($request) {
		                $q1->where('name', 'like', '%' . $request->search . '%');
		            });
		        }
		        if (isset($request->category_id) && !empty($request->category_id)) {
		            $query->where('parent_id', $request->category_id);
		        }
				$data = $query->orderBy('name', 'asc')->offset($page_no*50)->take(50)->get();
				if ($data->count() > 0) {
					$this->response = array(
						"status" => 200,
						"message" => ResponseMessages::getStatusCodeMessages(578),
						"data" => !empty($data) ? $data : null,
					);
				} else {
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(507),
						"data" => null,
					);
				}
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
				"data" => null,
			);
		}
		$this->shut_down();
		exit;
	}

	// function called to resendOtp
	public function resendOtp(Request $request) {
			$this->checkKeys(array_keys($request->all()), array('email', "device_id", "device_token", "device_type"));
			$access_token = '';
			try {
				if (($request->device_type == 'android') || ($request->device_type == 'ios') || ($request->device_type == 'web'))
				{	
					$rules = array(
						'email' => 'required',
					);
					$validate = Validator($request->all(), $rules);
					$attr = [
						'email' => 'Email',
					];	
					$validate->setAttributeNames($attr);

					if ($validate->fails()) {
						$errors = $validate->errors();
						$this->response = array(
							"status" => 300,
							"message" => $errors->first(),
							"data" => null,
							"errors" => $errors,
						);
					} else {
						if ($user = User::where("email", $request->email)->where('status', '!=', 'delete')->first()) {
								$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
					//$code  = rand(1000, 9999);
					$code  = '12345';
					if($userOTP =  UserOTP::where("user_id",$user->id)->first())
					{
						$userOTP->user_id = $user->id;
						$userOTP->email = $user->email;
						$userOTP->code = $code;
						$userOTP->save();
							
					}else{
						$userOTP = new UserOTP;
						$userOTP->user_id = $user->id;
						$userOTP->email = $user->email;
						$userOTP->code = $code;
						$userOTP->save();
					}
					$getEmailTemplate = EmailTemplate::where('id', 4)->select(['name', 'subject', 'description','footer'])->first();
					$record = (object)[];
					$desc = "Resend OTP Code Is:".$code;


					$description = $getEmailTemplate->description;
					$description = str_replace("{name}", ucfirst($user->name), $description);
					$description = str_replace("{phone_code}", $user->phone_code, $description);
					$description = str_replace("{mobile}", $user->mobile, $description);
					$description = str_replace("{email}", $user->email, $description);
					$description = str_replace("{otp}", $code, $description);
					
					$record->description= $description;
					$record->footer = $getEmailTemplate->footer;
					$record->subject = "Resend Otp Code";
					$subject = "Resend Otp Code";
					// if (!filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
						if ($user->email != '') {
							Mail::send('emails.welcome', compact('record'), function ($message) use ($user, $subject) {
								$message->to(trim($user->email), config('app.name'))->subject($subject);
								$message->from(env('MAIL_FROM_ADDRESS'), config('app.name'));
							});
						}
					// }
					$this->response = array(
									"status" => 200,
									"message" => ResponseMessages::getStatusCodeMessages(516),
									"data" => $code,
								);
						}else{
								$this->response = array(
									"status" => 300,
									"message" => ResponseMessages::getStatusCodeMessages(108),
									"data" => null
								);
						}
					}
				}else{
						$this->response = array(
							"status" => 300,
							"message" => ResponseMessages::getStatusCodeMessages(515),
							"data" => null
						);
				}
			} catch (\Exception $ex) {
					$this->response = array(
						"status" => 501,
						"message" => ResponseMessages::getStatusCodeMessages(501),
						"data" => null
					);
			}
			$this->shut_down();
			exit;
	}


	  // Function Called to verifyOtp 
	  public function verifyOtp(Request $request){
	  		$this->checkKeys(array_keys($request->all()), array('email', "code", "device_id", "device_token", "device_type"));
				$access_token = '';
				try {
						if (($request->device_type == 'android') || ($request->device_type == 'ios') || ($request->device_type == 'web'))
						{	
								$rules = array(
									'email' => 'required',
									'code' => 'required',
								);
								$validate = Validator($request->all(), $rules);
								$attr = [
									'email' => 'Email',
									'code' => 'Email',
								];	
								$validate->setAttributeNames($attr);

								if ($validate->fails()) {
									$errors = $validate->errors();
									$this->response = array(
										"status" => 300,
										"message" => $errors->first(),
										"data" => null,
										"errors" => $errors,
										"access_token" => $access_token
									);
								} else {
										if ($userOtp = UserOTP::where('email', $request->email)->where('code', $request->code)->first()) {
												if ($user = User::where("email", $request->email)->where('status', '!=', 'delete')->first()) {
														$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
														if (Auth::loginUsingId($user->id)) {
															$user->is_verified = '1';
															$user->save();
															$access_token = auth()->user()->createToken('Token')->accessToken;
															$user = User::select('id', 'avatar as image', 'is_verified', 'name','email', DB::raw("DATE_FORMAT(created_at,'%b %d') as created_date"))->withoutAppends()->where('email', $request->email)->where('status', 'active')->first();

															if (isset($user->id)) {
																	$userData = $this->userProfileDetails($user);
																	unset($userData->devices);
																	// $notification_count = 0;
																	// $des = 'Logged in successfully';
																	// $push = array('sender_id' => 1, 'notification_type' => 'login', 'notification_count' => $notification_count, 'title' => 'Laravel Login', 'description' => $des);
																	// $account_type = 'user';
														 			// $this->pushNotificationSendActive($userData, $push, $account_type);
																	$this->response = array(
																		"status" => 200,
																		"message" => ResponseMessages::getStatusCodeMessages(7),
																		"data" => !empty($userData) ? $userData : null,
																		"access_token" => $access_token,
																	);
															}else{
																$this->response = array(
																		"status" => 300,
																		"message" => ResponseMessages::getStatusCodeMessages(520),
																		"data" => null,
																		"access_token" => $access_token
																	);
																}
														} else {
																$this->response = array(
																	"status" => 300,
																	"message" => ResponseMessages::getStatusCodeMessages(108),
																	"data" => null,
																	"access_token" => $access_token
																);
														}
													}else{
															$this->response = array(
																"status" => 300,
																"message" => ResponseMessages::getStatusCodeMessages(108),
																"data" => null,
																"access_token" => $access_token
															);
													}
										}else{
											$this->response = array(
													"status" => 300,
													"message" => ResponseMessages::getStatusCodeMessages(102),
													"data" => null,
													"access_token" => $access_token
												);
										}
										
								}
						}else{
							$this->response = array(
								"status" => 300,
								"message" => ResponseMessages::getStatusCodeMessages(515),
								"data" => null,
								"access_token" => $access_token
							);
						}
				} catch (\Exception $ex) {
						$this->response = array(
							"status" => 501,
							"message" => ResponseMessages::getStatusCodeMessages(501),
							"data" => null,
							"access_token" => $access_token
						);
				}
				$this->shut_down();
				exit;
	  }

	public function userProfileDetails($user) {
		$users = User::select('id', 'name', 'email', 'phone_code', 'mobile', 'avatar as image', 'store_name', 'GST_number', 'store_logo', 'address', 'latitude', 'longitude', 'subscription_id', DB::raw("DATE_FORMAT(subscription_expire_date,'%b %d, %Y') as subscription_expire_date"), DB::raw("DATE_FORMAT(dob,'%b %d, %Y') as dob"), DB::raw("DATE_FORMAT(created_at,'%b %d, %Y') as created_date"))->withoutAppends()->where('id', $user->id)->where('status', 'active')->first();
		if (isset($users->id) && !empty($users)) {
			$total_visitor = VendorCustomer::where('vendor_id', $users->id)->where('type', 'Visitor')->count();
			$total_purchase = VendorCustomer::where('vendor_id', $users->id)->where('type', 'Purchase')->count();
			$total_repet_order = VendorCustomer::where('vendor_id', $users->id)->where('type', 'Repeat')->count();
			$total_receipt = VendorCustomer::where('vendor_id', $users->id)->where('type', 'Invoice')->count();
			$type_list = [
				'Visitor' => 'Visitor',
				'Purchase' => 'Purchase',
			];
			$category_list = Category::select('id', 'name')->where('status', 'active')->orderBy('name', 'asc')->get();
			$arrayList = [
				'total_visitor' => $total_visitor,
				'total_purchase' => $total_purchase,
				'total_repet_order' => $total_repet_order,
				'total_receipt' => $total_receipt,
				'type_list' => $type_list,
				'category_list' => $category_list
			];
			$users->home_page_data = $arrayList;
			$users->subscription = UserSubscription::select('id', 'name', 'price', 'days', 'type', 'description', DB::raw("DATE_FORMAT(start_date,'%b %d, %Y') as start_date"), DB::raw("DATE_FORMAT(end_date,'%b %d, %Y') as end_date"), DB::raw("DATE_FORMAT(created_at,'%b %d, %Y') as created_date"))->where('status', 'active')->where('user_id', $users->id)->first();
		}
		return $users;
	}

	public function customerProfileDetails($user) {
		$users = User::select('id', 'name', 'email', 'phone_code', 'mobile', 'avatar as image', 'referral_code', 'total_invite', DB::raw("DATE_FORMAT(dob,'%b %d, %Y') as dob"), DB::raw("DATE_FORMAT(created_at,'%b %d, %Y') as created_date"))->withoutAppends()->where('id', $user->id)->where('status', 'active')->first();
		return $users;
	}
	
	


		public function cmspage(Request $request,$slug) 
		{
			$this->checkKeys(array_keys($request->all()), array("device_id", "device_token", "device_type"));
			try {
						$page_content = CMS::select('id','name','slug','type','content','original_content')->where('status', 'active')->where('slug', $slug)->firstOrFail();
						

						if ($page_content) {
							$this->response = array(
								"status" => 200,
								"message" => ResponseMessages::getStatusCodeMessages(125),
								"data" => !empty($page_content) ? $page_content : null,
							);
						}else{
							$this->response = array(
								"status" => 300,
								"message" => ResponseMessages::getStatusCodeMessages(520),
								"data" => null,
							);
						}
					
			} catch (\Exception $ex) {
				$this->response = array(
					"status" => 501,
					"message" => ResponseMessages::getStatusCodeMessages(501),
					"data" => null,
				);
			}
			$this->shut_down();
				exit;
		}

		public function forgotPassword(Request $request) {
			// check keys are exist
			$this->checkKeys(array_keys($request->all()), ["email", 'device_id', 'device_token', 'device_type']);
			try {
				$input = $request->all();
				$validate = Validator($request->all(), [
				 	'email' => 'required|email',
				]);
				$attr = [
					'email' => 'Email',
				];
				$validate->setAttributeNames($attr);
				if ($validate->fails()) {
					$errors = $validate->errors();
					$this->response = array(
						"status" => 300,
						"message" => $errors->first(),
						"data" => null,
						"errors" => $errors,
					);
				}else{	
					if ($user = User::where("email", $request->email)->where('status', '!=', 'delete')->first()) {
						$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);

						$response = $this->broker()->sendResetLink(
				            $this->credentials($request)
				        );
						
						$this->response = array(
							"status" => Password::RESET_LINK_SENT
				                    ? 200
				                    : 300,
							"message" => Password::RESET_LINK_SENT
				                    ? 'Succefully sent register email id reset link'
				                    : 'Please wait before retrying.',
							"data" => null,
						);
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(5),
							"data" => null,
						);
					}
				}
			} catch (\Exception $ex) {
					$this->response = array(
						"status" => 501,
						"message" => ResponseMessages::getStatusCodeMessages(501),
						"data" => null,
					);
			}
			$this->shut_down();
			exit;
		}

		public function checkMobileExist(Request $request) {
			// check keys are exist
			$this->checkKeys(array_keys($request->all()), array("phone_code", "mobile"));
			try {
				$input = $request->all();
	  			$validate = Validator($request->all(), [
		            'phone_code' => 'required',
		            'mobile' => 'required',
		        ]);
		        $attr = [
		            'phone_code' => 'Phone Code',
		            'mobile' => 'Mobile',
		        ];
		        $validate->setAttributeNames($attr);
	        	if ($validate->fails()) {
					$errors = $validate->errors();
					$this->response = array(
						"status" => 300,
						"message" => $errors->first(),
						"data" => null,
						"errors" => $errors,
					);
				}else{
						
					if($checkedPhone = User::select('id', 'name', 'phone_code', 'mobile', 'email')->where("phone_code",$request->phone_code)->where("mobile",$request->mobile)->where("status","!=","delete")->first())
					{
						$this->response = array(
							"status" => 200,
							"message" => ResponseMessages::getStatusCodeMessages(571),
							"data" => $checkedPhone,
						);
					}else{
						$this->response = array(
							"status" => 300,
							"message" => ResponseMessages::getStatusCodeMessages(572),
							"data" => null,
						);
					}
				}
			} catch (\Exception $ex) {
				$this->response = array(
					"status" => 501,
					"message" => ResponseMessages::getStatusCodeMessages(501),
					"data" => null,
				);
			}
			$this->shut_down();
			exit;
		}

		public function statusUpdate(Request $request)
    	{
	        try {
	            $input =  $request->all();
	            $this->requestdata = $input;
	            $message = [
	                'id.required' => 'Id',
	                'model_name.required' => 'Model',
	                'key_name.required' => 'Key Name',
	                'value_name.required' => 'Value Name',
	            ];
	            $validator = Validator::make($input, [
	                'id'   => 'required|numeric',
	                'model_name'   => 'required',
	                'key_name'   => 'required',
	                'value_name'   => 'required',
	            ], $message);
	            if ($validator->fails()) {
	                $response = $this->errorValidation($validator);
	            } else {
	                
                    $modelName = $request->model_name;
                    $keyName = $request->key_name;
                    $keyValue = $request->value_name;
                    $updateData = [$keyName => (string)$keyValue];

                    $model = app("App\\$modelName");
                    $record = $model->where('id', $request->id)->update($updateData);
	                
	                $this->response = array(
						"status" => 200,
						"message" => 'Succefully update data',
						"data" => null,
					);
	            }
	        } catch (\Exception $ex) {
	            $this->response = array(
					"status" => 501,
					"message" => ResponseMessages::getStatusCodeMessages(501),
					"data" => null,
				);
	        }
        	$this->shut_down();
			exit;
    	}
	    public function broker()
	    {
	        return Password::broker();
	    }
	    protected function credentials(Request $request)
	    {
	        return $request->only('email');
	    }

	   	protected function sendResetLinkResponse(Request $request, $response)
	    {
	        /*return $request->wantsJson()
	                    ? new JsonResponse(['message' => trans($response)], 200)
	                    : back()->with('status', trans($response));*/

	        return $request->wantsJson()
	                    ? new JsonResponse(['message' => trans($response)], 200)
	                    : back()
	                ->with('success', trans($response));
	    }

	    /**
	     * Get the response for a failed password reset link.
	     *
	     * @param  \Illuminate\Http\Request  $request
	     * @param  string  $response
	     * @return \Illuminate\Http\RedirectResponse
	     *
	     * @throws \Illuminate\Validation\ValidationException
	     */
	    protected function sendResetLinkFailedResponse(Request $request, $response)
	    {
	        if ($request->wantsJson()) {
	            throw ValidationException::withMessages([
	                'email' => [trans($response)],
	            ]);
	        }

	        return back()
	                ->withInput($request->only('email'))
	                ->withErrors(['email' => trans($response)]);
	    }
		//Use for Item list


	//function call to customer_login
	public function customer_login(Request $request) {
		$this->checkKeys(array_keys($request->all()), ['email', 'password', 'device_id', 'device_token', 'device_type']);
		$access_token = '';
		try {
			if (in_array($request->device_type, ['android', 'ios', 'web'])) {  
				$rules = [
					'email' => 'required|email',
					'password' => 'required',
				];
				$validator = Validator::make($request->all(), $rules);
				$attr = [
					'email' => 'Email',
					'password' => 'Password'
				];
				$validator->setAttributeNames($attr);
	
				if ($validator->fails()) {
					$errors = $validator->errors();
					$this->response = [
						"status" => 300,
						"message" => $errors->first(),
						"data" => null,
						"errors" => $errors,
					];
				} else {
					$user = User::where("email", $request->email)->where('status', '!=', 'delete')->first();
					if (isset($user->id) && Hash::check($request->password, $user->password)) {
						if ($user->is_verified == '1') {
							if ($user->status == 'delete') {
								$this->response = [
									"status" => 300,
									"message" => ResponseMessages::getStatusCodeMessages(573),
									"data" => null,
									"access_token" => $access_token,
								];
							} else {
								$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
								Auth::login($user);
								$user->is_verified = '1';
								$user->save();
								$access_token = auth()->user()->createToken('Token')->accessToken;
								$userData = $this->customerProfileDetails($user);
								unset($userData->devices);
								// $notification_count = 0;
								// $des = 'Logged in successfully';
								// $push = [
								// 	'sender_id' => 1,
								// 	'notification_type' => 'login',
								// 	'notification_count' => $notification_count,
								// 	'title' => 'Laravel Login',
								// 	'description' => $des

								// ];
								// $account_type = 'user';
								// $this->pushNotificationSendActive($userData, $push, $account_type);
								$this->response = [
									"status" => 200,
									"message" => ResponseMessages::getStatusCodeMessages(7),
									"data" => !empty($userData) ? $userData : null,
									"access_token" => $access_token,
								];
							}
						} else {
							$this->response = [
								"status" => 300,
								"message" => ResponseMessages::getStatusCodeMessages(584),
								"data" => null,
								"access_token" => '',
							];
						}
										
						
					} else {
						$this->response = [
							"status" => 300,
							"message" => ResponseMessages::getStatusCodeMessages(108),
							"data" => null,
							"access_token" => $access_token,
						];
					}
				}
			} else {
				$this->response = [
					"status" => 300,
					"message" => ResponseMessages::getStatusCodeMessages(515),
					"data" => null,
				];
			}
		} catch (\Exception $ex) {
			$this->response = [
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
				"data" => null,
			];
		}
		$this->shut_down();
		exit;
	}
	//function call to customer_signUp
	public function customer_signUp(Request $request){
		$this->checkKeys(array_keys($request->all()), array('name', 'email', 'phone_code', 'mobile', 'password', 'confirm_password', "device_id", "device_token", "device_type"));
		try{
			$input = $request->all();
				$validate = Validator($request->all(), [
					// 'device_id'=>'required',
					// 'device_token'=>'required',
					// 'device_type'=>'required',
	            'name' => 'required',
	            'email' => 'required|email',
				'phone_code' => 'required',
	            'mobile' => 'required|min:8|numeric',
				'password' => 'required|same:confirm_password',
	        ]);
	        $attr = [
	        	// 'device_id'=>"Device Id",
	        	// 'device_token'=>"Device Token",
	        	// 'device_type'=>"Device Type",
	            'name' => 'Contact Person Name',
	            'email' => 'Email',
				'phone_code' =>'Country Code',
	            'mobile' => 'Phone No',
				'password' => 'Password',
	        ];
	        $validate->setAttributeNames($attr);
	        if ($validate->fails()) {
				$errors = $validate->errors();
				$this->response = array(
					"status" => 300,
					"message" => $errors->first(),
					"data" => null,
					"errors" => $errors,
				);
			}else{
				if (($request->device_type == 'android') || ($request->device_type == 'ios') || ($request->device_type == 'web')) {
					if ($users = User::where("email", $request->email)->where('status', '!=', 'delete')->first()) {
						$this->response = array(
							"status" => 300,
							"message" => ResponseMessages::getStatusCodeMessages(2),
							"data" => null,
						);
					}else{
						$users = new User;
						$users->name = $input['name'];
						$users->email = $input['email'];
						$users->password =Hash::make($input['password']);
						$users->temp_password =$input['password'];
						$users->phone_code = $input['phone_code'];
						$users->mobile = $input['mobile'];
						$users->role = 'Customer';	
						$users->is_verified = '1';	


									
						if ($request->hasfile('image')) {
                            $file = $request->file('image');
                            $filename = time() . $file->getClientOriginalName();
                            $filename = str_replace(' ', '', $filename);
                            $filename = str_replace('.jpeg', '.jpg', $filename);
                            $file->move(public_path('img/avatars'), $filename);
                            if ($users->avatar != null && file_exists(public_path('img/avatars/' . $users->avatar))) {
                                if ($users->avatar != 'no_avatar.jpg') {
                                    unlink(public_path('img/avatars/' . $users->avatar));
                                }
                            }
                            $users->avatar = $filename;
                        }
						if($users->save()){
							
							$users->referral_code = $this->generateReferralCode($users->id);
							$users->save();

							if (isset($request->invite_code) && !empty($request->invite_code)) {
								if ($invite_user = User::where('referral_code', $request->invite_code)->first()) {
									$invite_user->total_invite = $invite_user->total_invite + 1;
									$invite_user->save();
									$UserInvite = new UserInvite;
									$UserInvite->user_id = $invite_user->id;
									$UserInvite->user_invite_id = $users->id;
									$UserInvite->use_code = $request->invite_code;
									$UserInvite->status = 'active';
									$UserInvite->created_at = date('Y-m-d H:i:s');
								    $UserInvite->updated_at = date('Y-m-d H:i:s');
								    $UserInvite->save();

								}
							}
							/*if ($subscription = Subscription::where('id', 1)->first()) {
								$day = $subscription->days;
								UserSubscription::where('user_id', $users->id)->update(['status'=>'out']);
								$newSubscription = new UserSubscription;
								$newSubscription->user_id = $users->id;
								$newSubscription->subscription_id = $subscription->id;
								$newSubscription->name = $subscription->name;
								$newSubscription->price = $subscription->price;
								$newSubscription->days = $subscription->days;
								$newSubscription->type = $subscription->type;
								$newSubscription->description = $subscription->description;
								$newSubscription->status = 'active';
								$newSubscription->created_at = date('Y-m-d H:i:s');
							    $newSubscription->updated_at = date('Y-m-d H:i:s');
							    if ($newSubscription->save()) {
							    	$currentDate = date('Y-m-d');
							    	if ($users->subscription_expire_date != '') {
							    		if (strtotime($users->subscription_expire_date) >= strtotime($currentDate)) {
							    			$currentDate = date('Y-m-d', strtotime($users->subscription_expire_date)); 
											$final_date = date('Y-m-d', strtotime($currentDate . ' + '.$day.' days'));
							    		}else{
							    			$final_date = date('Y-m-d', strtotime($currentDate . ' + '.$day.' days'));
							    		}
							    		
							    	}else{
							    		$final_date = date('Y-m-d', strtotime($currentDate . ' + '.$day.' days'));
							    	}
							    	$users->subscription_id = $subscription->id;
							    	$users->subscription_expire_date = $final_date;
							    	$users->save();

							    	$newSubscription->start_date = $currentDate;
							    	$newSubscription->end_date = $final_date;
							    	$newSubscription->save();
							    }
							}*/

							$this->updateUserDevice($users->id, $request->device_id, $request->device_token, $request->device_type);
							/*$getEmailTemplate = EmailTemplate::where('id', 2)->select(['name', 'subject', 'description','footer'])->first();
							$record = (object)[];
							$description = $getEmailTemplate->description;
							$description = str_replace("{name}", ucfirst($users->name), $description);
							$description = str_replace("{email}", $users->email, $description);
							$description = str_replace("{password}", $input['password'], $description);
							$description = str_replace("{phone_code}", $users->phone_code, $description);
              				$description = str_replace("{mobile}", $users->mobile, $description);
							$record->description= $description;
							$record->footer = $getEmailTemplate->footer;
							$record->subject = "Login Password Code";
							$subject = "Sign Up  Password Code";
						
							if ($users->email != '') {
								Mail::send('emails.welcome', compact('record'), function ($message) use ($users, $subject) {
									$message->to(trim($users->email), config('app.name'))->subject($subject);
									$message->from(env('MAIL_FROM_ADDRESS'), config('app.name'));
								});
							}*/
							$notification_count = 0;
							$des = 'Welcome to Laravel! We are excited to have you on board.';
							$push = [
								'sender_id' => 1,
								'notification_type' => 'User Register',
								'notification_count' => $notification_count,
								'title' => 'Welcome to Laravel',
								'description' => $des
							];
							$account_type = 'user';
							$this->pushNotificationRegister($users, $push, $account_type);
							$this->response = array(
								"status" => 200,
								"message" => ResponseMessages::getStatusCodeMessages(558),
								/*"data" => !empty($users) ? $users : null,*/
								"data" => null,
							);
						}
					}
				}else{
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(515),
						"data" => null
					);
				}
			}
		}catch (\Exception $ex) {
			$this->response = array(
				"status" => 300,
				"message" => ResponseMessages::getStatusCodeMessages(501),
				"data" => $ex,
			);
		}
	  	$this->shut_down();
		exit;
	}



	//function call to tablet_login
	public function tablet_login(Request $request) {
		$this->checkKeys(array_keys($request->all()), ['email', 'password', 'device_id', 'device_token', 'device_type']);
		$access_token = '';
		try {
			if (in_array($request->device_type, ['android', 'ios', 'web'])) {  
				$rules = [
					'email' => 'required|email',
					'password' => 'required',
				];
				$validator = Validator::make($request->all(), $rules);
				$attr = [
					'email' => 'Email',
					'password' => 'Password'
				];
				$validator->setAttributeNames($attr);
	
				if ($validator->fails()) {
					$errors = $validator->errors();
					$this->response = [
						"status" => 300,
						"message" => $errors->first(),
						"data" => null,
						"errors" => $errors,
					];
				} else {
					$user = User::where("email", $request->email)->where('status', '!=', 'delete')->first();
					if (isset($user->id) && Hash::check($request->password, $user->password)) {
						if ($user->is_verified == '1') {
							if ($user->status == 'delete') {
								$this->response = [
									"status" => 300,
									"message" => ResponseMessages::getStatusCodeMessages(573),
									"data" => null,
									"access_token" => $access_token,
								];
							} else {
								$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
								Auth::login($user);
								$user->is_verified = '1';
								$user->save();
								$access_token = auth()->user()->createToken('Token')->accessToken;
								$userData = $this->customerProfileDetails($user);
								unset($userData->devices);
								// $notification_count = 0;
								// $des = 'Logged in successfully';
								// $push = [
								// 	'sender_id' => 1,
								// 	'notification_type' => 'login',
								// 	'notification_count' => $notification_count,
								// 	'title' => 'Laravel Login',
								// 	'description' => $des

								// ];
								// $account_type = 'user';
								// $this->pushNotificationSendActive($userData, $push, $account_type);
								$this->response = [
									"status" => 200,
									"message" => ResponseMessages::getStatusCodeMessages(7),
									"data" => !empty($userData) ? $userData : null,
									"access_token" => $access_token,
								];
							}
						} else {
							$this->response = [
								"status" => 300,
								"message" => ResponseMessages::getStatusCodeMessages(584),
								"data" => null,
								"access_token" => '',
							];
						}
										
						
					} else {
						$this->response = [
							"status" => 300,
							"message" => ResponseMessages::getStatusCodeMessages(108),
							"data" => null,
							"access_token" => $access_token,
						];
					}
				}
			} else {
				$this->response = [
					"status" => 300,
					"message" => ResponseMessages::getStatusCodeMessages(515),
					"data" => null,
				];
			}
		} catch (\Exception $ex) {
			$this->response = [
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
				"data" => null,
			];
		}
		$this->shut_down();
		exit;
	}
	//function call to tablet_signUp
	public function tablet_signUp(Request $request){
		$this->checkKeys(array_keys($request->all()), array('name', 'email', 'phone_code', 'mobile', 'password', 'confirm_password', "device_id", "device_token", "device_type"));
		try{
			$input = $request->all();
				$validate = Validator($request->all(), [
					// 'device_id'=>'required',
					// 'device_token'=>'required',
					// 'device_type'=>'required',
	            'name' => 'required',
	            'email' => 'required|email',
				'phone_code' => 'required',
	            'mobile' => 'required|min:8|numeric',
				'password' => 'required|same:confirm_password',
	        ]);
	        $attr = [
	        	// 'device_id'=>"Device Id",
	        	// 'device_token'=>"Device Token",
	        	// 'device_type'=>"Device Type",
	            'name' => 'Contact Person Name',
	            'email' => 'Email',
				'phone_code' =>'Country Code',
	            'mobile' => 'Phone No',
				'password' => 'Password',
	        ];
	        $validate->setAttributeNames($attr);
	        if ($validate->fails()) {
				$errors = $validate->errors();
				$this->response = array(
					"status" => 300,
					"message" => $errors->first(),
					"data" => null,
					"errors" => $errors,
				);
			}else{
				if (($request->device_type == 'android') || ($request->device_type == 'ios') || ($request->device_type == 'web')) {
					if ($users = User::where("email", $request->email)->where('status', '!=', 'delete')->first()) {
						$this->response = array(
							"status" => 300,
							"message" => ResponseMessages::getStatusCodeMessages(2),
							"data" => null,
						);
					}else{
						$users = new User;
						$users->name = $input['name'];
						$users->email = $input['email'];
						$users->password =Hash::make($input['password']);
						$users->temp_password =$input['password'];
						$users->phone_code = $input['phone_code'];
						$users->mobile = $input['mobile'];
						$users->role = 'Tablet';	
						$users->is_verified = '1';	

									
						if ($request->hasfile('image')) {
                            $file = $request->file('image');
                            $filename = time() . $file->getClientOriginalName();
                            $filename = str_replace(' ', '', $filename);
                            $filename = str_replace('.jpeg', '.jpg', $filename);
                            $file->move(public_path('img/avatars'), $filename);
                            if ($users->avatar != null && file_exists(public_path('img/avatars/' . $users->avatar))) {
                                if ($users->avatar != 'no_avatar.jpg') {
                                    unlink(public_path('img/avatars/' . $users->avatar));
                                }
                            }
                            $users->avatar = $filename;
                        }
						if($users->save()){
							
							$users->referral_code = $this->generateReferralCode($users->id);
							$users->save();

							if (isset($request->invite_code) && !empty($request->invite_code)) {
								if ($invite_user = User::where('referral_code', $request->invite_code)->first()) {
									$invite_user->total_invite = $invite_user->total_invite + 1;
									$invite_user->save();
									$UserInvite = new UserInvite;
									$UserInvite->user_id = $invite_user->id;
									$UserInvite->user_invite_id = $users->id;
									$UserInvite->use_code = $request->invite_code;
									$UserInvite->status = 'active';
									$UserInvite->created_at = date('Y-m-d H:i:s');
								    $UserInvite->updated_at = date('Y-m-d H:i:s');
								    $UserInvite->save();

								}
							}
							/*if ($subscription = Subscription::where('id', 1)->first()) {
								$day = $subscription->days;
								UserSubscription::where('user_id', $users->id)->update(['status'=>'out']);
								$newSubscription = new UserSubscription;
								$newSubscription->user_id = $users->id;
								$newSubscription->subscription_id = $subscription->id;
								$newSubscription->name = $subscription->name;
								$newSubscription->price = $subscription->price;
								$newSubscription->days = $subscription->days;
								$newSubscription->type = $subscription->type;
								$newSubscription->description = $subscription->description;
								$newSubscription->status = 'active';
								$newSubscription->created_at = date('Y-m-d H:i:s');
							    $newSubscription->updated_at = date('Y-m-d H:i:s');
							    if ($newSubscription->save()) {
							    	$currentDate = date('Y-m-d');
							    	if ($users->subscription_expire_date != '') {
							    		if (strtotime($users->subscription_expire_date) >= strtotime($currentDate)) {
							    			$currentDate = date('Y-m-d', strtotime($users->subscription_expire_date)); 
											$final_date = date('Y-m-d', strtotime($currentDate . ' + '.$day.' days'));
							    		}else{
							    			$final_date = date('Y-m-d', strtotime($currentDate . ' + '.$day.' days'));
							    		}
							    		
							    	}else{
							    		$final_date = date('Y-m-d', strtotime($currentDate . ' + '.$day.' days'));
							    	}
							    	$users->subscription_id = $subscription->id;
							    	$users->subscription_expire_date = $final_date;
							    	$users->save();

							    	$newSubscription->start_date = $currentDate;
							    	$newSubscription->end_date = $final_date;
							    	$newSubscription->save();
							    }
							}*/

							$this->updateUserDevice($users->id, $request->device_id, $request->device_token, $request->device_type);
							/*$getEmailTemplate = EmailTemplate::where('id', 2)->select(['name', 'subject', 'description','footer'])->first();
							$record = (object)[];
							$description = $getEmailTemplate->description;
							$description = str_replace("{name}", ucfirst($users->name), $description);
							$description = str_replace("{email}", $users->email, $description);
							$description = str_replace("{password}", $input['password'], $description);
							$description = str_replace("{phone_code}", $users->phone_code, $description);
              				$description = str_replace("{mobile}", $users->mobile, $description);
							$record->description= $description;
							$record->footer = $getEmailTemplate->footer;
							$record->subject = "Login Password Code";
							$subject = "Sign Up  Password Code";
						
							if ($users->email != '') {
								Mail::send('emails.welcome', compact('record'), function ($message) use ($users, $subject) {
									$message->to(trim($users->email), config('app.name'))->subject($subject);
									$message->from(env('MAIL_FROM_ADDRESS'), config('app.name'));
								});
							}*/
							$notification_count = 0;
							$des = 'Welcome to Laravel! We are excited to have you on board.';
							$push = [
								'sender_id' => 1,
								'notification_type' => 'User Register',
								'notification_count' => $notification_count,
								'title' => 'Welcome to Laravel',
								'description' => $des
							];
							$account_type = 'user';
							$this->pushNotificationRegister($users, $push, $account_type);
							$this->response = array(
								"status" => 200,
								"message" => ResponseMessages::getStatusCodeMessages(558),
								/*"data" => !empty($users) ? $users : null,*/
								"data" => null,
							);
						}
					}
				}else{
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(515),
						"data" => null
					);
				}
			}
		}catch (\Exception $ex) {
			$this->response = array(
				"status" => 300,
				"message" => ResponseMessages::getStatusCodeMessages(501),
				"data" => $ex,
			);
		}
	  	$this->shut_down();
		exit;
	}

		public function itemList(Request $request) {
				// check keys are exist
				$this->checkKeys(array_keys($request->all()), array("page"));
				try {
					$input = $request->all();
					$validate = Validator($request->all(), [
					 	'page' => 'required|numeric'
					]);
					$attr = [
						'page' => 'Page No',
					];
					$validate->setAttributeNames($attr);
					if ($validate->fails()) {
							$errors = $validate->errors();
							$this->response = array(
								"status" => 300,
								"message" => $errors->first(),
								"data" => null,
								"errors" => $errors,
							);
					}else{	
									if (isset($request->page) && !empty($request->page)) {
										$page_no = $request->page - 1;
									}else{
										$page_no = 0;
									}
									$query = Item::select('id', 'name', 'vendor_id')->where("status","active");
									
									$data = $query->orderBy('name', 'asc')->offset($page_no*20)->take(5000)->get();
									if ($data->count() > 0) {
										$this->response = array(
											"status" => 200,
											"message" => ResponseMessages::getStatusCodeMessages(125),
											"data" => !empty($data) ? $data : null,
										);
									} else {
										$this->response = array(
											"status" => 300,
											"message" => ResponseMessages::getStatusCodeMessages(507),
											"data" => null,
										);
									}
					}
				} catch (\Exception $ex) {
					
						$this->response = array(
							"status" => 501,
							"message" => ResponseMessages::getStatusCodeMessages(501),
							"data" => null,
						);
				}
				$this->shut_down();
				exit;
		}
	
	


		
		

		

		
		//Use for country list
		public function countryList(Request $request) {
				// check keys are exist
				//$this->checkKeys(array_keys($request->all()), array("page"));
				try {
						$input = $request->all();
						$validate = Validator($request->all(), [
					//  'page' => 'required|numeric'
					]);
					$attr = [
					// 'page' => 'Page No',
					];
					$validate->setAttributeNames($attr);
					if ($validate->fails()) {
							$errors = $validate->errors();
							$this->response = array(
								"status" => 300,
								"message" => $errors->first(),
								"data" => null,
								"errors" => $errors,
							);
					}else{
									
									if (isset($request->page) && !empty($request->page)) {
										$page_no = $request->page - 1;
									}else{
										$page_no = 0;
									}
									$query = Country::select('id', 'name', 'phonecode')->where("status","active");
									
									$data = $query->orderBy('name', 'asc')->offset($page_no*20)->take(5000)->get();
									if ($data->count() > 0) {
										$this->response = array(
											"status" => 200,
											"message" => ResponseMessages::getStatusCodeMessages(125),
											"data" => !empty($data) ? $data : null,
										);
									} else {
										$this->response = array(
											"status" => 300,
											"message" => ResponseMessages::getStatusCodeMessages(507),
											"data" => null,
										);
									}
					}
				} catch (\Exception $ex) {
						$this->response = array(
							"status" => 501,
							"message" => ResponseMessages::getStatusCodeMessages(501),
							"data" => null,
						);
				}
				$this->shut_down();
				exit;
		}

		//Use for state list
		public function stateList(Request $request) {
			
			// check keys are exist
			$this->checkKeys(array_keys($request->all()), array("country_id"));
			try {

					$input = $request->all();
  				$validate = Validator($request->all(), [
	            //'page' => 'required|numeric',
	            'country_id' => 'required|numeric'
	        ]);
	        $attr = [
	           // 'page' => 'Page No',
	            'country_id' => 'Country Id',
	        ];
	        $validate->setAttributeNames($attr);
	        if ($validate->fails()) {
							$errors = $validate->errors();
							$this->response = array(
								"status" => 300,
								"message" => $errors->first(),
								"data" => null,
								"errors" => $errors,
							);
					}else{
							if (isset($request->page) && !empty($request->page)) {
								$page_no = $request->page - 1;
							}else{
								$page_no = 0;
							}
							$query = State::select("id","name")->where("status","active")->where("country_id",$request->country_id);
						
							$data = $query->orderBy('name', 'asc')->offset($page_no*20)->take(5000)->get();
							
							if ($data->count() > 0) {
								$this->response = array(
									"status" => 200,
									"message" => ResponseMessages::getStatusCodeMessages(125),
									"data" => !empty($data) ? $data : null,
								);
							} else {
								$this->response = array(
									"status" => 300,
									"message" => ResponseMessages::getStatusCodeMessages(507),
									"data" => null,
								);
							}
					}
			} catch (\Exception $ex) {
				$this->response = array(
					"status" => 501,
					"message" => ResponseMessages::getStatusCodeMessages(501),
					"data" => null,
				);
			}
			$this->shut_down();
			exit;
		}
		//Use for city list
		public function cityList(Request $request) {
				// check keys are exist
				$this->checkKeys(array_keys($request->all()), array("state_id"));
				try {
					$input = $request->all();
	  				$validate = Validator($request->all(), [
		           // 'page' => 'required|numeric',
		            'state_id' => 'required|numeric'
		        ]);
		        $attr = [
		           // 'page' => 'Page No',
		            'state_id' => 'State Id',
		        ];
		        $validate->setAttributeNames($attr);
		        if ($validate->fails()) {
								$errors = $validate->errors();
								$this->response = array(
									"status" => 300,
									"message" => $errors->first(),
									"data" => null,
									"errors" => $errors,
								);
						}else{
								if (isset($request->page) && !empty($request->page)) {
									$page_no = $request->page - 1;
								}else{
									$page_no = 0;
								}
								$query = City::select('id', 'name')->where('state_id', $request->state_id)->where("status","active");
								$data = $query->orderBy('name', 'asc')->offset($page_no*20)->take(5000)->get();
								if ($data->count() > 0) {
										$this->response = array(
											"status" => 200,
											"message" => ResponseMessages::getStatusCodeMessages(125),
											"data" => !empty($data) ? $data : null,
										);
								} else {
										$this->response = array(
											"status" => 300,
											"message" => ResponseMessages::getStatusCodeMessages(507),
											"data" => null,
										);
								}
						}
				} catch (\Exception $ex) {
					$this->response = array(
						"status" => 501,
						"message" => ResponseMessages::getStatusCodeMessages(501),
						"data" => null,
					);
				}
				$this->shut_down();
				exit;
		}	
			
		//Use for upload Image
		public function uploadImage(Request $request) {
				// check keys are exist
				$this->checkKeys(array_keys($request->all()), array("type"));
				try {
					$input = $request->all();
	  				$validate = Validator($request->all(), [
		            'type' => 'required|in:user',
		        ]);
		        $attr = [
		            'type' => 'Type',
		        ];
		        $validate->setAttributeNames($attr);
		        if ($validate->fails()) {
								$errors = $validate->errors();
								$this->response = array(
									"status" => 300,
									"message" => $errors->first(),
									"data" => null,
									"errors" => $errors,
								);
						}else{
								if ($request->hasfile('image')) {
										$img = '';
										if ($request->type == 'user') {
											$file = $request->file('image');
	                    $filename = time() . $file->getClientOriginalName();
	                    $filename = str_replace(' ', '', $filename);
	                    $filename = str_replace('.jpeg', '.jpg', $filename);
	                    $file->move(public_path('img/avatars'), $filename);
	                    
	                    $img = asset('img/avatars').'/'.$filename;
										}
										if ($img != '') {
											 $this->response = array(
												"status" => 200,
												"message" => ResponseMessages::getStatusCodeMessages(218),
												"data" => !empty($img) ? $img : null,
											);
										}else{
												$this->response = array(
												"status" => 300,
												"message" => ResponseMessages::getStatusCodeMessages(502),
												"data" => null,
											);
										}
                }else{
                		$this->response = array(
											"status" => 300,
											"message" => ResponseMessages::getStatusCodeMessages(502),
											"data" => null,
										);
                }
						}
				} catch (\Exception $ex) {
					$this->response = array(
						"status" => 501,
						"message" => ResponseMessages::getStatusCodeMessages(501),
						"data" => null,
					);
				}
				$this->shut_down();
				exit;
		}	

	public function settingRuleOld(Request $request) {
		$this->checkKeys(array_keys($request->all()), array("device_id", "device_token", "device_type", "rule_name"));
		try {
			if (($request->device_type == 'android') || ($request->device_type == 'ios') || ($request->device_type == 'web')) {
				if ($request->rule_name == 'currency' || $request->rule_name == 'sms_sender_id' || $request->rule_name == 'image_quality' || $request->rule_name == 'sender_id' || $request->rule_name == 'png_image_quality' || $request->rule_name == 'referrer_amount' || $request->rule_name == 'refer_share_message' || $request->rule_name == 'android_url_user' || $request->rule_name == 'call_us' || $request->rule_name == 'legal') {
					if ((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
						if ($request->device_type != 'web') {
							$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
						}
					}
					$this->response = array(
						"status" => 200,
						"message" => ResponseMessages::getStatusCodeMessages(125),
						"data" => BusRuleRef::where("rule_name", $request->rule_name)->first()->rule_value
					);
				}else{
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(530),
						"data" => null,
					);
				}
			}else{
				$this->response = array(
					"status" => 300,
					"message" => ResponseMessages::getStatusCodeMessages(515),
					"data" => null,
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
				"data" => null,
			);
		}

		$this->shut_down();
			exit;
	}

	public function settingRule(Request $request) {
		$this->checkKeys(array_keys($request->all()), array("device_id", "device_token", "device_type"));
		try {
			if (($request->device_type == 'android') || ($request->device_type == 'ios') || ($request->device_type == 'web')) {
					if ((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
						if ($request->device_type != 'web') {
							$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
						}
					}
					$this->response = array(
						"status" => 200,
						"message" => ResponseMessages::getStatusCodeMessages(125),
						"data" => BusRuleRef::select('id', 'name', 'rule_name', 'rule_value')->get()
					);
				}else{
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(530),
						"data" => null,
					);
				}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
				"data" => null,
			);
		}

		$this->shut_down();
			exit;
	}

	// function called to display email Susbscriber sections
    public function emailSusbscriber(Request $request) {
        // check keys are exist
        $this->checkKeys(array_keys($request->all()), array("email"));
        try {
            $rules = array(
                'email' => 'required|email',
            );
            $validate = Validator($request->all(), $rules);
            $attr = [
                'email' => 'Email',
            ];  
            $validate->setAttributeNames($attr);

            if ($validate->fails()) {
                $errors = $validate->errors();
                $response['status'] = 300;
                $response['message'] = $errors->first();
                $response['data'] = null;
                $response['errors'] = $errors;
            } else {
                if ($newsletter = Newsletter::where('email', $request->email)->where('status', '!=', 'delete')->first()) {
                    $newsletter->updated_at = date('Y-m-d H:i:s'); 
                    $returnMessage = 'Email already subscribers';
                }else{
                    $newsletter = new Newsletter;
                    $newsletter->email = $request->email; 
                    $newsletter->status = 'active'; 
                    $newsletter->created_at = date('Y-m-d H:i:s'); 
                    $newsletter->updated_at = date('Y-m-d H:i:s'); 
                    $returnMessage = 'Succefully subscribers email';
                }
                if ($newsletter->save()) {
                    $response['status'] = 200;
                    $response['message'] = $returnMessage;
                    $response['data'] = null;
                }else{
                    $response['status'] = 300;
                    $response['message'] = "Something went wrong!";
                    $response['data'] = null;
                }
            }
        } catch (\Exception $ex) {
            $response['status'] = 501;
            $response['message'] = "Something went wrong!";
            $response['data'] = null;
        }
        return response()->json($response, 200);
        exit;
    }
   // function called to display contactUs sections
    public function contactUs(Request $request) {
        // check keys are exist
        $this->checkKeys(array_keys($request->all()), array("email"));
        try {
            $rules = array(
                'first_name' => 'required',
                'last_name' => 'required',
                'country_code' => 'required',
                'email' => 'required|email',
                'mobile_number' => 'required|numeric',
                'state' => 'required',
                'city' => 'required',
                'message' => 'required',
            );
            $validate = Validator($request->all(), $rules);
            $attr = [
                'first_name' => 'First Name',
                'last_name' => 'Last Name',
                'country_code' => 'Country Code',
                'email' => 'Email',
                'mobile_number' => 'Mobile',
                'state' => 'State',
                'city' => 'City',
                'message' => 'Message',
            ];  
            $validate->setAttributeNames($attr);

            if ($validate->fails()) {
                $errors = $validate->errors();
                $response['status'] = 300;
                $response['message'] = $errors->first();
                $response['data'] = null;
                $response['errors'] = $errors;
            } else {
                /*if ($newsletter = ContactUs::where('email', $request->email)->where('status', '!=', 'delete')->first()) {
                    $newsletter->updated_at = date('Y-m-d H:i:s'); 
                    $returnMessage = 'Already request this email id Contact Us';
                }else{*/
                    $newsletter = new ContactUs;
                    $newsletter->name = ucfirst($request->first_name).' '.ucfirst($request->last_name); 
                    $newsletter->first_name = ucfirst($request->first_name); 
                    $newsletter->last_name = ucfirst($request->last_name); 
                    $newsletter->country_code = $request->country_code; 
                    $newsletter->mobile = $request->mobile_number; 
                    $newsletter->state = $request->state; 
                    $newsletter->city = $request->city; 
                    $newsletter->message = $request->message; 
                    $newsletter->email = $request->email; 
                    $newsletter->status = 'active'; 
                    $newsletter->created_at = date('Y-m-d H:i:s'); 
                    $newsletter->updated_at = date('Y-m-d H:i:s'); 
                    $returnMessage = 'Succefully requested contact us';
                /*}*/
                if ($newsletter->save()) {
                    $response['status'] = 200;
                    $response['message'] = $returnMessage;
                    $response['data'] = null;
                }else{
                    $response['status'] = 300;
                    $response['message'] = "Something went wrong!";
                    $response['data'] = null;
                }
            }
        } catch (\Exception $ex) {
            $response['status'] = 501;
            $response['message'] = "Something went wrong!";
            $response['data'] = null;
        }
        return response()->json($response, 200);
        exit;
    }
}

	
