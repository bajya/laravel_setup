<?php
    
namespace App\Http\Controllers\Backend;
    
use App\Library\Helper;
use App\Library\Notify;    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\AdminSettings;
use App\BusRuleRef;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use URL;
use Illuminate\Support\Arr;
use App\Country;   
use App\EmailTemplate; 
use Mail;
use App\Transaction;
use App\State;
use App\City;
use App\Region;
use App\BoxPurchase;
use Illuminate\Validation\Rule;

class AdminSettingController extends Controller
{
    public $adminsetting;
    public $columns;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->adminsetting = new AdminSettings;
       
        $this->columns = [
            "select", "name","last_name", "email","created_at", "activate", "action",
        ];
    }

    public function index(Request $request) {
        $adminsetting = $this->adminsetting::first();
        // dd($adminsetting);
        return view('backend.adminsetting.create', compact('adminsetting'));
    }

    public function create() {
    
    }

    public function store(Request $request) {
       
    }

    public function show(Request $request, $id = null) {
       
    }

    public function edit(Request $request, $id = null) {
       
    }

    public function update(Request $request) {
        	$input = $request->all();
            $adminsetting = $this->adminsetting::first();

			if (isset($adminsetting)) {
				$validate = Validator($request->all(), [
					
				]);

                $attr = [
                
                ];

				$validate->setAttributeNames($attr);

				if ($validate->fails()) {
					return redirect()->back()->withInput($request->all())->withErrors($validate);
				} else {
					try {
						$adminsetting->total_user = $request->total_user;
                        $adminsetting->total_happy_client = $request->total_happy_client;
                        $adminsetting->total_review = $request->total_review;
                        $adminsetting->total_app_download = $request->total_app_download;
                        $adminsetting->about_app_description = $request->about_app_description;
                        $adminsetting->subscription_description = $request->subscription_description;
                        $adminsetting->testimonial_description = $request->testimonial_description;
                        $adminsetting->footer_description = $request->footer_description;
                        $adminsetting->facebook_url = $request->facebook_url;
                        $adminsetting->twitter_url = $request->twitter_url;
                        $adminsetting->instagram_url = $request->instagram_url;
                        $adminsetting->linkedin_url = $request->linkedin_url;
                        $adminsetting->whatsup_url = $request->whatsup_url;
                        $adminsetting->play_store_url = $request->play_store_url;
                        $adminsetting->app_store_url = $request->app_store_url;
                        $adminsetting->address = $request->address;
                        $adminsetting->latitude = $request->latitude;
                        $adminsetting->longitude = $request->longitude;

						if ($adminsetting->save()) {
							
							$request->session()->flash('success', 'Admin Setting updated successfully');
							return redirect()->route('adminsettings');
						} else {
							$request->session()->flash('error', 'Something went wrong. Please try again later.');
							return redirect()->route('adminsettings');
						}
					} catch (Exception $e) {
						$request->session()->flash('error', 'Something went wrong. Please try again later.');
						return redirect()->route('adminsettings');
					}

				}
			} else {
				$request->session()->flash('error', 'Invalid vdfd Data');
				return redirect()->route('adminsettings');
			}

    }
    // activate/deactivate user
    public function updateStatus(Request $request) {

      

    }

    // activate/deactivate user
    public function updateStatusAjax(Request $request) {

    }
    public function destroy(Request $request) {
       
    }
    public function bulkdelete(Request $request) {

        
    }
    public function bulkchangeStatus(Request $request) {

      
    }

 

}