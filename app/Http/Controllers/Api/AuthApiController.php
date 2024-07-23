<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use App\Library\Helper;
use App\Library\Notify;
use App\Library\ResponseMessages;
use App\User;
use App\UserOTP;
use App\UserDevice;
use App\BusRuleRef;
use App\Notificationuser;
use App\Transaction;
use App\BoxPrice;
use App\BoxPurchase;
use App\ContestParticipant;
use App\ContestJoin;
use App\Gift;
use App\Brand;
use App\CMS;
use App\Item;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\PurchaseItem;
use App\VendorCustomer;
use App\PostOfferRequest;
use App\VendorSalePerson;
use App\VendorAttribute;
use App\VendorProduct;
use App\Vouchar;
use App\Subscription;
use App\UserSubscription;
use App\RestaurantIngredient;
use App\PurchaseItemAttribute;
use App\Category;
use App\RestaurantCategory;
use App\RestaurantItem;
use App\RestaurantItemIngredient;
use App\ItemOrder;
use App\RestaurantOrder;
use App\RestaurantOrderItem;
use App\RestaurantOrderItemIngredient;
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
use Log;

class AuthApiController extends Controller {
	
	public function __construct()
    {
        $this->middleware('auth');
    }

	
	public function logout(Request $request) {
		$this->checkKeys(array_keys($request->all()), array("device_id", "device_token", "device_type"));
		try {
			if (($request->device_type == 'android') || ($request->device_type == 'ios') || ($request->device_type == 'web')) {
				if ((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
					$user->is_verified = '0';
					$user->save();
					$this->logoutUserDevice($user->id, $request->device_id, $request->device_token);
					$this->response = array(
						"status" => 200,
						"message" => ResponseMessages::getStatusCodeMessages(10),
						"data" => null,
					);
				} else {
					$this->response = array(
						"status" => 403,
						"message" => ResponseMessages::getStatusCodeMessages(5),
						"data" => null,
					);
				}
			}else{
				$this->response = array(
					"status" => 300,
					"message" => ResponseMessages::getStatusCodeMessages(214),
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

	public function homePageData(Request $request) {
		$this->checkKeys(array_keys($request->all()), array("device_id", "device_token", "device_type"));
		try {
			if (($request->device_type == 'android') || ($request->device_type == 'ios') || ($request->device_type == 'web')) {
				if ((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
					$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);

					$userData = $this->userProfileDetails($user);
					if ($userData) {
						$this->response = array(
							"status" => 200,
							"message" => ResponseMessages::getStatusCodeMessages(125),
							"data" => !empty($userData) ? $userData : null,
						);
					}else{
						$this->response = array(
							"status" => 300,
							"message" => ResponseMessages::getStatusCodeMessages(520),
							"data" => null,
						);
					}
				} else {
					$this->response = array(
						"status" => 403,
						"message" => ResponseMessages::getStatusCodeMessages(5),
						"data" => null,
					);
				}
			}
			else{
				$this->response = array(
					"status" => 300,
					"message" => ResponseMessages::getStatusCodeMessages(214),
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

	//function call to addcustomer
	public function addCustomer(Request $request) {
		$this->checkKeys(array_keys($request->all()), array("name", "phone_code", "mobile", "type", "category_id", "device_id", "device_token", "device_type"));
		$access_token = '';
		try {
			$input = $request->all();
  			$validate = Validator($request->all(), [
	            'name' => 'required|regex:/^[a-zA-Z]+$/',
				'phone_code' => 'required',
		        'mobile' => 'required|min:8|numeric',
		        'category_id' => 'required|numeric',
				'type' => 'required',
	        ]);
	        $attr = [
	            'name' => 'Customer Name',
				'phone_code' => 'Country Code',
		        'mobile' => 'Phone No',
		        'category_id' => 'Category Id',
				'type' => 'Shopping Type',
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
					if ($user = User::where('id', Auth::user()->id)->first()) {
						$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
						
						// Check if a user with the provided mobile number already exists
                    if ($existingUser = User::where('status', '!=', 'delete')->where('mobile', $input['mobile'])->first()) {
                        // If a user with the same mobile number exists, compare the names
                        if ($existingUser->name != $input['name']) {
                            // If the names are different, return an error
                            $this->response = array(
                                "status" => 300,
                                "message" => "A different user with the same mobile number already exists.",
                                "data" => null,
                            );
                            $this->shut_down();
                            exit;
                        }
                    }
					
						if ($customers = User::where('status', '!=', 'delete')->where('mobile', $input['mobile'])->first()) {
							
						}else{
							$password = $this->generateReferralCode(1);
							$customers = new User;
							$customers->password = Hash::make($password);
							$customers->role = 'Customer';
						}
						$customers->name = $input['name'];
						$customers->phone_code = $input['phone_code'];
						$customers->mobile = $input['mobile'];
						if ($customers->save()) {
							/*$purchaseitem = new PurchaseItem;
							$purchaseitem->user_id = $customers->id;
							$purchaseitem->item_id = $input['item_id'];
							$purchaseitem->save();*/
							if (!VendorCustomer::where('vendor_id', $user->id)->where('customer_id', $customers->id)->first()) {
								$orderType = $input['type'];
							}else{
								$orderType = 'Repeat';	
							}


							$vendor_customer = new VendorCustomer;
							$vendor_customer->vendor_id = $user->id;
							$vendor_customer->customer_id = $customers->id;
							$vendor_customer->type = $orderType;
							$vendor_customer->category_id = $request->category_id;
							$vendor_customer->save();

							$userData = $this->userProfileDetails($user);
							$this->response = array(
								"status" => 200,
								"message" => ResponseMessages::getStatusCodeMessages(574),
								"data" => !empty($userData) ? $userData : null,
							);
						}else{
							$userData = $this->userProfileDetails($user);
							$this->response = array(
								"status" => 300,
								"message" => ResponseMessages::getStatusCodeMessages(123),
								"data" => !empty($userData) ? $userData : null,
							);
						}
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(5),
							"data" => null,
						);
					}
				}
				else{
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(214),
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

	//Use for Added Customer list
	public function customerList(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("from_date", "end_date", "search", "list_type", "page", "device_id", "device_token", "device_type"));
		try {
			$input = $request->all();
			$validate = Validator($request->all(), [
			 	'page' => 'required|numeric',
			 	'list_type' => 'required|in:Visitor,Repeat,Purchase',
			]);
			$attr = [
				'page' => 'Page No',
				'list_type' => 'List Type',
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
					if ((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
						$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
						if (isset($request->page) && !empty($request->page)) {
							$page_no = $request->page - 1;
						}else{
							$page_no = 0;
						}
						$query = VendorCustomer::select('id', 'vendor_id', 'customer_id', 'type', 'product', 'feedback', DB::raw("DATE_FORMAT(created_at,'%b %d, %Y') as visit_date"))
								->with(['customer' => function ($q) {
									$q->select('id', 'name', 'phone_code', 'mobile');
								}])->whereHas('customer', function ($q) {
								   // $q->where('status', '!=', 'delete');
								    if (isset($request->search) && !empty($request->search)) {
							            $q->where(function ($q1) use ($request) {
							                $q1->where('name', 'like', '%' . $request->search . '%');
							            });
							        }
								});
						if (isset($request->from_date) && !empty($request->from_date)) {
				            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
				        }
				        if (isset($request->end_date) && !empty($request->end_date)) {
				            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
				        }
						$customerlist = $query->where('type', $request->list_type)->orderBy('id', 'desc')->offset($page_no*50)->take(50)->get();
						if (!empty($customerlist) && count($customerlist) > 0) {
							$this->response = array(
								"status" => 200,
								"message" => ResponseMessages::getStatusCodeMessages(125),
								"data" => !empty($customerlist) ? $customerlist : null,
							);
						}else{
							$this->response = array(
								"status" => 300,
								"message" => ResponseMessages::getStatusCodeMessages(520),
								"data" => null,
							
							);
						}
						
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(5),
							"data" => null,
						);
					}
				}else{
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(214),
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


	 // function called to display notification List sections
	public function notificationList(Request $request)
	{
		$this->checkKeys(array_keys($request->all()), array("page", "device_id", "device_token", "device_type"));
		try {
			$input = $request->all();
			$validate = Validator($request->all(), [
			 	'page' => 'required|numeric',
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
				if (($request->device_type == 'android') || ($request->device_type == 'ios') || ($request->device_type == 'web')) {
					if ((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
						if (isset($request->page) && !empty($request->page)) {
							$page_no = $request->page - 1;
						}else{
							$page_no = 0;
						}
						$notificationData = Notificationuser::select('id', 'receiver_id', 'sender_id', 'title', 'description', 'notification_type', 'type', 'is_read', DB::raw("DATE_FORMAT(created_at,'%b %d, %Y') as created_date"))->with(['sender' => function ($q) {
											$q->select('id', 'name', 'phone_code', 'mobile', 'avatar as image');
										}])
									 ->where('receiver_id', $user->id)
									 ->where('status', '!=', 'delete')
									 ->orderBy('id', 'desc')
									 ->offset($page_no*50)->take(50)
									 ->get();
						if(!empty($notificationData) && count($notificationData) > 0){
							$this->response = array(
								"status" => 200,
								"message" => ResponseMessages::getStatusCodeMessages(125),
								"data" => !empty($notificationData) ? $notificationData : null,
							);

						}else{
							$this->response = array(
								"status" => 300,
								"message" => ResponseMessages::getStatusCodeMessages(520),
								"data" => null,
							);
						}

					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(519),
							"data" => null,
						);
					}
				}else{
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(214),
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

	public function readNotification(Request $request)
	{
		$this->checkKeys(array_keys($request->all()), array("notification_id", "device_id", "device_token", "device_type"));
	
		try {
			if (($request->device_type == 'android') || ($request->device_type == 'ios') || ($request->device_type == 'web')) {
				if ((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
					if (isset($request->notification_id) && !empty($request->notification_id)) {
						Notificationuser::where('id', $request->notification_id)->where('receiver_id', $user->id)->update(['is_read' => 1]);
					}else{
						Notificationuser::where('receiver_id', $user->id)->update(['is_read' => 1]);
					}
					$page_no = 0;
					$notificationData = Notificationuser::select('id', 'receiver_id', 'sender_id', 'title', 'description', 'notification_type', 'type', 'is_read', DB::raw("DATE_FORMAT(created_at,'%b %d, %Y') as created_date"))->with(['sender' => function ($q) {
							$q->select('id', 'name', 'phone_code', 'mobile', 'avatar as image');
						}])
					 ->where('receiver_id', $user->id)
					 ->where('status', '!=', 'delete')
					 ->orderBy('id', 'desc')
					 ->offset($page_no*50)->take(50)
					 ->get();
					$this->response = array(
						"status" => 200,
						"message" => ResponseMessages::getStatusCodeMessages(568),
						"data" => !empty($notificationData) ? $notificationData : null,
					);
					
				} else {
					$this->response = array(
						"status" => 403,
						"message" => ResponseMessages::getStatusCodeMessages(519),
						"data" => null,
					);
				}
			} else {
				$this->response = array(
					"status" => 300,
					"message" => ResponseMessages::getStatusCodeMessages(214),
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

	public function deleteNotification(Request $request)
    {
		$this->checkKeys(array_keys($request->all()), array("notification_id", "device_id", "device_token", "device_type"));
	
		try {
			if (($request->device_type == 'android') || ($request->device_type == 'ios') || ($request->device_type == 'web')) {
				if ((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
					if (isset($request->notification_id) && !empty($request->notification_id)) {
						Notificationuser::where('id', $request->notification_id)->where('receiver_id', $user->id)->update(['status' => 'delete']);
					}else{
						Notificationuser::where('receiver_id', $user->id)->update(['status' => 'delete']);
					}
					$page_no = 0;
					$notificationData = Notificationuser::select('id', 'receiver_id', 'sender_id', 'title', 'description', 'notification_type', 'type', 'is_read', DB::raw("DATE_FORMAT(created_at,'%b %d, %Y') as created_date"))->with(['sender' => function ($q) {
							$q->select('id', 'name', 'phone_code', 'mobile', 'avatar as image');
						}])
					 ->where('receiver_id', $user->id)
					 ->where('status', '!=', 'delete')
					 ->orderBy('id', 'desc')
					 ->offset($page_no*50)->take(50)
					 ->get();
					$this->response = array(
						"status" => 200,
						"message" => ResponseMessages::getStatusCodeMessages(569),
						"data" => !empty($notificationData) ? $notificationData : null,
					);
				} else {
					$this->response = array(
						"status" => 403,
						"message" => ResponseMessages::getStatusCodeMessages(519),
						"data" => null,
					);
				}
			} else {
				$this->response = array(
					"status" => 300,
					"message" => ResponseMessages::getStatusCodeMessages(214),
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

	//function call to update Profile
	public function updateProfile(Request $request) {
		$this->checkKeys(array_keys($request->all()), array('store_name', 'GST_number', 'store_category', 'address', 'lat', 'lng', 'name', 'email', 'phone_code', 'mobile', "device_id", "device_token", "device_type"));
		try {
			$input = $request->all();
				$validate = Validator($request->all(), [
					// 'device_id'=>'required',
					// 'device_token'=>'required',
					// 'device_type'=>'required',
	            'name' => 'required',
				'store_name' => 'required',
	            'email' => 'required|email',
				//'password' => 'required',
				'phone_code' => 'required',
	            'mobile' => 'required|min:8|numeric',
				'store_category' =>'required|numeric',
				'GST_number' => 'required'
	        ]);
	        $attr = [
	        	// 'device_id'=>"Device Id",
	        	// 'device_token'=>"Device Token",
	        	// 'device_type'=>"Device Type",
	            'name' => 'Contact Person Name',
				'store_name' => 'Store Name',
	            'email' => 'Email',
				//'password' => 'Password',
				'phone_code' =>'Country Code',
	            'mobile' => 'Phone No',
				'store_category' => 'Store Type',
				'GST_number' => 'GST Number',
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
					if ((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
						$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
						
						$user->name = $input['name'];
						$user->email = $input['email'];
						//$user->password =Hash::make($input['password']);
						$user->phone_code = $input['phone_code'];
						$user->mobile = $input['mobile'];
						$user->store_name = $input['store_name'];
						$user->store_category = $input['store_category'];
						$user->GST_number = $input['GST_number'];
						$user->address = $input['address'];
						$user->latitude = $input['lat'];
						$user->longitude = $input['lng'];
						if ($request->hasfile('store_logo')) {
							$file = $request->file('store_logo');
							$filename = time() . $file->getClientOriginalName();
							$filename = str_replace(' ', '', $filename);
							$filename = str_replace('.jpeg', '.jpg', $filename);
							$file->move(public_path('img/storelogos'), $filename);
							if ($user->store_logo != null && file_exists(public_path('img/storelogos/' . $user->store_logo))) {
								if ($user->store_logo != 'no_avatar.jpg') {
									unlink(public_path('img/storelogos/' . $user->store_logo));
								}
							}
							$user->store_logo = $filename;
						}
						
                        if ($request->hasfile('image')) {
                            $file = $request->file('image');
                            $filename = time() . $file->getClientOriginalName();
                            $filename = str_replace(' ', '', $filename);
                            $filename = str_replace('.jpeg', '.jpg', $filename);
                            $file->move(public_path('img/avatars'), $filename);
                            if ($user->avatar != null && file_exists(public_path('img/avatars/' . $user->avatar))) {
                                if ($user->avatar != 'no_avatar.jpg') {
                                    unlink(public_path('img/avatars/' . $user->avatar));
                                }
                            }
                            $user->avatar = $filename;
                        }
						$user->save();
						$userData = $this->userProfileDetails($user);
						unset($userData->devices);
						$this->response = array(
							"status" => 200,
							"message" => ResponseMessages::getStatusCodeMessages(122),
							"data" => !empty($userData) ? $userData : null,
						);
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(5),
							"data" => null,
						);
					}
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

	//change password
	public function changePassword(Request $request) {
		$this->checkKeys(array_keys($request->all()), array("old_password","new_password","confirm_password", "device_id", "device_token", "device_type"));
		try {
			if (($request->device_type == 'android') || ($request->device_type == 'ios') || ($request->device_type == 'web'))
			{	
				$input = $request->all();
	    		$userid = Auth::user();

				$rules = array(
					'old_password' => 'required',
			        'new_password' => 'required|min:6',
			        'confirm_password' => 'required|same:new_password',
				);
				$validate = Validator($request->all(), $rules);
				$attr = [
					'old_password' => 'Old Password',
					'new_password' => 'New Password',
					'confirm_password' => 'Confirm Password',
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
					if (!Hash::check(request('old_password'), Auth::user()->password)) {
						$this->response = array(
							"status" => 300,
		                    "message" => "Check your old password.",
		                    "data" => null
						);
						$this->shut_down();
						exit;
		            } elseif (Hash::check(request('new_password'), Auth::user()->password)) {
		            	$this->response = array(
							"status" => 300,
		                    "message" => "Please enter a password that is not similar to the current password.",
		                    "data" => null
						);
						$this->shut_down();
						exit;
		            } else {
		                Auth::user()->update(['password' => Hash::make($input['new_password']), 'temp_password' => $input['new_password']]);
		                $this->response = array(
							"status" => 200,
		                    "message" => "Password updated successfully.",
		                    "data" => null
						);
						$this->shut_down();
						exit;
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
	//deleteaccount
    public function deleteAccount(Request $request)
    {
		$this->checkKeys(array_keys($request->all()), array("device_id", "device_token", "device_type", "reason"));
		try {
			$input = $request->all();
				$validate = Validator($request->all(), [
					// 'device_id'=>'required',
					// 'device_token'=>'required',
					// 'device_type'=>'required',
	            'reason' => 'required'
	        ]);
	        $attr = [
	        	// 'device_id'=>"Device Id",
	        	// 'device_token'=>"Device Token",
	        	// 'device_type'=>"Device Type",
	            'reason' => 'Reason'
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
					if ((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
						$user->deletion_reason  = $request->reason;
						$user->status ='delete';
						$user->save();
						//$this->logoutUserDevice($user->id, $request->device_id, $request->device_token);

						$this->response = array(
							"status" => 200,
							"message" => ResponseMessages::getStatusCodeMessages(555),
							"data" => null,
						);
					} else {
						$this->response = array(
							"status" => 200,
							"message" => ResponseMessages::getStatusCodeMessages(555),
							"data" => null,
						);
					}
				}else{
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(564),
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

	// function call to user information
	public function profileDetail(Request $request) {
		$this->checkKeys(array_keys($request->all()), array("device_id", "device_token", "device_type"));
		try {
			if (($request->device_type == 'android') || ($request->device_type == 'ios') || ($request->device_type == 'web')) {
				if ((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
					$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
					$userData = $this->userProfileDetails($user);
					if ($userData) {
						$this->response = array(
							"status" => 200,
							"message" => ResponseMessages::getStatusCodeMessages(125),
							"data" => !empty($userData) ? $userData : null,
						);
					}else{
						$this->response = array(
							"status" => 300,
							"message" => ResponseMessages::getStatusCodeMessages(520),
							"data" => null,
						);
					}
				} else {
					$this->response = array(
						"status" => 403,
						"message" => ResponseMessages::getStatusCodeMessages(5),
						"data" => null,
					);
				}
			}else{
				$this->response = array(
					"status" => 300,
					"message" => ResponseMessages::getStatusCodeMessages(214),
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
	// function call to post offer request
	public function postOfferRequest(Request $request) {
		$this->checkKeys(array_keys($request->all()), array("description", "device_id", "device_token", "device_type"));
		// dd($request->all());
		$access_token = '';
		try {
			$input = $request->all();
  			$validate = Validator($request->all(), [
	            'description' => 'required',
				
	        ]);
	        $attr = [
	            'description' => 'Offer Description',   
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
					if (Auth::user()->id) {
						$user = User::where('id', Auth::user()->id)->first();
					
						$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
						
						$postoffers = new PostOfferRequest;
						$postoffers->description = $input['description'];
						$postoffers->vendor_id = $user->id;

						if ($request->hasfile('image')) {
							$file = $request->file('image');
							$filename = time() . $file->getClientOriginalName();
							$filename = str_replace(' ', '', $filename);
							$filename = str_replace('.jpeg', '.jpg', $filename);
							$file->move(public_path('uploads/postoffers'), $filename);
							if ($postoffers->image != null && file_exists(public_path('uploads/postoffers/' . $postoffers->image))) {
								if ($postoffers->image != 'no_avatar.jpg') {
									unlink(public_path('uploads/postoffers/' . $postoffers->image));
								}
							}
							$postoffers->image = $filename;
						}
						$postoffers->save();
	
						$this->response = array(
							"status" => 200,
							"message" => ResponseMessages::getStatusCodeMessages(8),
							"data" => null,
						);
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(5),
							"data" => null,
						);
					}
				}else{
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(214),
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

	//function call to addSalePerson
	public function addSalePerson(Request $request) {
		$this->checkKeys(array_keys($request->all()), array("sale_person", "device_id", "device_token", "device_type"));
		$access_token = '';
		try {
			$input = $request->all();
  			$validate = Validator($request->all(), [
	            'sale_person' => 'required'
	        ]);
	        $attr = [
	            'sale_person' => 'Sale Person'
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
					if ($user = User::where('id', Auth::user()->id)->first()) {
						$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
						if ($request->sale_person != '') {
							$sale_persons = $request->sale_person;
							$arrayDatas = json_decode($sale_persons, true);
							if (!empty($arrayDatas)) {
								foreach($arrayDatas as $k => $val){
									if (isset($val['name'])) {
										$VendorSalePerson = new VendorSalePerson;
						                $VendorSalePerson->vendor_id = Auth::user()->id;
						                $VendorSalePerson->name = ucfirst($val['name']);
						                $VendorSalePerson->phone_code = isset($val['phone_code']) ? $val['phone_code'] : '';
						                $VendorSalePerson->mobile_number = isset($val['mobile_number']) ? $val['mobile_number'] : '';
						                $VendorSalePerson->status = 'active';
						           
						                $VendorSalePerson->created_at = date('Y-m-d H:i:s');
						                $VendorSalePerson->updated_at = date('Y-m-d H:i:s');
						                $VendorSalePerson->save();
									}
								}
							}
						}
						$page_no = 0;
						$userData = $this->vendorSalePersonList($user, $page_no, $request);
						$this->response = array(
							"status" => 200,
							"message" => ResponseMessages::getStatusCodeMessages(538),
							"data" => !empty($userData) ? $userData : null,
						);
						
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(5),
							"data" => null,
						);
					}
				}
				else{
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(214),
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

	public function vendorSalePersonList($user, $page_no, $request) {
		
		$query = VendorSalePerson::select('id', 'name', 'phone_code', 'mobile_number', DB::raw("DATE_FORMAT(created_at,'%b %d, %Y') as created_date"));
		if (isset($request->search) && !empty($request->search)) {
			$query->where(function ($q1) use ($request) {
                $q1->where('name', 'like', '%' . $request->search . '%');
                $q1->orWhere('phone_code', 'like', '%' . $request->search . '%');
                $q1->orWhere('mobile_number', 'like', '%' . $request->search . '%');
            });
		}	
		$users = $query->where('vendor_id', $user->id)->where('status', '!=', 'delete')->orderBy('id', 'desc')->offset($page_no*50)->take(50)->get();
		
		return $users;
	}

	//Use for sale Person List
	public function salePersonList(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("search", "page", "device_id", "device_token", "device_type"));
		try {
			$input = $request->all();
			$validate = Validator($request->all(), [
			 	'page' => 'required|numeric'
			]);
			$attr = [
				'page' => 'Page No'
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
					if ((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
						$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
						if (isset($request->page) && !empty($request->page)) {
							$page_no = $request->page - 1;
						}else{
							$page_no = 0;
						}
						$userData = $this->vendorSalePersonList($user, $page_no, $request);
						if (!empty($userData) && count($userData) > 0) {
							$this->response = array(
								"status" => 200,
								"message" => ResponseMessages::getStatusCodeMessages(125),
								"data" => !empty($userData) ? $userData : null,
							);
						}else{
							$this->response = array(
								"status" => 300,
								"message" => ResponseMessages::getStatusCodeMessages(520),
								"data" => null,
							);
						}
						
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(5),
							"data" => null,
						);
					}
				}else{
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(214),
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

	//function call to updateSalePerson
	public function updateSalePerson(Request $request) {
		$this->checkKeys(array_keys($request->all()), array('name', 'mobile_number', 'phone_code', 'sale_person_id', "device_id", "device_token", "device_type"));
		try {
			$input = $request->all();
				$validate = Validator($request->all(), [
					// 'device_id'=>'required',
					// 'device_token'=>'required',
					// 'device_type'=>'required',
	            'name' => 'required',
				'sale_person_id' => 'required',
	        ]);
	        $attr = [
	        	// 'device_id'=>"Device Id",
	        	// 'device_token'=>"Device Token",
	        	// 'device_type'=>"Device Type",
	            'name' => 'Name',
				'sale_person_id' => 'Update Id',
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
					if ((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
						$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
						if ($VendorSalePerson = VendorSalePerson::where('id', $request->sale_person_id)->where('vendor_id', Auth::user()->id)->first()) {
							// code...
						}else{
							$VendorSalePerson = new VendorSalePerson;
							$VendorSalePerson->status = 'active';
							$VendorSalePerson->created_at = date('Y-m-d H:i:s');
						}
		                $VendorSalePerson->vendor_id = Auth::user()->id;
		                $VendorSalePerson->name = ucfirst($request->name);
		                $VendorSalePerson->phone_code = isset($request->phone_code) ? $request->phone_code : '';
		                $VendorSalePerson->mobile_number = isset($request->mobile_number) ? $request->mobile_number : '';
		                
		                
		                $VendorSalePerson->updated_at = date('Y-m-d H:i:s');
		                $VendorSalePerson->save();
						$page_no = 0;
						$userData = $this->vendorSalePersonList($user, $page_no, $request);
						$this->response = array(
							"status" => 200,
							"message" => ResponseMessages::getStatusCodeMessages(122),
							"data" => !empty($userData) ? $userData : null,
						);
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(5),
							"data" => null,
						);
					}
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



	//function call to addAttribute
	public function addAttribute(Request $request) {
		$this->checkKeys(array_keys($request->all()), array("attribute", "device_id", "device_token", "device_type"));
		$access_token = '';
		try {
			$input = $request->all();
  			$validate = Validator($request->all(), [
	            'attribute' => 'required'
	        ]);
	        $attr = [
	            'attribute' => 'Attribute'
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
					if ($user = User::where('id', Auth::user()->id)->first()) {
						$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
						if ($request->attribute != '') {
							$attributes = $request->attribute;
							$arrayDatas = json_decode($attributes, true);
							if (!empty($arrayDatas)) {
								foreach($arrayDatas as $k => $val){
									if (isset($val['name'])) {
										$VendorAttribute = new VendorAttribute;
						                $VendorAttribute->vendor_id = Auth::user()->id;
						                $VendorAttribute->name = ucfirst($val['name']);
						                $VendorAttribute->status = 'active';
						           
						                $VendorAttribute->created_at = date('Y-m-d H:i:s');
						                $VendorAttribute->updated_at = date('Y-m-d H:i:s');
						                $VendorAttribute->save();
									}
								}
							}
						}
						$page_no = 0;
						$userData = $this->vendorAttributeList($user, $page_no, $request);
						$this->response = array(
							"status" => 200,
							"message" => ResponseMessages::getStatusCodeMessages(538),
							"data" => !empty($userData) ? $userData : null,
						);
						
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(5),
							"data" => null,
						);
					}
				}
				else{
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(214),
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

	public function vendorAttributeList($user, $page_no, $request) {
		
		$query = VendorAttribute::select('id', 'name', DB::raw("DATE_FORMAT(created_at,'%b %d, %Y') as created_date"));
		if (isset($request->search) && !empty($request->search)) {
			$query->where(function ($q1) use ($request) {
                $q1->where('name', 'like', '%' . $request->search . '%');
            });
		}	
		$users = $query->where('vendor_id', $user->id)->where('status', '!=', 'delete')->orderBy('id', 'desc')->offset($page_no*50)->take(50)->get();
		
		return $users;
	}

	//Use for sale Person List
	public function attributeList(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("search", "page", "device_id", "device_token", "device_type"));
		try {
			$input = $request->all();
			$validate = Validator($request->all(), [
			 	'page' => 'required|numeric'
			]);
			$attr = [
				'page' => 'Page No'
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
					if ((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
						$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
						if (isset($request->page) && !empty($request->page)) {
							$page_no = $request->page - 1;
						}else{
							$page_no = 0;
						}
						$userData = $this->vendorAttributeList($user, $page_no, $request);
						if (!empty($userData) && count($userData) > 0) {
							$this->response = array(
								"status" => 200,
								"message" => ResponseMessages::getStatusCodeMessages(125),
								"data" => !empty($userData) ? $userData : null,
							);
						}else{
							$this->response = array(
								"status" => 300,
								"message" => ResponseMessages::getStatusCodeMessages(520),
								"data" => null,
							);
						}
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(5),
							"data" => null,
						);
					}
				}else{
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(214),
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

	//function call to updateAttribute
	public function updateAttribute(Request $request) {
		$this->checkKeys(array_keys($request->all()), array('name', 'attribute_id', "device_id", "device_token", "device_type"));
		try {
			$input = $request->all();
				$validate = Validator($request->all(), [
					// 'device_id'=>'required',
					// 'device_token'=>'required',
					// 'device_type'=>'required',
	            'name' => 'required',
				'attribute_id' => 'required',
	        ]);
	        $attr = [
	        	// 'device_id'=>"Device Id",
	        	// 'device_token'=>"Device Token",
	        	// 'device_type'=>"Device Type",
	            'name' => 'Name',
				'attribute_id' => 'Update Id',
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
					if ((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
						$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
						if ($VendorAttribute = VendorAttribute::where('id', $request->attribute_id)->where('vendor_id', Auth::user()->id)->first()) {
							// code...
						}else{
							$VendorAttribute = new VendorAttribute;
							$VendorAttribute->status = 'active';
							$VendorAttribute->created_at = date('Y-m-d H:i:s');
						}
		                $VendorAttribute->vendor_id = Auth::user()->id;
		                $VendorAttribute->name = ucfirst($request->name);
		                
		                
		                $VendorAttribute->updated_at = date('Y-m-d H:i:s');
		                $VendorAttribute->save();
						$page_no = 0;
						$userData = $this->vendorAttributeList($user, $page_no, $request);
						$this->response = array(
							"status" => 200,
							"message" => ResponseMessages::getStatusCodeMessages(122),
							"data" => !empty($userData) ? $userData : null,
						);
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(5),
							"data" => null,
						);
					}
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



	//function call to addProduct
	public function addProduct(Request $request) {
		$this->checkKeys(array_keys($request->all()), array("product", "device_id", "device_token", "device_type"));
		$access_token = '';
		try {
			$input = $request->all();
  			$validate = Validator($request->all(), [
	            'product' => 'required'
	        ]);
	        $attr = [
	            'product' => 'Product'
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
					if ($user = User::where('id', Auth::user()->id)->first()) {
						$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
						if ($request->product != '') {
							$products = $request->product;
							$arrayDatas = json_decode($products, true);
							if (!empty($arrayDatas)) {
								foreach($arrayDatas as $k => $val){
									if (isset($val['name']) && isset($val['gst']) && !empty($val['name']) && !empty($val['gst'])) {
										$VendorProduct = new VendorProduct;
						                $VendorProduct->vendor_id = Auth::user()->id;
						                $VendorProduct->name = ucfirst($val['name']);
						                $VendorProduct->gst = ucfirst($val['gst']);
						                $VendorProduct->status = 'active';
						           
						                $VendorProduct->created_at = date('Y-m-d H:i:s');
						                $VendorProduct->updated_at = date('Y-m-d H:i:s');
						                $VendorProduct->save();
									}
								}
							}
						}
						$page_no = 0;
						$userData = $this->vendorProductList($user, $page_no, $request);
						$this->response = array(
							"status" => 200,
							"message" => ResponseMessages::getStatusCodeMessages(538),
							"data" => !empty($userData) ? $userData : null,
						);
						
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(5),
							"data" => null,
						);
					}
				}
				else{
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(214),
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

	public function vendorProductList($user, $page_no, $request) {
		
		$query = VendorProduct::select('id', 'name', 'gst', DB::raw("DATE_FORMAT(created_at,'%b %d, %Y') as created_date"));
		if (isset($request->search) && !empty($request->search)) {
			$query->where(function ($q1) use ($request) {
                $q1->where('name', 'like', '%' . $request->search . '%');
            });
		}	
		$users = $query->where('vendor_id', $user->id)->where('status', '!=', 'delete')->orderBy('id', 'desc')->offset($page_no*50)->take(50)->get();
		
		return $users;
	}

	//Use for sale Person List
	public function productList(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("search", "page", "device_id", "device_token", "device_type"));
		try {
			$input = $request->all();
			$validate = Validator($request->all(), [
			 	'page' => 'required|numeric'
			]);
			$attr = [
				'page' => 'Page No'
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
					if ((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
						$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
						if (isset($request->page) && !empty($request->page)) {
							$page_no = $request->page - 1;
						}else{
							$page_no = 0;
						}
						$userData = $this->vendorProductList($user, $page_no, $request);
						if (!empty($userData) && count($userData) > 0) {
							$this->response = array(
								"status" => 200,
								"message" => ResponseMessages::getStatusCodeMessages(125),
								"data" => !empty($userData) ? $userData : null,
							);
						}else{
							$this->response = array(
								"status" => 300,
								"message" => ResponseMessages::getStatusCodeMessages(520),
								"data" => null,
							);
						}
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(5),
							"data" => null,
						);
					}
				}else{
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(214),
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

	//function call to updateProduct
	public function updateProduct(Request $request) {
		$this->checkKeys(array_keys($request->all()), array('name', 'gst', 'product_id', "device_id", "device_token", "device_type"));
		try {
			$input = $request->all();
				$validate = Validator($request->all(), [
					// 'device_id'=>'required',
					// 'device_token'=>'required',
					// 'device_type'=>'required',
	            'name' => 'required',
	            'gst' => 'required',
				'product_id' => 'required',
	        ]);
	        $attr = [
	        	// 'device_id'=>"Device Id",
	        	// 'device_token'=>"Device Token",
	        	// 'device_type'=>"Device Type",
	            'name' => 'Name',
	            'gst' => 'GST',
				'product_id' => 'Update Id',
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
					if ((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
						$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
						if ($VendorProduct = VendorProduct::where('id', $request->product_id)->where('vendor_id', Auth::user()->id)->first()) {
							// code...
						}else{
							$VendorProduct = new VendorProduct;
							$VendorProduct->status = 'active';
							$VendorProduct->created_at = date('Y-m-d H:i:s');
						}
		                $VendorProduct->vendor_id = Auth::user()->id;
		                $VendorProduct->name = ucfirst($request->name);
		                $VendorProduct->gst = $request->gst;
		                
		                
		                $VendorProduct->updated_at = date('Y-m-d H:i:s');
		                $VendorProduct->save();
						$page_no = 0;
						$userData = $this->vendorProductList($user, $page_no, $request);
						$this->response = array(
							"status" => 200,
							"message" => ResponseMessages::getStatusCodeMessages(122),
							"data" => !empty($userData) ? $userData : null,
						);
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(5),
							"data" => null,
						);
					}
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


	//function call to createInvoice
	public function createInvoice(Request $request) {
		$this->checkKeys(array_keys($request->all()), array("customer_name", "customer_phone_code", "customer_mobile_number", "sale_person_id", "product", "device_id", "device_token", "device_type"));
		$access_token = '';
		try {
			$input = $request->all();
  			$validate = Validator($request->all(), [
	            'product' => 'required',
	            'customer_name' => 'required',
	            'customer_phone_code' => 'required',
	            'customer_mobile_number' => 'required',
	            'sale_person_id' => 'required',
	        ]);
	        $attr = [
	            'product' => 'Product',
	            'customer_name' => 'Customer Name',
	            'customer_phone_code' => 'Customer Phone Code',
	            'customer_mobile_number' => 'Customer Mobile Number',
	            'sale_person_id' => 'Sale Person Id',
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
				$total_amount = 0;
				$total_gst = 0;
				if (($request->device_type == 'android') || ($request->device_type == 'ios') || ($request->device_type == 'web')) {
					if ($user = User::where('id', Auth::user()->id)->first()) {
						$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
						if ($customers = User::where('status', '!=', 'delete')->where('mobile', $input['customer_mobile_number'])->first()) {
							// code...
						}else{
							$password = $this->generateReferralCode(1);
							$customers = new User;
							$customers->password = Hash::make($password);
							$customers->role = 'Customer';
						}
						$customers->name = $input['customer_name'];
						$customers->phone_code = $input['customer_phone_code'];
						$customers->mobile = $input['customer_mobile_number'];
						if ($customers->save()) {
							$orderType = 'Invoice';

							$invoice_no =$this->generateReferralCode(1);
						
							$vendor_customer = new VendorCustomer;
							$vendor_customer->vendor_id = $user->id;
							$vendor_customer->customer_id = $customers->id;
							$vendor_customer->type = $orderType;
							$vendor_customer->category_id = 0;
							$vendor_customer->sale_person_id = $request->sale_person_id;
							$vendor_customer->invoice_no = $invoice_no;
							$vendor_customer->created_at = date('Y-m-d H:i:s');
							$vendor_customer->updated_at = date('Y-m-d H:i:s');
							if ($vendor_customer->save()) {
								
								if ($request->product != '') {
									$products = $request->product;
									$arrayDatas = json_decode($products, true);
									if (!empty($arrayDatas)) {
										foreach($arrayDatas as $k => $val){
											if (isset($val['product_id'])) {
												$PurchaseItem = new PurchaseItem;
								                $PurchaseItem->vendor_customer_id = $vendor_customer->id;
								                $PurchaseItem->product_id = $val['product_id'];
								                $PurchaseItem->price = $val['price'];
								                $PurchaseItem->qty = 1;
								                $PurchaseItem->gst = $val['gst'];
								                $PurchaseItem->status = 'active';
								                $PurchaseItem->created_at = date('Y-m-d H:i:s');
								                $PurchaseItem->updated_at = date('Y-m-d H:i:s');
								                $total_amount = $total_amount + $val['price'];
									            $total_gst = $total_gst + $val['gst'];
								                if ($PurchaseItem->save()) {
								                	$attributes = explode(",", $val['attribute_id']);
								                	if (!empty($attributes)) {
								                		foreach($attributes as $key => $value){
															if (isset($val['product_id'])) {
																$PurchaseItemAttribute = new PurchaseItemAttribute;
												                $PurchaseItemAttribute->purchase_item_id = $PurchaseItem->id;
												                $PurchaseItemAttribute->vendor_customer_id = $vendor_customer->id;
												                $PurchaseItemAttribute->attribute_id = $value;
												                $PurchaseItemAttribute->status = 'active';
												                $PurchaseItemAttribute->created_at = date('Y-m-d H:i:s');
												                $PurchaseItemAttribute->updated_at = date('Y-m-d H:i:s');
												                $PurchaseItemAttribute->save();
															}
														}
								                	}
								                }
											}
										}
										$originalAmount = $total_amount;
										$gstPercent = $total_gst; 
										$gstAmount = ($originalAmount * $gstPercent) / 100;
										$total_amount = $originalAmount + $gstAmount;
									}
								}
								$vendor_customer->total_gst = $total_gst;
								$vendor_customer->total_amount = $total_amount;
								$vendor_customer->save();
								$this->pdf($vendor_customer->id);
								$page_no = 0;
								$page_no = 0;
								$userData = $this->vendorInvoiceList($user, $page_no, $request);
								$this->response = array(
									"status" => 200,
									"message" => ResponseMessages::getStatusCodeMessages(538),
									"data" => !empty($userData) ? $userData : null,
								);
							}else{
								$this->response = array(
									"status" => 300,
									"message" => 'Customer not create please try again',
									"data" => null,
								);
							}
						}else{
							$this->response = array(
								"status" => 300,
								"message" => 'Customer not create please try again',
								"data" => null,
							);
						}
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(5),
							"data" => null,
						);
					}
				}
				else{
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(214),
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


    public function pdf($id){
        $query = VendorCustomer::select('id', 'vendor_id', 'customer_id', 'sale_person_id', 'invoice_no', 'total_amount', 'total_gst', 'pdf', DB::raw("DATE_FORMAT(created_at,'%b %d, %Y') as created_date"))->with([
			'customer' => function ($q) {
				$q->select('id', 'name', 'phone_code', 'mobile', 'email');
			},
			'vendor' => function ($q) {
				$q->select('id', 'name', 'phone_code', 'mobile', 'email', 'store_name', 'store_logo', 'GST_number', DB::raw("DATE_FORMAT(created_at,'%b %d, %Y') as created_date"));
			},
			'sale_person' => function ($q) {
				$q->select('id', 'name', 'phone_code', 'mobile_number');
			},
			'purchase_product' => function ($q) {
				$q->select('id', 'vendor_customer_id', 'product_id', 'price', 'gst', 'qty')->with([
					'product' => function ($q) {
						$q->select('id', 'name', 'gst');
					},
					'purchase_product_attribute' => function ($q) {
						$q->select('id', 'purchase_item_id', 'attribute_id')->with([
							'attribute' => function ($q) {
								$q->select('id', 'name');
							}
						]);
					}
				]);
			}
		]);
		$results = $query->where('type', 'Invoice')->where('id', $id)->first();
        $dompdf = new Dompdf();
        $htmlview = view('frontend.invoice.pdf',compact('results'));
        $dompdf->loadHtml($htmlview);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->set_option('isRemoteEnabled', true);
        $dompdf->render();
        $file_name = 'Laravel_invoice'.$id.'.pdf';
        VendorCustomer::where(['id'=>$id])->update(['pdf'=>$file_name]);
        $output = $dompdf->output();
        file_put_contents(public_path('uploads/invoice_pdf/').$file_name, $output);
        return url('download').'/'.$file_name;
        //return asset('uploads/invoice_pdf').'/'.$file_name;
    }

	public function vendorInvoiceList($user, $page_no, $request) {
		
		$query = VendorCustomer::select('id', 'vendor_id', 'customer_id', 'sale_person_id', 'invoice_no', 'total_amount', 'pdf', DB::raw("DATE_FORMAT(created_at,'%b %d, %Y') as created_date"))->with(['customer' => function ($q) {
				$q->select('id', 'name', 'phone_code', 'mobile', 'email');
			}])->whereHas('customer', function ($q) {
			    if (isset($request->search) && !empty($request->search)) {
		            $q->where(function ($q1) use ($request) {
		                $q1->where('name', 'like', '%' . $request->search . '%');
		                $q1->orWhere('phone_code', 'like', '%' . $request->search . '%');
	                	$q1->orWhere('mobile_number', 'like', '%' . $request->search . '%');
	                	$q1->orWhere('email', 'like', '%' . $request->search . '%');
		            });
		        }
			});
			if (isset($request->from_date) && !empty($request->from_date)) {
	            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
	        }
	        if (isset($request->end_date) && !empty($request->end_date)) {
	            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
	        }
			if (isset($request->search) && !empty($request->search)) {
				$query->where(function ($q1) use ($request) {
	                $q1->where('invoice_no', 'like', '%' . $request->search . '%');
	                
	            });
			}	
		$users = $query->where('vendor_id', $user->id)->where('type', 'Invoice')->where('status', '!=', 'delete')->orderBy('id', 'desc')->offset($page_no*50)->take(50)->get();
		
		return $users;

	}

	//Use for invoiceList
	public function invoiceList(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("search", "from_date", "end_date", "page", "device_id", "device_token", "device_type"));
		try {
			$input = $request->all();
			$validate = Validator($request->all(), [
			 	'page' => 'required|numeric'
			]);
			$attr = [
				'page' => 'Page No'
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
					if ((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
						$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
						if (isset($request->page) && !empty($request->page)) {
							$page_no = $request->page - 1;
						}else{
							$page_no = 0;
						}
						$userData = $this->vendorInvoiceList($user, $page_no, $request);
						if (!empty($userData) && count($userData) > 0) {
							$this->response = array(
								"status" => 200,
								"message" => ResponseMessages::getStatusCodeMessages(125),
								"data" => !empty($userData) ? $userData : null,
							);
						}else{
							$this->response = array(
								"status" => 300,
								"message" => ResponseMessages::getStatusCodeMessages(520),
								"data" => null,
							);
						}
						
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(5),
							"data" => null,
						);
					}
				}else{
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(214),
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

	//function call to updateInvoice
	public function updateInvoice(Request $request) {
		$this->checkKeys(array_keys($request->all()), array("id", "sale_person_id", "product", "device_id", "device_token", "device_type"));
		$access_token = '';
		try {
			$input = $request->all();
  			$validate = Validator($request->all(), [
	            'product' => 'required',
	            'id' => 'required',
	            'sale_person_id' => 'required',
	        ]);
	        $attr = [
	            'product' => 'Product',
	            'id' => 'Customer Id',
	            'sale_person_id' => 'Sale Person Id',
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
					if ($user = User::where('id', Auth::user()->id)->first()) {
						$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
							$total_amount = 0;
							$total_gst = 0;
							$orderType = 'Invoice';
							$invoice_no =$this->generateReferralCode(1);
							if ($vendor_customer = VendorCustomer::where('id', $request->id)->first()) {
								$vendor_customer->sale_person_id = $request->sale_person_id;
								$vendor_customer->updated_at = date('Y-m-d H:i:s');
								if ($vendor_customer->save()) {
									if ($request->product != '') {
										$products = $request->product;
										$arrayDatas = json_decode($products, true);
										if (!empty($arrayDatas)) {
											PurchaseItem::where('vendor_customer_id', $vendor_customer->id)->delete();
											PurchaseItemAttribute::where('vendor_customer_id', $vendor_customer->id)->delete();
											foreach($arrayDatas as $k => $val){
												if (isset($val['product_id'])) {
													$PurchaseItem = new PurchaseItem;
									                $PurchaseItem->vendor_customer_id = $vendor_customer->id;
									                $PurchaseItem->product_id = $val['product_id'];
									                $PurchaseItem->price = $val['price'];
									                $PurchaseItem->gst = $val['gst'];
									                $PurchaseItem->qty = 1;
									                $PurchaseItem->status = 'active';
									                $PurchaseItem->created_at = date('Y-m-d H:i:s');
									                $PurchaseItem->updated_at = date('Y-m-d H:i:s');
									                $total_amount = $total_amount + $val['price'];
									                $total_gst = $total_gst + $val['gst'];
									                if ($PurchaseItem->save()) {
									                	$attributes = explode(",", $val['attribute_id']);
									                	if (!empty($attributes)) {
									                		foreach($attributes as $key => $value){
																if (isset($val['product_id'])) {
																	$PurchaseItemAttribute = new PurchaseItemAttribute;
													                $PurchaseItemAttribute->purchase_item_id = $PurchaseItem->id;
													                $PurchaseItemAttribute->vendor_customer_id = $vendor_customer->id;
													                $PurchaseItemAttribute->attribute_id = $value;
													                $PurchaseItemAttribute->status = 'active';
													                $PurchaseItemAttribute->created_at = date('Y-m-d H:i:s');
													                $PurchaseItemAttribute->updated_at = date('Y-m-d H:i:s');
													                $PurchaseItemAttribute->save();
																}
															}
									                	}
									                }
												}
											}

											$originalAmount = $total_amount;
											$gstPercent = $total_gst; 
											$gstAmount = ($originalAmount * $gstPercent) / 100;
											$total_amount = $originalAmount + $gstAmount;
										}
									}
									$vendor_customer->total_gst = $total_gst;
									$vendor_customer->total_amount = $total_amount;
									$vendor_customer->save();
									$this->pdf($vendor_customer->id);
									$page_no = 0;
									$userData = $this->vendorInvoiceList($user, $page_no, $request);
									$this->response = array(
										"status" => 200,
										"message" => ResponseMessages::getStatusCodeMessages(124),
										"data" => !empty($userData) ? $userData : null,
									);
								}else{
									$this->response = array(
										"status" => 300,
										"message" => 'Customer not create please try again',
										"data" => null,
									);
								}
							}else{
								$this->response = array(
									"status" => 300,
									"message" => 'Invalid invoice id',
									"data" => null,
								);
							}
							
							
						
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(5),
							"data" => null,
						);
					}
				}else{
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(214),
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

	//Use for invoiceList
	public function invoiceDetail(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("id", "device_id", "device_token", "device_type"));
		try {
			$input = $request->all();
			$validate = Validator($request->all(), [
			 	'id' => 'required|numeric'
			]);
			$attr = [
				'id' => 'Id'
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
					if ((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
						$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
						$query = VendorCustomer::select('id', 'vendor_id', 'customer_id', 'sale_person_id', 'invoice_no', 'total_amount', 'total_gst', 'pdf', DB::raw("DATE_FORMAT(created_at,'%b %d, %Y') as created_date"))->with([
							'customer' => function ($q) {
								$q->select('id', 'name', 'phone_code', 'mobile', 'email');
							},
							'vendor' => function ($q) {
								$q->select('id', 'name', 'phone_code', 'mobile', 'email', 'store_name', 'store_logo', 'GST_number', DB::raw("DATE_FORMAT(created_at,'%b %d, %Y') as created_date"));
							},
							'sale_person' => function ($q) {
								$q->select('id', 'name', 'phone_code', 'mobile_number');
							},
							'purchase_product' => function ($q) {
								$q->select('id', 'vendor_customer_id', 'product_id', 'price', 'gst', 'qty')->with([
									'product' => function ($q) {
										$q->select('id', 'name', 'gst');
									},
									'purchase_product_attribute' => function ($q) {
										$q->select('id', 'purchase_item_id', 'attribute_id')->with([
											'attribute' => function ($q) {
												$q->select('id', 'name');
											}
										]);
									}
								]);
							}
						]);
						$results = $query->where('vendor_id', $user->id)->where('type', 'Invoice')->where('id', $request->id)->first();
						if (isset($results->id)) {
							$this->response = array(
	 							"status" => 200,
	 							"message" => ResponseMessages::getStatusCodeMessages(124),
								"data" => $results,
							);
						}else{
							$this->response = array(
								"status" => 300,
								"message" => ResponseMessages::getStatusCodeMessages(520),
								"data" => null,
							);
						}
						
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(5),
							"data" => null,
						);
					}
				}else{
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(214),
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

	public function sendInvoice(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("id", "device_id", "device_token", "device_type"));
		try {
			$input = $request->all();
			$validate = Validator($request->all(), [
			 	'id' => 'required|numeric'
			]);
			$attr = [
				'id' => 'Id'
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
					if ((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
						$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
						$query = VendorCustomer::select('id', 'vendor_id', 'customer_id', 'sale_person_id', 'invoice_no', 'total_amount', 'total_gst', 'pdf', DB::raw("DATE_FORMAT(created_at,'%b %d, %Y') as created_date"))->with([
							'customer' => function ($q) {
								$q->select('id', 'name', 'phone_code', 'mobile', 'email');
							},
							'vendor' => function ($q) {
								$q->select('id', 'name', 'phone_code', 'mobile', 'email', 'store_name', 'store_logo', 'GST_number', DB::raw("DATE_FORMAT(created_at,'%b %d, %Y') as created_date"));
							},
							'sale_person' => function ($q) {
								$q->select('id', 'name', 'phone_code', 'mobile_number');
							},
							'purchase_product' => function ($q) {
								$q->select('id', 'vendor_customer_id', 'product_id', 'price', 'gst', 'qty')->with([
									'product' => function ($q) {
										$q->select('id', 'name', 'gst');
									},
									'purchase_product_attribute' => function ($q) {
										$q->select('id', 'purchase_item_id', 'attribute_id')->with([
											'attribute' => function ($q) {
												$q->select('id', 'name');
											}
										]);
									}
								]);
							}
						]);
							
						$results = $query->where('vendor_id', $user->id)->where('type', 'Invoice')->where('id', $request->id)->first();
						if (isset($results->id)) {
							$pdfurl = $this->pdf($results->id);
							$this->response = array(
	 							"status" => 200,
	 							"message" => ResponseMessages::getStatusCodeMessages(577),
								"data" => $pdfurl,
							);
						}else{
							$this->response = array(
								"status" => 300,
								"message" => ResponseMessages::getStatusCodeMessages(579),
								"data" => null,
							);
						}
						
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(5),
							"data" => null,
						);
					}
				}else{
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(214),
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

	public function vendorSubscriptionList($user, $page_no, $request) {
		
		$query = Subscription::select('id', 'name', 'price', 'days', 'type', 'description', DB::raw("DATE_FORMAT(created_at,'%b %d, %Y') as created_date"));
		if (isset($request->search) && !empty($request->search)) {
			$query->where(function ($q1) use ($request) {
                $q1->where('name', 'like', '%' . $request->search . '%');
                $q1->orWhere('price', 'like', '%' . $request->search . '%');
                $q1->orWhere('days', 'like', '%' . $request->search . '%');
                $q1->orWhere('type', 'like', '%' . $request->search . '%');
                $q1->orWhere('description', 'like', '%' . $request->search . '%');
            });
		}	
		$users = $query->where('status', '!=', 'delete')->orderBy('id', 'asc')->offset($page_no*50)->take(50)->get();
		if (!empty($users) && count($users) > 0) {
			foreach($users as $key=>$value){
				if ($value->id == $user->subscription_id) {
					$value->is_active = true;
				}else{
					$value->is_active = false;
				}
			}
		}
		return $users;
	}

	//Use for subscription List
	public function subscriptionList(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("search", "page", "device_id", "device_token", "device_type"));
		try {
			$input = $request->all();
			$validate = Validator($request->all(), [
			 	'page' => 'required|numeric'
			]);
			$attr = [
				'page' => 'Page No'
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
					if ((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
						$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
						if (isset($request->page) && !empty($request->page)) {
							$page_no = $request->page - 1;
						}else{
							$page_no = 0;
						}
						$userData = $this->vendorSubscriptionList($user, $page_no, $request);
						if (!empty($userData) && count($userData) > 0) {
							$this->response = array(
								"status" => 200,
								"message" => ResponseMessages::getStatusCodeMessages(125),
								"data" => !empty($userData) ? $userData : null,
							);
						}else{
							$this->response = array(
								"status" => 300,
								"message" => ResponseMessages::getStatusCodeMessages(520),
								"data" => null,
							);
						}
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(5),
							"data" => null,
						);
					}
				}else{
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(214),
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


	//function call to subscription Upgrade
	public function subscriptionUpgrade(Request $request) {
		$this->checkKeys(array_keys($request->all()), array('subscription_id', "txn_id", "payment_method", "device_id", "device_token", "device_type"));
		try {
			$input = $request->all();
				$validate = Validator($request->all(), [
					// 'device_id'=>'required',
					// 'device_token'=>'required',
					// 'device_type'=>'required',
	            'subscription_id' => 'required',
	            'txn_id' => 'required',
	            'payment_method' => 'required',
	        ]);
	        $attr = [
	        	// 'device_id'=>"Device Id",
	        	// 'device_token'=>"Device Token",
	        	// 'device_type'=>"Device Type",
	            'subscription_id' => 'Subscription Id',
	            'txn_id' => 'TXN Id',
	            'payment_method' => 'Payment Method',
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
					if ((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
						$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
						if ($subscription = Subscription::where('id', $request->subscription_id)->first()) {
							if ($request->subscription_id == '1') {
								if ($already = UserSubscription::where('user_id', $user->id)->where('subscription_id', $request->subscription_id)->first()) {
									$this->response = array(
										"status" => 300,
										"message" => 'You have already buy trial version please upgrade another plan!',
										"data" => null,
									);
									$this->shut_down();
									exit;
								}
							}
							$day = $subscription->days;
							UserSubscription::where('user_id', $user->id)->update(['status'=>'out']);
							$newSubscription = new UserSubscription;
							$newSubscription->user_id = $user->id;
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
						    	if ($user->subscription_expire_date != '') {
						    		if (strtotime($user->subscription_expire_date) >= strtotime($currentDate)) {
						    			$currentDate = date('Y-m-d', strtotime($user->subscription_expire_date)); 
										$final_date = date('Y-m-d', strtotime($currentDate . ' + '.$day.' days'));
						    		}else{
						    			$final_date = date('Y-m-d', strtotime($currentDate . ' + '.$day.' days'));
						    		}
						    	}else{
						    		$final_date = date('Y-m-d', strtotime($currentDate . ' + '.$day.' days'));
						    	}
						    	$user->subscription_id = $subscription->id;
						    	$user->subscription_expire_date = $final_date;
						    	$user->save();


						    	//$transaction_no ='TRN'.$this->generateReferralCode(1);
								$Transaction = new Transaction;
								$Transaction->user_id = $user->id;
								$Transaction->order_id = $newSubscription->id;
								$Transaction->transaction_no = $request->txn_id;
								$Transaction->type = 'subscription';
 								//$boxtransation->transaction_no = $transaction_no;
 								$Transaction->payment_type = $request->payment_method;
 								$Transaction->amount = $subscription->price;
								$Transaction->title = 'You have successfully purchased the ' .ucfirst($subscription->name);
 								$Transaction->save();

						    	$newSubscription->start_date = $currentDate;
						    	$newSubscription->end_date = $final_date;
						    	$newSubscription->save();
								$page_no = 0;
								$userData = $this->vendorSubscriptionList($user, $page_no, $request);
								if (!empty($userData) && count($userData) > 0) {
									$this->response = array(
										"status" => 200,
										"message" => ResponseMessages::getStatusCodeMessages(535),
										"data" => !empty($userData) ? $userData : null,
									);
								}else{
									$this->response = array(
										"status" => 300,
										"message" => 'Subscription not upgrade please try again!',
										"data" => null,
									);
								}
						    }else{
						    	$this->response = array(
									"status" => 300,
									"message" => 'Subscription not upgrade please try again!',
									"data" => null,
								);
						    }
						}else{
							$this->response = array(
								"status" => 300,
								"message" => ResponseMessages::getStatusCodeMessages(531),
								"data" => null,
							);
						}
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(5),
							"data" => null,
						);
					}
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

	public function vendorAlreadyBuySubscriptionList($user, $page_no, $request) {
		
		$query = UserSubscription::select('id', 'name', 'price', 'days', 'type', 'description', 'status', DB::raw("DATE_FORMAT(start_date,'%b %d, %Y') as start_date"), DB::raw("DATE_FORMAT(end_date,'%b %d, %Y') as end_date"), DB::raw("DATE_FORMAT(created_at,'%b %d, %Y') as created_date"));
		if (isset($request->search) && !empty($request->search)) {
			$query->where(function ($q1) use ($request) {
                $q1->where('name', 'like', '%' . $request->search . '%');
                $q1->orWhere('price', 'like', '%' . $request->search . '%');
                $q1->orWhere('days', 'like', '%' . $request->search . '%');
                $q1->orWhere('type', 'like', '%' . $request->search . '%');
                $q1->orWhere('description', 'like', '%' . $request->search . '%');
            });
		}	
		$users = $query->where('status', '!=', 'delete')->orderBy('id', 'desc')->offset($page_no*50)->take(50)->get();
		return $users;
	}

	//Use for alreadyBuySubscriptionList
	public function alreadyBuySubscriptionList(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("search", "page", "device_id", "device_token", "device_type"));
		try {
			$input = $request->all();
			$validate = Validator($request->all(), [
			 	'page' => 'required|numeric'
			]);
			$attr = [
				'page' => 'Page No'
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
					if ((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
						$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
						if (isset($request->page) && !empty($request->page)) {
							$page_no = $request->page - 1;
						}else{
							$page_no = 0;
						}
						$userData = $this->vendorAlreadyBuySubscriptionList($user, $page_no, $request);
						if (!empty($userData) && count($userData) > 0) {
							$this->response = array(
								"status" => 200,
								"message" => ResponseMessages::getStatusCodeMessages(125),
								"data" => !empty($userData) ? $userData : null,
							);
						}else{
							$this->response = array(
								"status" => 300,
								"message" => ResponseMessages::getStatusCodeMessages(520),
								"data" => null,
							);
						}
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(5),
							"data" => null,
						);
					}
				}else{
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(214),
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

	public function vendorTransactionList($user, $page_no, $request) {
		
		$query = Transaction::select('id', 'order_id', 'type', 'transaction_no', 'payment_type', 'title', 'amount', DB::raw("DATE_FORMAT(created_at,'%b %d, %Y') as created_date"));
			if (isset($request->from_date) && !empty($request->from_date)) {
	            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
	        }
	        if (isset($request->end_date) && !empty($request->end_date)) {
	            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
	        }
			if (isset($request->search) && !empty($request->search)) {
				$query->where(function ($q1) use ($request) {
	                $q1->where('order_id', 'like', '%' . $request->search . '%');
		            $q1->orWhere('transaction_no', 'like', '%' . $request->search . '%');
	               	$q1->orWhere('payment_type', 'like', '%' . $request->search . '%');
	                $q1->orWhere('title', 'like', '%' . $request->search . '%');
	                $q1->orWhere('amount', 'like', '%' . $request->search . '%');
	            });
			}	
		$users = $query->where('user_id', $user->id)->where('status', '!=', 'delete')->orderBy('id', 'desc')->offset($page_no*50)->take(50)->get();
		if (!empty($users) && count($users) > 0) {
			foreach($users as $key=>$value){
				if ($value->type == 'subscription') {
					$value->order = UserSubscription::select('id', 'name', 'price', 'days', 'type', 'description', DB::raw("DATE_FORMAT(start_date,'%b %d, %Y') as start_date"), DB::raw("DATE_FORMAT(end_date,'%b %d, %Y') as end_date"), DB::raw("DATE_FORMAT(created_at,'%b %d, %Y') as created_date"))->where('status', 'active')->where('id', $value->order_id)->first();
				}else{
					$value->order = '';
				}
			}
		}
		return $users;

	}

	//Use for transactionList
	public function transactionList(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("search", "from_date", "end_date", "page", "device_id", "device_token", "device_type"));
		try {
			$input = $request->all();
			$validate = Validator($request->all(), [
			 	'page' => 'required|numeric'
			]);
			$attr = [
				'page' => 'Page No'
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
					if ((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
						$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
						if (isset($request->page) && !empty($request->page)) {
							$page_no = $request->page - 1;
						}else{
							$page_no = 0;
						}
						$userData = $this->vendorTransactionList($user, $page_no, $request);
						if (!empty($userData) && count($userData) > 0) {
							$this->response = array(
								"status" => 200,
								"message" => ResponseMessages::getStatusCodeMessages(125),
								"data" => !empty($userData) ? $userData : null,
							);
						}else{
							$this->response = array(
								"status" => 300,
								"message" => ResponseMessages::getStatusCodeMessages(520),
								"data" => null,
							);
						}
						
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(5),
							"data" => null,
						);
					}
				}else{
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(214),
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

	public function vendorMenuCategoryList($user, $page_no, $request) {
		
		$query = RestaurantCategory::select('id', 'name', 'image','icon');
			
			if (isset($request->search) && !empty($request->search)) {
				$query->where(function ($q1) use ($request) {
	                $q1->where('name', 'like', '%' . $request->search . '%');
	            });
			}	
		$results = $query->where('vendor_id', $user->id)->where('status', 'active')->orderBy('name', 'asc')->offset($page_no*50)->take(50)->get();
		return $results;

	}
	//Use for menuCategoryList
	public function menuCategoryList(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("search", "page", "device_id", "device_token", "device_type"));
		try {
			$input = $request->all();
			$validate = Validator($request->all(), [
			 	'page' => 'required|numeric'
			]);
			$attr = [
				'page' => 'Page No'
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
					if ((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
						$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
						if (($user->table_toggle == 'Yes') && ($user->store_category == '44')) {
							if (isset($request->page) && !empty($request->page)) {
								$page_no = $request->page - 1;
							}else{
								$page_no = 0;
							}
							$userData = $this->vendorMenuCategoryList($user, $page_no, $request);
							if (!empty($userData) && count($userData) > 0) {
								$this->response = array(
									"status" => 200,
									"message" => ResponseMessages::getStatusCodeMessages(125),
									"data" => !empty($userData) ? $userData : null,
								);
							}else{
								$this->response = array(
									"status" => 300,
									"message" => ResponseMessages::getStatusCodeMessages(520),
									"data" => null,
								);
							}
						}else{
							$this->response = array(
								"status" => 300,
								"message" => ResponseMessages::getStatusCodeMessages(585),
								"data" => null,
							);
						}
						
						
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(5),
							"data" => null,
						);
					}
				}else{
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(214),
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

	public function vendorMenuCategoryWiseProductList($user, $page_no, $request) {
		
		$query = RestaurantItem::select('id', 'item_name as name', 'image', 'price', 'description');
		if (isset($request->category_id) && !empty($request->category_id)) {
			$query->where('item_category_id', $request->category_id);
		}	
		if (isset($request->search) && !empty($request->search)) {
			$query->where(function ($q1) use ($request) {
                $q1->where('item_name', 'like', '%' . $request->search . '%');
                $q1->orWhere('description', 'like', '%' . $request->search . '%');
            });
		}	
		$results = $query->where('vendor_id', $user->id)->where('status', 'active')->orderBy('name', 'asc')->offset($page_no*50)->take(50)->get();
		return $results;

	}
	//Use for menuCategoryWiseProductList
	public function menuCategoryWiseProductList(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("search", "category_id", "page", "device_id", "device_token", "device_type"));
		try {
			$input = $request->all();
			$validate = Validator($request->all(), [
			 	'page' => 'required|numeric',
			 	'category_id' => 'required|numeric'
			]);
			$attr = [
				'page' => 'Page No',
				'category_id' => 'Category Id'
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
					if ((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
						$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
						if (($user->table_toggle == 'Yes') && ($user->store_category == '44')) {
							if (isset($request->page) && !empty($request->page)) {
								$page_no = $request->page - 1;
							}else{
								$page_no = 0;
							}
							$userData = $this->vendorMenuCategoryWiseProductList($user, $page_no, $request);
							if (!empty($userData) && count($userData) > 0) {
								$this->response = array(
									"status" => 200,
									"message" => ResponseMessages::getStatusCodeMessages(125),
									"data" => !empty($userData) ? $userData : null,
								);
							}else{
								$this->response = array(
									"status" => 300,
									"message" => ResponseMessages::getStatusCodeMessages(520),
									"data" => null,
								);
							}
						}else{
							$this->response = array(
								"status" => 300,
								"message" => ResponseMessages::getStatusCodeMessages(585),
								"data" => null,
							);
						}
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(5),
							"data" => null,
						);
					}
				}else{
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(214),
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

	public function vendorMenuProductDetail($request) {
		$query = RestaurantItem::select('id', 'item_category_id', 'ingredients_id', 'item_name as name', 'image', 'price', 'description');
		$query->where('id', $request->product_id);
		$results = $query->where('status', 'active')->first();
		if (isset($results->id)) { 	
			if ($category = RestaurantCategory::select('id', 'name', 'image','icon')->where('id', $results->item_category_id)->first()) {
				$results->category = $category;
			}else{
				$results->category = '';
			}
			$ingredient = RestaurantIngredient::select('id', 'name', 'price')->whereIn('id', explode(",", $results->ingredients_id))->get();
			if (!empty($ingredient) && count($ingredient) > 0) {
				$results->ingredient = $category;
			}else{
				$results->ingredient = '';
			}
		}
		return $results;
	}

	// function call to menuProductDetail
	public function menuProductDetail(Request $request) {
		$this->checkKeys(array_keys($request->all()), array("product_id", "device_id", "device_token", "device_type"));
		try {
			$input = $request->all();
			$validate = Validator($request->all(), [
			 	'product_id' => 'required|numeric'
			]);
			$attr = [
				'product_id' => 'Product Id'
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
					if ((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
						$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
						if (($user->table_toggle == 'Yes') && ($user->store_category == '44')) {
							$userData = $this->vendorMenuProductDetail($request);
							if ($userData) {
								$this->response = array(
									"status" => 200,
									"message" => ResponseMessages::getStatusCodeMessages(125),
									"data" => !empty($userData) ? $userData : null,
								);
							}else{
								$this->response = array(
									"status" => 300,
									"message" => ResponseMessages::getStatusCodeMessages(520),
									"data" => null,
								);
							}
						}else{
							$this->response = array(
								"status" => 300,
								"message" => ResponseMessages::getStatusCodeMessages(585),
								"data" => null,
							);
						}
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(5),
							"data" => null,
						);
					}
				}else{
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(214),
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

	// function call to menuProductOrder
	public function menuProductOrder(Request $request) {
		$this->checkKeys(array_keys($request->all()), array("table_no", "product", "device_id", "device_token", "device_type"));
		try {
			$input = $request->all();
			// Log::info(['login' => $input]);
			$validate = Validator($request->all(), [
			 	'table_no' => 'required',
			 	'product' => 'required'
			]);
			$attr = [
				'table_no' => 'Table No',
				'product' => 'Product'
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
					if ((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
						// dd($user);
						$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
						if (($user->table_toggle == 'Yes') && ($user->store_category == '44')) {
							$order_total_amount = 0;
							if ($RestaurantOrder = RestaurantOrder::where('vendor_id', $user->id)->where('table_no', $request->table_no)->whereIn('status', ['Pending', 'Preparing', 'Prepared'])->first()) {
								$RestaurantOrder->updated_at = date('Y-m-d H:i:s');
								$order_total_amount = $RestaurantOrder->amount;
							}else{
								$RestaurantOrder = new RestaurantOrder;
								$RestaurantOrder->vendor_id = $user->id;
								$RestaurantOrder->table_no = $request->table_no;
								$RestaurantOrder->amount = $order_total_amount;
								$RestaurantOrder->status = 'Pending';
								$RestaurantOrder->created_at = date('Y-m-d H:i:s');
							}
							if ($RestaurantOrder->save()) {
								if ($request->product != '') {
	                                $arrayDatas = $request->product;
									
	                                if (!empty($arrayDatas)) {
	                                    foreach($arrayDatas as $k => $val){
	                                        if (isset($val['product_id']) && !empty($val['product_id']) && !empty($val['qty']) && isset($val['qty'])) {
	                                        	if ($p = RestaurantItem::where('id', $val['product_id'])->first()) {
		                                        	$item_amount = $p->price;
		                                        	$ingredient_price = 0;
		                                        	$total_ingredient_price = 0;
		                                        	$total_amount = 0;
		                                            $RestaurantOrderItem = new RestaurantOrderItem;
		                                            $RestaurantOrderItem->restaurant_order_id = $RestaurantOrder->id;
		                                            $RestaurantOrderItem->vendor_id = $user->id;
		                                            $RestaurantOrderItem->item_id = $val['product_id'];
		                                            $RestaurantOrderItem->quantity = $val['qty'];
		                                            $RestaurantOrderItem->amount = $item_amount;
		                                            $RestaurantOrderItem->ingredient_price = $ingredient_price;
		                                            $RestaurantOrderItem->total_amount = $item_amount * $val['qty'];
		                                            $RestaurantOrderItem->status = 'Pending';
		                                            $RestaurantOrderItem->created_at = date('Y-m-d H:i:s');
		                                            $RestaurantOrderItem->updated_at = date('Y-m-d H:i:s');
		                                            if ($RestaurantOrderItem->save()) {
		                                            	if (isset($val['ingredient_id']) && !empty($val['ingredient_id'])) {
		                                            		$ingredients = explode(",", $val['ingredient_id']);
		                                            		if (!empty($ingredients)) {
		                                            			foreach($ingredients as $k1 => $val1){
		                                            				if ($i = RestaurantIngredient::where('id', $val1)->first()) {
			                                            				$ingredient_amount = $i->price;
			                                            				$total_ingredient_price = $total_ingredient_price + $i->price;
			                                            				$RestaurantOrderItemIngredient = new RestaurantOrderItemIngredient;
							                                            $RestaurantOrderItemIngredient->restaurant_order_item_id = $RestaurantOrderItem->id;
							                                            $RestaurantOrderItemIngredient->ingredient_id = $val1;
							                                            $RestaurantOrderItemIngredient->amount = $ingredient_amount;
							                                            $RestaurantOrderItemIngredient->created_at = date('Y-m-d H:i:s');
							                                            $RestaurantOrderItemIngredient->updated_at = date('Y-m-d H:i:s');
							                                            $RestaurantOrderItemIngredient->save();
							                                        }
		                                            			}
		                                            		}
		                                            	}
		                                            	$RestaurantOrderItem->ingredient_price = $total_ingredient_price;
		                                            	$RestaurantOrderItem->total_amount = $RestaurantOrderItem->total_amount + $total_ingredient_price;
		                                            	$RestaurantOrderItem->save();
		                                            	$order_total_amount = $order_total_amount + $RestaurantOrderItem->total_amount;
		                                            }
		                                        }
	                                        }
	                                    }
	                                }
	                            }
	                            $RestaurantOrder->amount = $order_total_amount;
								$RestaurantOrder->status = 'Pending';
								$RestaurantOrder->save();
								$this->response = array(
									"status" => 200,
									"message" => ResponseMessages::getStatusCodeMessages(218),
									"data" => $RestaurantOrder,
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
								"message" => ResponseMessages::getStatusCodeMessages(585),
								"data" => null,
							);
						}
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(5),
							"data" => null,
						);
					}
				}else{
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(214),
						"data" => null,
					);
				}
			}
		} catch (\Exception $ex) {
			dd($ex);
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
				"data" => null,
			);
		}
		$this->shut_down();
			exit;
	}


	// function call to customer_profileDetail
	public function customer_profileDetail(Request $request) {
		$this->checkKeys(array_keys($request->all()), array("device_id", "device_token", "device_type"));
		try {
			if (($request->device_type == 'android') || ($request->device_type == 'ios') || ($request->device_type == 'web')) {
				if ((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
					$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
					$userData = $this->customerProfileDetails($user);
					if ($userData) {
						$this->response = array(
							"status" => 200,
							"message" => ResponseMessages::getStatusCodeMessages(125),
							"data" => !empty($userData) ? $userData : null,
						);
					}else{
						$this->response = array(
							"status" => 300,
							"message" => ResponseMessages::getStatusCodeMessages(520),
							"data" => null,
						);
					}
				} else {
					$this->response = array(
						"status" => 403,
						"message" => ResponseMessages::getStatusCodeMessages(5),
						"data" => null,
					);
				}
			}else{
				$this->response = array(
					"status" => 300,
					"message" => ResponseMessages::getStatusCodeMessages(214),
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
	public function customerProfileDetails($user) {
		$users = User::select('id', 'name', 'email', 'phone_code', 'mobile', 'avatar as image', 'referral_code', 'total_invite', DB::raw("DATE_FORMAT(dob,'%b %d, %Y') as dob"), DB::raw("DATE_FORMAT(created_at,'%b %d, %Y') as created_date"))->withoutAppends()->where('id', $user->id)->where('status', 'active')->first();
		return $users;
	}	

	public function customerOfferList($user, $page_no, $request) {

        $query = Vouchar::select('id', 'name', 'discount_type', 'description', 'amount', 'minimum_buy_amount', 'cash_point', 'loyalty_point', DB::raw("DATE_FORMAT(start_date,'%b %d, %Y') as start_date"), DB::raw("DATE_FORMAT(end_date,'%b %d, %Y') as end_date"))->where('start_date', '<=', date('Y-m-d'))->where('end_date', '>=', date('Y-m-d'));  
        if (isset($request->retailer_id) && !empty($request->retailer_id)) {
            $query->where('vendor_id', $request->retailer_id);
        }
        if (isset($request->search) && !empty($request->search)) {
            $query->where(function($q) use ($request){
                $q->where('name', 'LIKE', "%{$request->search}%");
                $q->orWhere('description', 'LIKE', "%{$request->search}%");
            });  
        }   
        $vouchars = $query->where('status', 'active')
                        ->orderBy('end_date', 'asc')
                        ->offset($page_no*50)->take(50)->get();  
		return $vouchars;

	}

	//Use for customer_offerList
	public function customer_offerList(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("page", "device_id", "device_token", "device_type"));
		try {
			$input = $request->all();
			$validate = Validator($request->all(), [
			 	'page' => 'required|numeric',
			 	//'retailer_id' => 'numeric',
			]);
			$attr = [
				'page' => 'Page No',
				//'retailer_id' => 'Retailer Id',
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
					if ((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
						$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
						if (isset($request->page) && !empty($request->page)) {
							$page_no = $request->page - 1;
						}else{
							$page_no = 0;
						}
						$userData = $this->customerOfferList($user, $page_no, $request);
						if (!empty($userData) && count($userData) > 0) {
							$this->response = array(
								"status" => 200,
								"message" => ResponseMessages::getStatusCodeMessages(125),
								"data" => !empty($userData) ? $userData : null,
							);
						}else{
							$this->response = array(
								"status" => 300,
								"message" => ResponseMessages::getStatusCodeMessages(520),
								"data" => null,
							);
						}
						
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(5),
							"data" => null,
						);
					}
				}else{
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(214),
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


	public function customerMegaOfferList($user, $page_no, $request) {
		$query_mega_offer = User::select('id', 'store_name', 'store_logo')
		    ->has('voucher')
		    /*->with(['voucher' => function ($q1) {
		        $q1->select('id', 'name', 'discount_type', 'description', 'amount', 'minimum_buy_amount', 'cash_point', 'loyalty_point', DB::raw("DATE_FORMAT(start_date,'%b %d, %Y') as start_date"), DB::raw("DATE_FORMAT(end_date,'%b %d, %Y') as end_date"));
		    }])*/
		    ->whereHas('voucher', function ($q) use ($request) {
		        $q->where('start_date', '<=', date('Y-m-d'))
		          ->where('end_date', '>=', date('Y-m-d'))
		          ->where('status', 'active');
		        if (isset($request->search) && !empty($request->search)) {
		            $q->where(function($q2) use ($request){
		                $q2->where('name', 'LIKE', "%{$request->search}%")
		                   ->orWhere('description', 'LIKE', "%{$request->search}%");
		            });  
		        } 
		    });
		if (isset($request->search) && !empty($request->search)) {
		    $query_mega_offer->where(function($q) use ($request){
		        $q->where('name', 'LIKE', "%{$request->search}%")
		          ->orWhere('store_name', 'LIKE', "%{$request->search}%");
		    });  
		}  

		$mega_offer = $query_mega_offer->where('status', 'active')
		    ->where('role', 'Vendor')
		    ->orderBy('store_name', 'asc')
		    ->offset($page_no * 50)
		    ->take(50)
		    ->get();
		if (!empty($mega_offer)) {
			foreach ($mega_offer as $key => $value) {
				$value->voucher = Vouchar::select('id', 'name', 'discount_type', 'description', 'amount', 'minimum_buy_amount', 'cash_point', 'loyalty_point', DB::raw("DATE_FORMAT(start_date,'%b %d, %Y') as start_date"), DB::raw("DATE_FORMAT(end_date,'%b %d, %Y') as end_date"))->where('start_date', '<=', date('Y-m-d'))->where('end_date', '>=', date('Y-m-d'))->where('vendor_id', $value->id)->where('status', 'active')->first(); 
			}
		}
        return $mega_offer;

	}

	//Use for customer_megaOfferList
	public function customer_megaOfferList(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("page", "device_id", "device_token", "device_type"));
		try {
			$input = $request->all();
			$validate = Validator($request->all(), [
			 	'page' => 'required|numeric',
			 	//'retailer_id' => 'numeric',
			]);
			$attr = [
				'page' => 'Page No',
				//'retailer_id' => 'Retailer Id',
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
					if ((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
						$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
						if (isset($request->page) && !empty($request->page)) {
							$page_no = $request->page - 1;
						}else{
							$page_no = 0;
						}
						$userData = $this->customerMegaOfferList($user, $page_no, $request);
						if (!empty($userData) && count($userData) > 0) {
							$this->response = array(
								"status" => 200,
								"message" => ResponseMessages::getStatusCodeMessages(125),
								"data" => !empty($userData) ? $userData : null,
							);
						}else{
							$this->response = array(
								"status" => 300,
								"message" => ResponseMessages::getStatusCodeMessages(520),
								"data" => null,
							);
						}
						
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(5),
							"data" => null,
						);
					}
				}else{
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(214),
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


	public function customerRecentlyAddedCouponList($user, $page_no, $request) {

        $query = Vouchar::select('id', 'name', 'discount_type', 'description', 'amount', 'minimum_buy_amount', 'cash_point', 'loyalty_point', DB::raw("DATE_FORMAT(start_date,'%b %d, %Y') as start_date"), DB::raw("DATE_FORMAT(end_date,'%b %d, %Y') as end_date"))->where('start_date', '<=', date('Y-m-d'))->where('end_date', '>=', date('Y-m-d'));  
        if (isset($request->retailer_id) && !empty($request->retailer_id)) {
            $query->where('vendor_id', $request->retailer_id);
        }
        if (isset($request->search) && !empty($request->search)) {
            $query->where(function($q) use ($request){
                $q->where('name', 'LIKE', "%{$request->search}%");
                $q->orWhere('description', 'LIKE', "%{$request->search}%");
            });  
        }   
        $vouchars = $query->where('status', 'active')
                        ->orderBy('id', 'desc')
                        ->offset($page_no*50)->take(50)->get();  
		return $vouchars;

	}

	//Use for customer_recentlyAddedCouponList
	public function customer_recentlyAddedCouponList(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("page", "device_id", "device_token", "device_type"));
		try {
			$input = $request->all();
			$validate = Validator($request->all(), [
			 	'page' => 'required|numeric',
			 	//'retailer_id' => 'numeric',
			]);
			$attr = [
				'page' => 'Page No',
				//'retailer_id' => 'Retailer Id',
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
					if ((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
						$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
						if (isset($request->page) && !empty($request->page)) {
							$page_no = $request->page - 1;
						}else{
							$page_no = 0;
						}
						$userData = $this->customerRecentlyAddedCouponList($user, $page_no, $request);
						if (!empty($userData) && count($userData) > 0) {
							$this->response = array(
								"status" => 200,
								"message" => ResponseMessages::getStatusCodeMessages(125),
								"data" => !empty($userData) ? $userData : null,
							);
						}else{
							$this->response = array(
								"status" => 300,
								"message" => ResponseMessages::getStatusCodeMessages(520),
								"data" => null,
							);
						}
						
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(5),
							"data" => null,
						);
					}
				}else{
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(214),
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
	public function customerrecommendedOfferList($user, $page_no, $request) {
		$ids = VendorCustomer::where('customer_id', $user->id)->pluck('vendor_id')->toArray();
        $query = Vouchar::select('id', 'name', 'discount_type', 'description', 'amount', 'minimum_buy_amount', 'cash_point', 'loyalty_point', DB::raw("DATE_FORMAT(start_date,'%b %d, %Y') as start_date"), DB::raw("DATE_FORMAT(end_date,'%b %d, %Y') as end_date"))->where('start_date', '<=', date('Y-m-d'))->where('end_date', '>=', date('Y-m-d'));  
        if (isset($request->retailer_id) && !empty($request->retailer_id)) {
            $query->where('vendor_id', $request->retailer_id);
        }
        if (isset($request->search) && !empty($request->search)) {
            $query->where(function($q) use ($request){
                $q->where('name', 'LIKE', "%{$request->search}%");
                $q->orWhere('description', 'LIKE', "%{$request->search}%");
            });  
        }   
        $vouchars = $query->where('status', 'active')
                        ->whereIn('vendor_id', $ids)
                        ->orderBy('end_date', 'asc')
                        ->offset($page_no*50)->take(50)->get();  
		return $vouchars;

	}

	//Use for customer_offerList
	public function customer_recommendedOfferList(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("page", "device_id", "device_token", "device_type"));
		try {
			$input = $request->all();
			$validate = Validator($request->all(), [
			 	'page' => 'required|numeric',
			 	//'retailer_id' => 'numeric',
			]);
			$attr = [
				'page' => 'Page No',
				//'retailer_id' => 'Retailer Id',
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
					if ((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
						$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
						if (isset($request->page) && !empty($request->page)) {
							$page_no = $request->page - 1;
						}else{
							$page_no = 0;
						}
						$userData = $this->customerrecommendedOfferList($user, $page_no, $request);
						if (!empty($userData) && count($userData) > 0) {
							$this->response = array(
								"status" => 200,
								"message" => ResponseMessages::getStatusCodeMessages(125),
								"data" => !empty($userData) ? $userData : null,
							);
						}else{
							$this->response = array(
								"status" => 300,
								"message" => ResponseMessages::getStatusCodeMessages(520),
								"data" => null,
							);
						}
						
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(5),
							"data" => null,
						);
					}
				}else{
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(214),
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
	public function customerBrandList($user, $page_no, $request) {

        $query = Brand::select('id', 'name', 'description', 'image');  
        if (isset($request->search) && !empty($request->search)) {
            $query->where(function($q) use ($request){
                $q->where('name', 'LIKE', "%{$request->search}%");
                $q->orWhere('description', 'LIKE', "%{$request->search}%");
            });  
        }   
        $results = $query->where('status', 'active')
                        ->orderBy('name', 'asc')
                        ->offset($page_no*50)->take(50)->get();  
		return $results;

	}

	//Use for brandList
	public function brandList(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("page", "device_id", "device_token", "device_type"));
		try {
			$input = $request->all();
			$validate = Validator($request->all(), [
			 	'page' => 'required|numeric',
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
				if (($request->device_type == 'android') || ($request->device_type == 'ios') || ($request->device_type == 'web')) {
					if ((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
						$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
						if (isset($request->page) && !empty($request->page)) {
							$page_no = $request->page - 1;
						}else{
							$page_no = 0;
						}
						$userData = $this->customerBrandList($user, $page_no, $request);
						if (!empty($userData) && count($userData) > 0) {
							$this->response = array(
								"status" => 200,
								"message" => ResponseMessages::getStatusCodeMessages(125),
								"data" => !empty($userData) ? $userData : null,
							);
						}else{
							$this->response = array(
								"status" => 300,
								"message" => ResponseMessages::getStatusCodeMessages(520),
								"data" => null,
							);
						}
						
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(5),
							"data" => null,
						);
					}
				}else{
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(214),
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
	public function customerHomePageData(Request $request) {
		$this->checkKeys(array_keys($request->all()), array("search", "device_id", "device_token", "device_type"));
		try {
			if (($request->device_type == 'android') || ($request->device_type == 'ios') || ($request->device_type == 'web')) {
				if ((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
					$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
					/*$query_brand = Brand::select('id', 'name', 'image');  
			        if (isset($request->search) && !empty($request->search)) {
			            $query_brand->where(function($q) use ($request){
			                $q->where('name', 'LIKE', "%{$request->search}%");
			                $q->orWhere('description', 'LIKE', "%{$request->search}%");
			            });  
			        }   
			        $brand = $query_brand->where('status', 'active')
			                        ->orderBy('name', 'asc')
			                        ->limit(20)->get(); */

			        $query_store = User::select('id', 'store_name', 'store_logo');  
			        if (isset($request->search) && !empty($request->search)) {
			            $query_store->where(function($q) use ($request){
			                $q->where('name', 'LIKE', "%{$request->search}%");
			                $q->orWhere('store_name', 'LIKE', "%{$request->search}%");
			            });  
			        }   
			        $store = $query_store->where('status', 'active')->where('role', 'Vendor')
			                        ->orderBy('store_name', 'asc')
			                        ->limit(20)->get();

			        $offer_banner_first = [];

			        $query_coupon_store = Vouchar::select('id', 'name', 'discount_type', 'description', 'amount', 'minimum_buy_amount', 'cash_point', 'loyalty_point', DB::raw("DATE_FORMAT(start_date,'%b %d, %Y') as start_date"), DB::raw("DATE_FORMAT(end_date,'%b %d, %Y') as end_date"))->where('start_date', '<=', date('Y-m-d'))->where('end_date', '>=', date('Y-m-d'));  
			        
			        if (isset($request->search) && !empty($request->search)) {
			            $query_coupon_store->where(function($q) use ($request){
			                $q->where('name', 'LIKE', "%{$request->search}%");
			                $q->orWhere('description', 'LIKE', "%{$request->search}%");
			            });  
			        }   
			        $explore_coupon_store = $query_coupon_store->where('status', 'active')
			                        ->orderBy('end_date', 'asc')
			                        ->limit(20)->get();  

					$recommended_for_you_coupon_ids = VendorCustomer::where('customer_id', $user->id)->pluck('vendor_id')->toArray();
					$query_recommended_for_you_coupon = Vouchar::select('id', 'name', 'discount_type', 'description', 'amount', 'minimum_buy_amount', 'cash_point', 'loyalty_point', DB::raw("DATE_FORMAT(start_date,'%b %d, %Y') as start_date"), DB::raw("DATE_FORMAT(end_date,'%b %d, %Y') as end_date"))->where('start_date', '<=', date('Y-m-d'))->where('end_date', '>=', date('Y-m-d'));  
			        if (isset($request->search) && !empty($request->search)) {
			            $query_recommended_for_you_coupon->where(function($q) use ($request){
			                $q->where('name', 'LIKE', "%{$request->search}%");
			                $q->orWhere('description', 'LIKE', "%{$request->search}%");
			            });  
			        }   
			        $recommended_for_you_coupon = $query_recommended_for_you_coupon->where('status', 'active')
			        	->whereIn('vendor_id', $recommended_for_you_coupon_ids)
                        ->orderBy('end_date', 'asc')
                        ->limit(20)->get();  

					$offer_banner_second = [];
			        $query_category = Category::select('id', 'name', 'image');  
			        if (isset($request->search) && !empty($request->search)) {
			            $query_category->where(function($q) use ($request){
			                $q->where('name', 'LIKE', "%{$request->search}%");
			            });  
			        }   
			        $recommended_for_you_category = $query_category->where('status', 'active')
			                        ->orderBy('name', 'asc')
			                        ->limit(20)->get(); 

			        $query_mega_offer = User::select('id', 'store_name', 'store_logo');  
			        if (isset($request->search) && !empty($request->search)) {
			            $query_mega_offer->where(function($q) use ($request){
			                $q->where('name', 'LIKE', "%{$request->search}%");
			                $q->orWhere('store_name', 'LIKE', "%{$request->search}%");
			            });  
			        }   
			        $query_mega_offer/*->with(['voucher' => function ($q) {
						$q->select('id', 'name', 'discount_type', 'description', 'amount', 'minimum_buy_amount', 'cash_point', 'loyalty_point', DB::raw("DATE_FORMAT(start_date,'%b %d, %Y') as start_date"), DB::raw("DATE_FORMAT(end_date,'%b %d, %Y') as end_date"));
					}])*/->whereHas('voucher', function ($q) use ($request) {
					    $q->where('start_date', '<=', date('Y-m-d'))->where('end_date', '>=', date('Y-m-d'))->where('status', 'active');
					    if (isset($request->search) && !empty($request->search)) {
				            $q->where(function($q2) use ($request){
				                $q2->where('name', 'LIKE', "%{$request->search}%");
				                $q2->orWhere('description', 'LIKE', "%{$request->search}%");
				            });  
				        } 
					});
			        $mega_offer = $query_mega_offer->where('status', 'active')->where('role', 'Vendor')
			                        ->orderBy('store_name', 'asc')
			                        ->limit(20)->get();
			        if (!empty($mega_offer)) {
						foreach ($mega_offer as $key => $value) {
							$value->voucher = Vouchar::select('id', 'name', 'discount_type', 'description', 'amount', 'minimum_buy_amount', 'cash_point', 'loyalty_point', DB::raw("DATE_FORMAT(start_date,'%b %d, %Y') as start_date"), DB::raw("DATE_FORMAT(end_date,'%b %d, %Y') as end_date"))->where('start_date', '<=', date('Y-m-d'))->where('end_date', '>=', date('Y-m-d'))->where('vendor_id', $value->id)->where('status', 'active')->first(); 
						}
					}                
					$plan_list = [];

					$query_added_coupon = Vouchar::select('id', 'name', 'discount_type', 'description', 'amount', 'minimum_buy_amount', 'cash_point', 'loyalty_point', DB::raw("DATE_FORMAT(start_date,'%b %d, %Y') as start_date"), DB::raw("DATE_FORMAT(end_date,'%b %d, %Y') as end_date"))->where('start_date', '<=', date('Y-m-d'))->where('end_date', '>=', date('Y-m-d'));
			        if (isset($request->search) && !empty($request->search)) {
			            $query_added_coupon->where(function($q) use ($request){
			                $q->where('name', 'LIKE', "%{$request->search}%");
			                $q->orWhere('description', 'LIKE', "%{$request->search}%");
			            });  
			        }   
			        $recently_added_coupon = $query_added_coupon->where('status', 'active')
			                        ->orderBy('id', 'desc')
			                        ->limit(20)->get(); 
					$total_invite = 10;
					$total_use_invite = $user->total_invite;
					$get_loyalty_point = 20;
					$get_cash_point = 5;
					$invite_friend = [
						'use_invite' => $total_use_invite,
						'total_invite' => $total_invite,
						'get_loyalty_point' => $get_loyalty_point,
						'get_cash_point' => $get_cash_point,
					];
					//$data['brand'] = $brand;
					$data['store'] = $store;
					$data['offer_banner_first'] = $offer_banner_first;
					$data['explore_coupon_store'] = $explore_coupon_store;
					$data['recommended_for_you_coupon'] = $recommended_for_you_coupon;
					$data['offer_banner_second'] = $offer_banner_second;
					$data['recommended_for_you_category'] = $recommended_for_you_category;
					$data['mega_offer'] = $mega_offer;
					$data['plan_list'] = $plan_list;
					$data['recently_added_coupon'] = $recently_added_coupon;
					$data['invite_friend'] = $invite_friend;
					if ($data) {
						$this->response = array(
							"status" => 200,
							"message" => ResponseMessages::getStatusCodeMessages(125),
							"data" => !empty($data) ? $data : null,
						);
					}else{
						$this->response = array(
							"status" => 300,
							"message" => ResponseMessages::getStatusCodeMessages(520),
							"data" => null,
						);
					}
				} else {
					$this->response = array(
						"status" => 403,
						"message" => ResponseMessages::getStatusCodeMessages(5),
						"data" => null,
					);
				}
			}
			else{
				$this->response = array(
					"status" => 300,
					"message" => ResponseMessages::getStatusCodeMessages(214),
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





































	// public function transaction(Request $request)
	// {
	// 	$this->checkKeys(array_keys($request->all()), array("device_id", "device_token", "device_type"));
	// 		try {
	// 			if (($request->device_type == 'android') || ($request->device_type == 'ios') || ($request->device_type == 'web')) {
	// 				if ((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
	// 					// $transactiondata = Transaction::where(['user_id' => $user->id])->orderBy('id','desc')->get();
	
	// 					$query = Transaction::where(['user_id'=> $user->id]);
						

	// 					// Filter by date
	// 					if (isset($request->date) && !empty($request->date)) {
	// 						$query->whereDate('created_at', $request->input('date'));
	// 					}
		
	// 					// Filter by date range
	// 					if ((isset($request->start_date) && !empty($request->start_date)) && (isset($request->end_date) && !empty($request->end_date))) {
	// 						$query->whereBetween('created_at', [$request->input('start_date'), $request->input('end_date')]);
	// 					}
		
	// 					// Filter by box
	// 					if (isset($request->box_id) && !empty($request->box_id)) {
	// 						$query->where('order_id', $request->input('box_id'));
	// 					}
		
	// 					$transactiondata = $query->orderBy('id', 'desc')->get();
						

	// 					if($transactiondata){
	// 						$this->response = array(
	// 							"status" => 200,
	// 							"message" => ResponseMessages::getStatusCodeMessages(125),
	// 							"data" => !empty($transactiondata) ? $transactiondata : null,
	// 						);

	// 					}else{
	// 						$this->response = array(
	// 							"status" => 300,
	// 							"message" => ResponseMessages::getStatusCodeMessages(520),
	// 							"data" => null,
	// 						);
	// 					}
					
	// 				} else {
	// 					$this->response = array(
	// 						"status" => 403,
	// 						"message" => ResponseMessages::getStatusCodeMessages(5),
	// 						"data" => null,
	// 					);
	// 				}
	// 			}else{
	// 				$this->response = array(
	// 					"status" => 300,
	// 					"message" => ResponseMessages::getStatusCodeMessages(214),
	// 					"data" => null,
	// 				);
	// 			}
	// 		} catch (\Exception $ex) {
	// 			$this->response = array(
	// 				"status" => 501,
	// 				"message" => ResponseMessages::getStatusCodeMessages(501),
	// 				"data" => null,
	// 			);
	// 		}
	// 		$this->shut_down();
	// 			exit;
	// }


	
	// function call to Box List
	// public function boxlist(Request $request)
	// {
	// 	$this->checkKeys(array_keys($request->all()), array("device_id", "device_token", "device_type"));
	// 	try {
	// 		if (($request->device_type == 'android') || ($request->device_type == 'ios') || ($request->device_type == 'web')) {
	// 			if((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
	// 				$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);

	// 				$currentDate = Carbon::now();
	// 				$firstDay = $currentDate->firstOfMonth();
	// 				$lastDay = $currentDate->lastOfMonth();
	// 				if($contest = ContestParticipant::where('start_date', '<=', $firstDay)->where('end_date', '>=', $lastDay)->where("status","active")->orderBy('id', 'desc')->first())
	// 				{
	// 					$contest_id = $contest->id;
	// 				}else{
	// 					$contest = new ContestParticipant;
	// 					$contest->name = $firstDay->format('F');
	// 					$contest->start_date = $firstDay;
	// 					$contest->end_date = $lastDay;
	// 					$contest->save();

	// 					$contest_id = $contest->id;
	// 				}

	// 				$numberOfBoxes = 3;

	// 				$userPurchasedBoxes = $user->Purchases()->where('contest_id', $contest_id)->pluck('box_id')->toArray();
					
	// 				$purchasedBoxes = BoxPrice::select('id', 'name', 'box_price', 'image')
	// 					->where("status", "active")
	// 					->whereIn('id', $userPurchasedBoxes);

	// 				$additionalBoxes = BoxPrice::select('id', 'name', 'box_price', 'image')
	// 					->where("status", "active")
	// 					->whereNotIn('id', $userPurchasedBoxes)
	// 					->inRandomOrder()
	// 					->take(3);

	// 				$boxshow = $purchasedBoxes->union($additionalBoxes)->inRandomOrder()->limit($numberOfBoxes)->get();
					
	// 				if(!empty($boxshow)){
						
	// 					foreach($boxshow as $k=>$val){
	// 						$val->is_purchased = false;
	// 						if($pBox = BoxPurchase::where('box_id', $val->id)->where('user_id', $user->id)->where('contest_id', $contest_id)->first()){
	// 							$val->is_purchased = true;
	// 						}
	// 					}
	// 					$this->response = array(
	// 						"status" => 200,
	// 						"message" => ResponseMessages::getStatusCodeMessages(125),
	// 						"data" => !empty($boxshow) ? $boxshow : null,
	// 					);

	// 				}else{
	// 					$this->response = array(
	// 						"status" => 300,
	// 						"message" => ResponseMessages::getStatusCodeMessages(520),
	// 						"data" => null,
	// 					);
	// 				}
	// 			} else {
	// 				$this->response = array(
	// 					"status" => 403,
	// 					"message" => ResponseMessages::getStatusCodeMessages(5),
	// 					"data" => null,
	// 				);
	// 			}
	// 		}else{
	// 			$this->response = array(
	// 				"status" => 300,
	// 				"message" => ResponseMessages::getStatusCodeMessages(214),
	// 				"data" => null,
	// 			);
	// 		}
	// 	} catch (\Exception $ex) {
	// 		$this->response = array(
	// 			"status" => 501,
	// 			"message" => ResponseMessages::getStatusCodeMessages(501),
	// 			"data" => null,
	// 		);
	// 	}
	// 	$this->shut_down();
	// 		exit;
	// }


	//  // function called to display box purchase
	// public function boxbuy(Request $request)
	// {
	// 	$this->checkKeys(array_keys($request->all()), array("device_id", "device_token", "device_type","box_id","transaction_no","payment_type"));
	// 	try {
	// 			$input = $request->all();
  	// 			$validate = Validator($request->all(), [
	// 				'box_id' => 'required',
    //         		'transaction_no' => 'required',
	// 				'payment_type' => 'required',
	// 	        ]);
	// 	        $attr = [
	// 	            'box_id' => 'Box Id',
	// 	            'transaction_no' => 'Transaction Id',
	// 				'payment_type' => 'Payment Type'
					
	// 	        ];
	// 	        $validate->setAttributeNames($attr);
	// 			if ($validate->fails()) {
	// 				$errors = $validate->errors();
	// 				$this->response = array(
	// 					"status" => 300,
	// 					"message" => $errors->first(),
	// 					"data" => null,
	// 					"errors" => $errors,
	// 				);
	// 			}else{

	// 				if (($request->device_type == 'android') || ($request->device_type == 'ios') || ($request->device_type == 'web')) {
	// 					if((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
	// 						// $this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
					
	// 						$currentDate = Carbon::now();
	// 						$firstDay = $currentDate->firstOfMonth();
	// 						$lastDay = $currentDate->lastOfMonth();
	// 						if($contest = ContestParticipant::where('start_date', '<=', $firstDay)->where('end_date', '>=', $lastDay)->where("status","active")->orderBy('id', 'desc')->first())
	// 						{
								
	// 						}else{
	// 							$contest = new ContestParticipant;
	// 							$contest->name = $firstDay->format('F');
	// 							$contest->start_date = $firstDay;
	// 							$contest->end_date = $lastDay;
	// 							$contest->save();
	// 						}

	// 							if(!BoxPurchase::where('user_id', $user->id)->where('contest_id', $contest->id)->where('box_id', $input['box_id'])->first()){
	// 								$boxpurchase = new BoxPurchase;
	// 								$boxpurchase->user_id = $user->id;
	// 								$boxpurchase->contest_id = $contest->id;
	// 								$boxpurchase->box_id = $input['box_id'];
	// 								$boxpurchase->save();

	// 								if($ContestJoin = ContestJoin::where('user_id', $contest->id)->where('contest_id', $contest->id)->first()){
	// 									$ContestJoin->purchase_box_id = $ContestJoin->purchase_box_id.','.$boxpurchase->box_id;
	// 									$ContestJoin->box_quantity = $ContestJoin->box_quantity + 1;
	// 								}else{
	// 									$ContestJoin = new ContestJoin;
	// 									$ContestJoin->purchase_box_id = $boxpurchase->box_id;
	// 									$ContestJoin->box_quantity = 1;
	// 								}
	// 								$ContestJoin->user_id = $user->id;
	// 								$ContestJoin->contest_id = $contest->id;
	// 								$ContestJoin->save();

	// 								$box = BoxPrice::find($boxpurchase->box_id);

	// 								$transaction_no ='TRN'.$this->generateReferralCode(1);
	// 								$boxtransation = new Transaction;
	// 								$boxtransation->user_id = $user->id;
	// 								$boxtransation->order_id = $boxpurchase->box_id;
	// 								// $boxtransation->transaction_no = $input['transaction_no'];
	// 								$boxtransation->transaction_no = $transaction_no;
	// 								$boxtransation->payment_type = $input['payment_type'];
	// 								$boxtransation->amount = $box->box_price;
	// 								$boxtransation->title = 'You have successfully purchased the ' .ucfirst($box->name);
	// 								$boxtransation->save();

	// 								$notification_count = 0;
	// 								$des = 'You have successfully purchased the ' .ucfirst($box->name);
	// 								$push = [
	// 									'sender_id' => 1,
	// 									'notification_type' => 'Box Purchase',
	// 									'notification_count' => $notification_count,
	// 									'title' => 'Box Purchase',
	// 									'description' => $des

	// 								];
	// 								$account_type = 'user';
	// 								$this->pushNotificationSendActive($user, $push, $account_type);

	// 								$this->response = array(
	// 									"status" => 200,
	// 									"message" => ResponseMessages::getStatusCodeMessages(561),
	// 									"data" => $box,
	// 								);
	// 							}else{
	// 								$this->response = array(
	// 									"status" => 300,
	// 									"message" => ResponseMessages::getStatusCodeMessages(560),
	// 									"data" => null,
	// 								);
	// 							}
							
	// 					} else {
	// 						$this->response = array(
	// 							"status" => 403,
	// 							"message" => ResponseMessages::getStatusCodeMessages(5),
	// 							"data" => null,
	// 						);
	// 					}
	// 				}else{
	// 					$this->response = array(
	// 						"status" => 300,
	// 						"message" => ResponseMessages::getStatusCodeMessages(214),
	// 						"data" => null,
	// 					);
	// 				}
	// 			}

	// 	} catch (\Exception $ex) {
	// 		$this->response = array(
	// 			"status" => 501,
	// 			"message" => ResponseMessages::getStatusCodeMessages(501),
	// 			"data" => null,
	// 		);
	// 	}
	// 	$this->shut_down();
	// 		exit;
	// }

	// public function getMonthlyLeaderboard(Request $request)
	// {
	// 	$this->checkKeys(array_keys($request->all()), array("device_id", "device_token", "device_type"));

    //     try {
			
	// 					if (($request->device_type == 'android') || ($request->device_type == 'ios') || ($request->device_type == 'web')) {
	// 						if ((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
	// 							$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);

	// 							$users = User::select('users.id', 'users.name','users.avatar')
	// 										->whereHas('participatedUsers', function ($q) use ($request) {
	// 											$q->where('contest_id', $request->contest_id);
	// 										})
	// 										->withCount([
	// 											'participatedUsers as total_wins' => function ($q) use ($request) {
	// 												$q->where('contest_id', $request->contest_id)->whereNotNull('gift_id');
	// 											},
	// 										])
	// 										->orderBy("total_wins","desc")
	// 										->get();

	// 										// // Add position to each user in the collection
	// 										// $users->each(function ($user, $index) {
	// 										// 	$user->position = $index + 1;
	// 										// });
									
	// 										$users = $users->sortByDesc('total_wins');
	// 										$position = 1;
	// 										$prevWins = null;

	// 										$users->each(function ($user) use (&$position, &$prevWins) {
												
	// 											$user->position = $position;
											
	// 											if ($user->total_wins !== $prevWins) {
	// 												$prevWins = $user->total_wins;
	// 												$position++;
	// 											}
	// 										});

	// 								if ($users ) {

	// 									$this->response = array(
	// 									"status" => 200,
	// 									"message" => ResponseMessages::getStatusCodeMessages(572),
	// 									"data" => $users,
	// 									);
								
	// 								} else {
	// 								$this->response = array(
	// 									"status" => 404,
	// 									"message" => ResponseMessages::getStatusCodeMessages(520),
	// 									"data" => null,
	// 									);
	// 								}
								
	// 						} else {
	// 						$this->response = array(
	// 									"status" => 403,
	// 									"message" => ResponseMessages::getStatusCodeMessages(5),
	// 									"data" => null,
	// 								);
	// 						}
	// 					}else{
	// 						$this->response = array(
	// 							"status" => 300,
	// 							"message" => ResponseMessages::getStatusCodeMessages(507),
	// 							"data" => null,
	// 						);
	// 					}
					
    //     } catch (\Exception $ex) {
    //         // 
    //         $this->response = array(
	// 			"status" => 501,
	// 			"message" => ResponseMessages::getStatusCodeMessages(501),
	// 			"data" => null,
	// 		);
    //     }
	// 	$this->shut_down();
	// 	exit;
	// }




	public function ReadAllNotification(Request $request)
	{
		$this->checkKeys(array_keys($request->all()), array("device_id", "device_token", "device_type"));

		try {
			if (($request->device_type == 'android') || ($request->device_type == 'ios') || ($request->device_type == 'web')) {
				if ((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
					$notification = Notificationuser::where('receiver_id', $user->id)
									->where('is_read', false)
									->update(['is_read' => true]);
	
					$this->response = array(
						"status" => 200,
						"message" => ResponseMessages::getStatusCodeMessages(570),
						"data" => $notification,
					);
				} else {
					$this->response = array(
						"status" => 403,
						"message" => ResponseMessages::getStatusCodeMessages(519),
						"data" => null,
					);
				}
			} else {
				$this->response = array(
					"status" => 300,
					"message" => ResponseMessages::getStatusCodeMessages(214),
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

	 // function called to display purchased box sections
	public function notificationDetail(Request $request)
	{
			$this->checkKeys(array_keys($request->all()), array("device_id", "device_token", "device_type"));
				try {
					$input = $request->all();
	  				$validate = Validator($request->all(), [
		            'order_id' => 'required',
		        ]);
		        $attr = [
		            'order_id' => 'Contest Id',
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
									if ((isset(Auth::user()->id)) && ($user = User::select('users.id','users.name as username','users.avatar as image')->where("id", Auth::user()->id)->first())) {
										

										$boxImageUrlBase = asset('uploads/boxprices') . '/';

										$notificationData = ContestJoin::select('box_prices.name', 'box_prices.box_price', 'box_prices.image','contest_joins.gift_id')
											->join('box_prices', 'contest_joins.purchase_box_id', '=', 'box_prices.id')
											->where('contest_id', $request->order_id)
											->where("user_id", Auth::user()->id)
											->get();

											if (!empty($notificationData)) {
													foreach ($notificationData as $value) {
														$value->gift = null;
		
														$value->image = $boxImageUrlBase . $value->image;
		
														
														if ($gift = Gift::select('id', 'name', 'gift_image as image')
															->where('id', $value->gift_id)
															->first()) {
															$value->gift = $gift;
														}else{
															$value->gift = null;
														}
													}
												}
										$user->box = $notificationData;

									
										$this->response = array(
											"status" => 200,
											"message" => ResponseMessages::getStatusCodeMessages(567),
											"data" => !empty($user) ? $user : null,
										);
									} else {
										$this->response = array(
											"status" => 403,
											"message" => ResponseMessages::getStatusCodeMessages(519),
											"data" => null,
										);
									}
							}else{
								$this->response = array(
									"status" => 300,
									"message" => ResponseMessages::getStatusCodeMessages(214),
									"data" => null,
								);
							}
								
						}
				} catch (\Exception $ex) {
					// 
						$this->response = array(
							"status" => 501,
							"message" => ResponseMessages::getStatusCodeMessages(501),
							"data" => null,
						);
				}
				$this->shut_down();
				exit;
	}



	public function deleteAllNotifications(Request $request)
	{
		$this->checkKeys(array_keys($request->all()), ["device_id", "device_token", "device_type"]);
		// $input = $request->all();
		// Log::info(['login' => $input]);

		try {
			if (in_array($request->device_type, ['android', 'ios', 'web'])) {
				if (Auth::check()) {
					$user = Auth::user();

					if ($user) {
						
						$notifications = NotificationUser::where('receiver_id', $user->id)->get();


						foreach ($notifications as $notification) {
							$notification->status = 'delete';
							$notification->save();
							// $notification->delete();
						}

						$this->response = [
							"status" => 200,
							"message" => ResponseMessages::getStatusCodeMessages(569),
							"data" => $notification,
						];
					} else {
						$this->response = [
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(5),
							"data" => null,
						];
					}
				} else {
					$this->response = [
						"status" => 403,
						"message" => ResponseMessages::getStatusCodeMessages(5),
						"data" => null,
					];
				}
			} else {
				$this->response = [
					"status" => 300,
					"message" => ResponseMessages::getStatusCodeMessages(564),
					"data" => null,
				];
			}
		} catch (\Exception $ex) {
			\Log::error($ex);

			$this->response = [
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
				"data" => null,
			];
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






	


	//Use for Item list






	public function AddItem(Request $request) {
		$this->checkKeys(array_keys($request->all()), array("device_type","name"));
		// dd($request->all());
		$access_token = '';
		try {
			$input = $request->all();
			// dd($input);
  				$validate = Validator($request->all(), [
	            'name' => 'required',
				// 'image' =>'required',

	        ]);
	        $attr = [
	            'name' => 'Item Name',
				// 'image' => 'Item Image',
		        
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
					if (Auth::user()->id) {
						$user = User::where('id', Auth::user()->id)->first();
					
						$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
						
						$items = new Item;
						$items->name = $input['name'];
						$items->vendor_id = $user->id;

						// if ($request->hasfile('image')) {
						// 	$file = $request->file('image');
						// 	$filename = time() . $file->getClientOriginalName();
						// 	$filename = str_replace(' ', '', $filename);
						// 	$filename = str_replace('.jpeg', '.jpg', $filename);
						// 	$file->move(public_path('uploads/items'), $filename);
						// 	if ($items->image != null && file_exists(public_path('uploads/items/' . $items->image))) {
						// 		if ($items->image != 'no_avatar.jpg') {
						// 			unlink(public_path('uploads/items/' . $items->image));
						// 		}
						// 	}
						// 	$items->image = $filename;
						// }
						$items->save();
	
						$this->response = array(
							"status" => 200,
							"message" => ResponseMessages::getStatusCodeMessages(576),
							"data" => !empty($items) ? $items : null,
						);
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(5),
							"data" => null,
						);
					}
				}
				else{
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(214),
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


	//place orders api 

	public function RestaurantItemAdd(Request $request) {
		$this->checkKeys(array_keys($request->all()), array("device_type","item_name","image"));
		
		$access_token = '';
		try {
			$input = $request->all();
			
  				$validate = Validator($request->all(), [
	            'item_name' => 'required',
				'image' =>'required',

	        ]);
	        $attr = [
	            'item_name' => 'Restaurant Item Name',
				'image' => 'Restaurant Item Image',
		        
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
					if (Auth::user()->id) {
						$user = User::where('id', Auth::user()->id)->first();
					
						$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
						
						$restaurantitems = new RestaurantItem;
						$restaurantitems->item_name = $input['item_name'];
						$restaurantitems->description = $input['description'];
						$restaurantitems->price = $input['price'];
						$restaurantitems->vendor_id = $user->id;

						if ($request->hasfile('image')) {
							$file = $request->file('image');
							$filename = time() . $file->getClientOriginalName();
							$filename = str_replace(' ', '', $filename);
							$filename = str_replace('.jpeg', '.jpg', $filename);
							$file->move(public_path('uploads/restaurantitems'), $filename);
							if ($restaurantitems->image != null && file_exists(public_path('uploads/restaurantitems/' . $restaurantitems->image))) {
								if ($restaurantitems->image != 'no_avatar.jpg') {
									unlink(public_path('uploads/restaurantitems/' . $restaurantitems->image));
								}
							}
							$restaurantitems->image = $filename;
						}
						$restaurantitems->save();
	
						$this->response = array(
							"status" => 200,
							"message" => ResponseMessages::getStatusCodeMessages(576),
							"data" => !empty($restaurantitems) ? $restaurantitems : null,
						);
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(5),
							"data" => null,
						);
					}
				}
				else{
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(214),
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

	public function RestaurantItemIngredientAdd(Request $request) {
		$this->checkKeys(array_keys($request->all()), array("device_type","name"));
		
		$access_token = '';
		try {
			$input = $request->all();
  				$validate = Validator($request->all(), [
	            'name' => 'required',
	        ]);
	        $attr = [
	            'name' => 'Restaurant Item Ingredient Name',
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
					if (Auth::user()->id) {
						$user = User::where('id', Auth::user()->id)->first();
					
						$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
						
						$restaurantitemingredients = new RestaurantItemIngredient;
						$restaurantitemingredients->name = $input['name'];
						$restaurantitemingredients->price = $input['price'];
						$restaurantitemingredients->save();
	
						$this->response = array(
							"status" => 200,
							"message" => ResponseMessages::getStatusCodeMessages(580),
							"data" => !empty($restaurantitemingredients) ? $restaurantitemingredients : null,
						);
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(5),
							"data" => null,
						);
					}
				}
				else{
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(214),
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

	public function placeOrder(Request $request) {
		$this->checkKeys(array_keys($request->all()), array("device_type"));
		
		$access_token = '';
		try {
			$input = $request->all();
  				$validate = Validator($request->all(), [
	            'customer_id' => 'required|exists:users,id',
	        ]);
	        $attr = [
	            'customer_id' => 'customer Name',
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
					if (Auth::user()->id) {
						$user = User::where('id', Auth::user()->id)->first();
					
						$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);

						$order = new ItemOrder();
						$order->customer_id = $input['customer_id'];
						$order->item_id = $input['item_id'];
						$order->quantity = $input['quantity'];
						// $order->status = $input['status'];
						// $order->total_price = $input['total_price'];

						// foreach ($request->input('restaurant_items') as $item) {
						// 	$order->items()->attach($item['item_id'], ['quantity' => $item['quantity']]);
						// }

						$order->save();

						$this->response = array(
							"status" => 200,
							"message" => ResponseMessages::getStatusCodeMessages(581),
							"data" => !empty($order) ? $order : null,
						);
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(5),
							"data" => null,
						);
					}
				}
				else{
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(214),
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

	public function updateStatus(Request $request)
    {
		$this->checkKeys(array_keys($request->all()), array("device_type","id","status"));
		
		$access_token = '';
		try {
			
			$input = $request->all();
  				$validate = Validator($request->all(), [
	            'id' => 'required',
				'status' => 'required|in:Pending,Preparing,Completed,Cancelled',
	        ]);
	        $attr = [

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
					if (Auth::user()->id) {
						$user = User::where('id', Auth::user()->id)->first();

						$id = $request->id;
                		$order = ItemOrder::find($id);
						
						$order->status = $input['status'];
        				$order->save();
						
						$this->response = array(
							"status" => 200,
							"message" => ResponseMessages::getStatusCodeMessages(582),
							"data" => !empty($order) ? $order : null,
						);
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(5),
							"data" => null,
						);
					}
				}
				else{
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(214),
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

	public function RestaurantOrderList(Request $request) {
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
						$query = ItemOrder::select('id', 'customer_id','item_id','quantity', 'status', DB::raw("DATE_FORMAT(created_at,'%b %d, %Y') as created_date"))
						->with([
							'restaurantItem' => function ($query) {
							$query->select('id', 'item_name');
							},
							'customer' => function ($query) {
								$query->select('id', 'name');
							}	
						]); 
						
						$data = $query->orderBy('status', 'asc')->offset($page_no*20)->take(5000)->get();
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


	

	public function CreateInvoiceOld(Request $request) {
		$this->checkKeys(array_keys($request->all()), array("device_type","name","mobile"));
	
		$access_token = '';
		try {
			$input = $request->all();
		
  				$validate = Validator($request->all(), [
	            'name' => 'required',
		        'mobile' => 'required|min:8|numeric',

	        ]);
	        $attr = [
	            'name' => 'Customer Name',
		        'mobile' => 'Phone No',
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
					if (Auth::user()->id) {
						$user = User::where('id', Auth::user()->id)->first();

						// $users = $user->select('users.store_name','users.mobile','users.address','users.created_at')->first();
						
						$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
						
						$password = $this->generateReferralCode(1);
						$customers = new User;
						$customers->name = $input['name'];
						$customers->phone_code = $input['phone_code'];
						$customers->mobile = $input['mobile'];
						$customers->password = Hash::make($password);
						$customers->type = 'Purchase';
						$customers->role = 'Customer';
						$customers->save();
						
						$invoice_no =$this->generateReferralCode(1);

						foreach ($input['items'] as $item) {
							$purchaseitem = new PurchaseItem;
							$purchaseitem->user_id = $customers->id;
							$purchaseitem->item_id = $item['item_id'];
							$purchaseitem->qty = $item['qty'];
							$purchaseitem->GST = $item['GST'];
							$purchaseitem->cost = $item['cost'];
							$purchaseitem->invoice_no = $invoice_no;
							$purchaseitem->save();

							$purchaseItems[] = $purchaseitem;
						}


						// $purchase = new Purchase;
						// $purchase->vendor_id = $user->id;
						// $purchase->customer_id = $customers->id;
						// $purchase->invoice_no = $purchaseitem->invoice_no;
						// $purchase->total_quantity = $customers->id;
						// $purchase->total_ammount = $customers->id;
						// $purchase->save();
						

						$vendoritem = new VendorCustomer;
						$vendoritem->vendor_id = $user->id;
						$vendoritem->customer_id = $customers->id;
						$vendoritem->save();

						// $userData = $this->userProfileDetails($user);
						// unset($userData->devices);
						$this->response = array(
							"status" => 200,
							"message" => ResponseMessages::getStatusCodeMessages(577),
							// "data" => !empty($customers) ? $customers : null,
							"data" => array(
								"user" => $user,
								"customers" => $customers,
								"purchaseitem" => $purchaseItems,
								
							),
							
						);
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(5),
							"data" => null,
						);
					}
				}
				else{
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(214),
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

	//function call to update Profile
	// public function UpdateInvoice(Request $request) {
    // 	$this->checkKeys(array_keys($request->all()));
    // 	$access_token = '';
	// 	try {
	// 		$input = $request->all();

	// 		$validate = Validator($request->all(), [
	// 			'id' => 'required',
	// 			'name' => 'required',
	// 			'mobile' => 'required|min:8|numeric',
	// 		]);

	// 		$attr = [
	// 			'id' => 'Invoice Id',
	// 			'name' => 'Customer Name',
	// 			'mobile' => 'Phone No',
	// 		];
			
	// 		$validate->setAttributeNames($attr);

	// 		if ($validate->fails()) {
	// 			$errors = $validate->errors();
	// 			$this->response = [
	// 				"status" => 300,
	// 				"message" => $errors->first(),
	// 				"data" => null,
	// 				"errors" => $errors,
	// 			];
	// 		} else {
	// 			if (($request->device_type == 'android') || ($request->device_type == 'ios') || ($request->device_type == 'web')) {
	// 				if (Auth::user()->id) {
	// 					$user = User::where('id', Auth::user()->id)->first();

	// 					$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);

	// 					$invoice = User::with('purchaseItems')->find($request['id']);
	// 					// $invoice = User::where(['id' => $request['id']])->first();
	// 					// dd($invoice);

	// 					if ($invoice) {
	// 						$invoice->name = $input['name'];
	// 						$invoice->phone_code = $input['phone_code'];
	// 						$invoice->mobile = $input['mobile'];
	// 						$invoice->type = 'Purchase';
	// 						$invoice->role = 'Customer';
	// 						$invoice->save();

	// 						foreach ($input['items'] as $item) {
	// 							$purchaseitem = new PurchaseItem;
	// 							$purchaseitem->user_id = $invoice->id;
	// 							$purchaseitem->item_id = $item['item_id'];
	// 							$purchaseitem->qty = $item['qty'];
	// 							$purchaseitem->GST = $item['GST'];
	// 							$purchaseitem->cost = $item['cost'];
	// 							// $purchaseitem->invoice_no = $invoice_no;
	// 							$purchaseitem->save();

	// 							$purchaseItems[] = $purchaseitem;
	// 						}

	// 						$this->response = [
	// 							"status" => 200,
	// 							"message" => ResponseMessages::getStatusCodeMessages(578), // Update message
	// 							"data" => [
	// 								"invoice" => $invoice,
	// 								"purchaseitems" => $purchaseItems,
	// 							],
	// 						];
	// 					} else {
	// 						$this->response = [
	// 							"status" => 404,
	// 							"message" => ResponseMessages::getStatusCodeMessages(404), // Invoice not found
	// 							"data" => null,
	// 						];
	// 					}
	// 				} else {
	// 					$this->response = [
	// 						"status" => 403,
	// 						"message" => ResponseMessages::getStatusCodeMessages(5),
	// 						"data" => null,
	// 					];
	// 				}
	// 			} else {
	// 				$this->response = [
	// 					"status" => 300,
	// 					"message" => ResponseMessages::getStatusCodeMessages(214),
	// 					"data" => null,
	// 				];
	// 			}
	// 		}
	// 	} catch (\Exception $ex) {
	// 		
	// 		// Handle exceptions
	// 		$this->response = [
	// 			"status" => 501,
	// 			"message" => ResponseMessages::getStatusCodeMessages(501),
	// 			"data" => null,
	// 		];
	// 	}

	// 	$this->shut_down();
	// 	exit;
	// }

	public function UpdateInvoiceOld(Request $request)
{
    $this->checkKeys(array_keys($request->all()));

    try {
        $input = $request->all();

			$validate = Validator($request->all(), [
				'id' => 'required',
				'name' => 'required',
				'mobile' => 'required|min:8|numeric',
			]);

			$attr = [
				'id' => 'Invoice Id',
				'name' => 'Customer Name',
				'mobile' => 'Phone No',
			];

        if ($validate->fails()) {
            return response()->json([
                "status" => 300,
                "message" => $validate->errors()->first(),
                "data" => null,
                "errors" => $validate->errors(),
            ], 400);
        }

        $user = Auth::user();

        if (!$user) {
            return response()->json([
                "status" => 403,
                "message" => ResponseMessages::getStatusCodeMessages(5),
                "data" => null,
            ], 403);
        }

        $this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);

        $invoice = User::with('purchaseItems')->find($request['id']);

        if (!$invoice) {
            return response()->json([
                "status" => 404,
                "message" => ResponseMessages::getStatusCodeMessages(404), // Invoice not found
                "data" => null,
            ], 404);
        }

        $invoice->name = $input['name'];
        $invoice->phone_code = $input['phone_code'];
        $invoice->mobile = $input['mobile'];
        $invoice->type = 'Purchase';
        $invoice->role = 'Customer';
        $invoice->save();

        // Update or create purchase items
        // $purchaseItems = [];

        // foreach ($input['items'] as $item) {
        //     $purchaseitem = new PurchaseItem([
        //         'item_id' => $item['item_id'],
        //         'qty' => $item['qty'],
        //         'GST' => $item['GST'],
        //         'cost' => $item['cost'],
        //     ]);
			
        //     $purchaseItems[] = $purchaseitem;
        // }
		// foreach ($input['items'] as $item) {
		// 	$purchaseitem = new PurchaseItem;
		// 	$purchaseitem->user_id = $invoice->id;
		// 	$purchaseitem->item_id = $item['item_id'];
		// 	$purchaseitem->qty = $item['qty'];
		// 	$purchaseitem->GST = $item['GST'];
		// 	$purchaseitem->cost = $item['cost'];
		// 	// $purchaseitem->invoice_no = $invoice_no;
		// 	$purchaseitem->save();

		// 	$purchaseItems[] = $purchaseitem;
		// }

		// Update or create purchase items
        $purchaseItems = [];

        foreach ($input['items'] as $item) {
            $purchaseitem = new PurchaseItem([
				'user_id' => $invoice->id,
                'item_id' => $item['item_id'],
                'qty' => $item['qty'],
                'GST' => $item['GST'],
                'cost' => $item['cost'],
            ]);

            $purchaseItems[] = $purchaseitem;
        }


        $invoice->purchaseItems()->delete(); // Delete existing items
        $invoice->purchaseItems()->saveMany($purchaseItems); // Save new items

        return response()->json([
            "status" => 200,
            "message" => ResponseMessages::getStatusCodeMessages(578), // Update message
            "data" => [
                "invoice" => $invoice,
                "purchaseitems" => $purchaseItems,
            ],
        ], 200);
    } catch (\Exception $ex) {
        
        // Handle exceptions
        return response()->json([
            "status" => 501,
            "message" => ResponseMessages::getStatusCodeMessages(501),
            "data" => null,
        ], 500);
    }
}




	public function InvoiceListOld(Request $request) {
				// check keys are exist
				$this->checkKeys(array_keys($request->all()), array("device_type"));
				try {
	        
				if (($request->device_type == 'android') || ($request->device_type == 'ios') || ($request->device_type == 'web')) {
					if ((isset(Auth::user()->id)) && ($user = User::where("id", Auth::user()->id)->first())) {
						$this->updateUserDevice($user->id, $request->device_id, $request->device_token, $request->device_type);
						
						$invoicelist = User::select('users.id', 'users.name', 'users.phone_code', 'users.mobile', 'purchase_items.cost','purchase_items.invoice_no')
										->join('purchase_items', 'users.id', '=', 'purchase_items.user_id')
										->where("role", "Customer")->get();
		
						// $customerlist = $query->orderBy('id', 'desc')->get();
						
						
						// $customerlist = User::select('users.id', 'users.name', 'users.phone_code', 'users.mobile', 'users.type', 'items.name as item_name')
						// 				->join('purchase_items', 'users.id', '=', 'purchase_items.user_id')
						// 				->join('items', 'purchase_items.item_id', '=', 'items.id')
						// 				// ->where("status", "!=", "delete")
						// 				->where("role", "Customer")
						// 				->get();

						$this->response = array(
							"status" => 200,
							"message" => ResponseMessages::getStatusCodeMessages(575),
							"data" => !empty($invoicelist) ? $invoicelist : null,
						
						);
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(5),
							"data" => null,
						);
					}
				}
				else{
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(214),
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

	public function menuCategoryWiseItems(Request $request){
		$categories 			= 	RestaurantCategory::where('status', 'active')->get();
		$responseData 			= 	[];
		$otherCategoriesData 	= 	[];
		$mainCourseCategoryData = 	[];

		foreach ($categories as $category) {
			$categoryData = [
				'id' 		=> 	$category->id,
				'name' 		=> 	$category->name,
				'image' 	=> 	$category->image,
				'icon' 		=> 	$category->icon,
				'status' 	=> 	$category->status,
			];

			if ($category->subCategories->isNotEmpty()) {
				$subCategories 	= [];
				foreach ($category->subCategories as $subCategory) {
					$subCategoryData = [
						'id' 		=> $subCategory->id,
						'name' 		=> $subCategory->name,
						'status' 	=> $subCategory->status,
						'products' 	=> $subCategory->products->toArray(),
					];
					
					$subCategories[] = $subCategoryData;
				}
				$categoryData['subcategories'] = $subCategories;
			} else {
				$categoryData['products'] = $category->products->toArray();
			}

			$responseData[] = [$category->name => $categoryData];
			
			if ($category->slug === 'main_course') {
				$mainCourseCategoryData = $categoryData;
			} else {
				$otherCategoriesData[] = $categoryData;
			}
		}

		$this->response = [
			'status' => 200,
			'message' => ResponseMessages::getStatusCodeMessages(575),
			'data' => [
				'other' => $otherCategoriesData,
				'main_course' => $mainCourseCategoryData,
			],
		];

		$this->shut_down();
		exit;
	}  
}