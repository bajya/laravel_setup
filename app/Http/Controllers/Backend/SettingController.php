<?php

namespace App\Http\Controllers\Backend;
use App\Library\Helper;
use App\Library\Notify;    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\BusRuleRef;
use App\User;
use App\Message;
use App\Notificationuser;
use App\CMS;
use App\Push;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Image;
use URL; 
use Illuminate\Support\Arr;

class SettingController extends Controller {


	public function __construct() {
		
		/*$this->middleware('permission:setting-list', ['only' => ['index','store']]);*/
	}
	/**
	 * Setting Modual Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		$settings = BusRuleRef::select('id', 'type', 'name', 'rule_value')->where('sts_cd', 'AC')->get();
		return view('backend.settings.create', compact('settings'));
	}

	/**
	 * Setting Modual Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() { 

	}



	/**
	 * Setting Modual Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {

		
	}

	/**
	 * Setting Modual Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(Request $request, $id = null) {
		
	}

	/**
	 * Setting Modual Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Request $request, $id = null) {
		
	}



	/**
	 * Setting Modual Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request) {
		$validate = Validator($request->all(), [
			'total_val' => 'required',
		]);
		$attr = [
			'total_val' => 'Input',
		];
		$validate->setAttributeNames($attr);
		if ($validate->fails()) {
			return redirect()->route('createSetting')->withInput($request->all())->withErrors($validate);
		} else {
			try {
				$data = $request->all();

				for ($i=0; $i < $request->total_val ; $i++) { 
                    $id = "id_" . $i;
                    $setting = "value_" . $i;
                    if ($res = BusRuleRef::where('id', $data[$id])->first()) {
                        $res->rule_value = $data[$setting];
                        $res->save();
                    }
                }
				$request->session()->flash('success', 'Setting updated successfully');
				return redirect()->route('settings');
			} catch (Exception $e) {
				$request->session()->flash('error', 'Something went wrong. Please try again later.');
				return redirect()->route('settings');
			}
		}
	}
	/**
	 * Clear Modual Show the specified resource clear form.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
   	public function clears()
    {
        return view('backend.clear_record');
    }
	/**
	 * Clear Modual Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */    
    public function clearRecord(Request $request){
    	try {
	        $validate = Validator($request->all(), [
	            'type' => 'required',
	        ]);
	        $attr = [
	            'type' => 'Type',
	        ];
	        $validate->setAttributeNames($attr);

	        if ($validate->fails()) {
	        	$request->session()->flash('error', 'Something went wrong. Please try again later.');
	            return redirect()->route('clears')->withInput($request->all())->withErrors($validate);
	        } else {
                $path_articles = public_path('uploads/articles');
	        	$files_articles = \File::allFiles($path_articles);
	        	if (!empty($files_articles)) {
                	foreach ($files_articles as $key_articles => $value_articles) {
                		if ($value_articles->getFilename() != null && file_exists(public_path('uploads/articles/' . $value_articles->getFilename()))) {
                			if ($value_articles->getFilename() != 'noimage.jpg') {
                                unlink(public_path('uploads/articles/' . $value_articles->getFilename()));
                            }
                        }
                	}
                }

                $path_churchs = public_path('uploads/churchs');
	        	$files_churchs = \File::allFiles($path_churchs);
	        	if (!empty($files_churchs)) {
                	foreach ($files_churchs as $key_churchs => $value_churchs) {
                		if ($value_churchs->getFilename() != null && file_exists(public_path('uploads/churchs/' . $value_churchs->getFilename()))) {
                			if ($value_churchs->getFilename() != 'noimage.jpg') {
                                unlink(public_path('uploads/churchs/' . $value_churchs->getFilename()));
                            }
                        }
                	}
                }
                if ($request->type == 'all') {
                	$path_splashscreens = public_path('uploads/splashscreens');
		        	$files_splashscreens = \File::allFiles($path_splashscreens);
		        	if (!empty($files_splashscreens)) {
	                	foreach ($files_splashscreens as $key_splashscreens => $value_splashscreens) {
	                		if ($value_splashscreens->getFilename() != null && file_exists(public_path('uploads/splashscreens/' . $value_splashscreens->getFilename()))) {
	                			if ($value_splashscreens->getFilename() != 'noimage.jpg') {
	                                unlink(public_path('uploads/splashscreens/' . $value_splashscreens->getFilename()));
	                            }
	                        }
	                	}
	                }
                	DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                	DB::table('notification_users')->truncate();
                	DB::table('otp')->truncate();
                	DB::table('pushs')->truncate();
                	DB::table('churches')->truncate();
                	DB::table('church_images')->truncate();
                	DB::table('articles')->truncate();
                	DB::table('article_images')->truncate();
                	DB::table('languages')->truncate();
                	DB::table('splash_screens')->truncate();
                	DB::table('push_user')->truncate();
                	DB::table('users')->where('id', '!=', 1)->delete();
                	DB::statement('SET FOREIGN_KEY_CHECKS=1;'); 
	                session()->flash('success','All record successfully clear');
	                return redirect()->route('clears');
                }else{
                	DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                	DB::table('churches')->truncate();
                	DB::table('church_images')->truncate();
                	DB::table('articles')->truncate();
                	DB::table('article_images')->truncate();
                	DB::statement('SET FOREIGN_KEY_CHECKS=1;'); 

	                session()->flash('success','Church and Article related record successfully clear');
	                return redirect()->route('clears');
                }
	        }
        } catch (Exception $e) {
            $request->session()->flash('error', 'Something went wrong. Please try again later.');
            return redirect()->back();
        }
    }

}
