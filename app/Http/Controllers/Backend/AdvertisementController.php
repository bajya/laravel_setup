<?php

namespace App\Http\Controllers\Backend;
use App\Library\Helper;
use App\Library\Notify;    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Advertisement;
use App\ChurchImage;
use App\Language;
use App\Ministrie;
use App\Size;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Image;
use URL;
use Zip;
use File;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Exceptions\NoTypeDetectedException;
use App\Family;
use App\Country;
use App\State;
use App\City;
use App\Region;
use App\Library\ResponseMessages;
use Carbon\Carbon;


class AdvertisementController extends Controller {
	public $church;
	public $columns;
	public $logs;

	public function __construct() {
		$this->advertisement = new Advertisement;
		$this->columns = [
			"select","families_id","countries_id", "state_id","cities_id","region_id", "image", "url", "auto_deactive",  "status","created_at", "action"
		];
		/*$this->middleware('permission:church-list|church-create|church-edit|church-delete', ['only' => ['index','store']]);
        $this->middleware('permission:church-create', ['only' => ['create','store']]);
        $this->middleware('permission:church-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:church-delete', ['only' => ['destroy']]);*/
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		$count = $this->advertisement::where('status', '!=', 'delete')->count();
		return view('backend.advertisements.index', compact('count'));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function advertisementsAjax(Request $request) {
		if (isset($request->search['value'])) {
            $request->search = $request->search['value'];
        }else{
            $request->search = $request->searchVal;
        }
		if (isset($request->order[0]['column'])) {
			$request->order_column = $request->order[0]['column'];
			$request->order_dir = $request->order[0]['dir'];
		}
		
		$records = $this->advertisement->fetchAdvertisements($request, $this->columns);
		$count = $records->get();
		if (isset($request->start)) {
			$list = $records->offset($request->start)->limit($request->length)->get();
		} else {
			$list = $records->offset($request->start)->limit(count($count))->get();
		}
		// echo $total;
		$result = [];
		
		$total = count($count);
		// die();
		$i = 1;
		foreach ($list as $cat) {
			$data = [];
			$data['select'] = '<div class="form-check form-check-flat"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="user_id[]" value="' . $cat->id . '"><i class="input-helper"></i></label></div>';
			
			$getfamily = Family::whereIn("id",explode(",",$cat->families_id))->where("status","active")->pluck("name")->toArray();
			$data['family_name'] = "<div class='txtElipes'>". implode(",",$getfamily)  ."</div>" ?? '-</div>';
			
			$getcountry = Country::whereIn("id",explode(",",$cat->countries_id))->where("status","active")->pluck("name")->toArray();
			$data['country_name'] ="<div class='txtElipes'>". implode(",",$getcountry) ."</div>" ?? '-</div>';

			$getState = State::whereIn("id",explode(",",$cat->state_id))->where("status","active")->pluck("name")->toArray();
			$data['state_name'] = "<div class='txtElipes'>".implode(",",$getState) ."</div>" ?? '-</div>';

			$getCity = City::whereIn("id",explode(",",$cat->cities_id))->where("status","active")->pluck("name")->toArray();
			$data['city_name'] ="<div class='txtElipes'>". implode(",",$getCity)  ."</div>" ?? '-</div>';

			$getRegion = Region::whereIn("id",explode(",",$cat->region_id))->where("status","active")->pluck("name")->toArray();
			$data['region_name'] ="<div class='txtElipes'>". implode(",",$getRegion) ."</div>" ?? '-</div>';


			//$data['sno'] = $i++;
			$data['image'] = ($cat->image != null) ? '<img src="'. $cat->image.'" width="70" />' : '-';
			$data['url'] = ($cat->url != null) ? $cat->url : '-';
			$data['auto_deactive'] = ($cat->auto_deactive != null) ? date('d-m-Y H:m:i A', strtotime($cat->auto_deactive)) : '-';
			$data['status'] = ucfirst(config('constants.STATUS.' . $cat->status));
			$data['activate'] = '<div class="bt-switch"><div class="col-md-2"><input type="checkbox"' . ($cat->status == 'active' ? ' checked' : '') . ' data-id="' . $cat->id . '" data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" class="statusAdvertisements"></div></div>';
			
			$data['created_at'] = date('d-m-Y', strtotime($cat->created_at));
			$action = '<div class="actionBtn d-flex align-itemss-center" style="gap:8px">';
			
			if (Helper::checkAccess(route('editAdvertisements'))) {
				$action .= '<a href="' . route('editAdvertisements', ['id' => $cat->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
			}
			$action .= '<a href="' . route('viewAdvertisements', ['id' => $cat->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';
			
			if (Helper::checkAccess(route('deleteAdvertisements'))) {
				
				$action .= '<a href="javascript:;" class="toolTip deleteAdvertisements" data-toggle="tooltip" data-placement="bottom" data-id="' . $cat->id . '" title="Delete"><i class="fa fa-times"></i></a>';
			}
			$action.="</div>";
			$data['action'] = $action;

			$result[] = $data;
		}
		$data = json_encode([
			'data' => $result,
			'recordsTotal' => $total,
			'recordsFiltered' => $total,
		]);
		echo $data;

	}




	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() { 
		$type = 'add';
		$url = route('addAdvertisements');
		$advertisement = new advertisement;
		$familieslist = Family::select('id', 'name')->where('status', 'active')->orderBy('id', 'asc')->get();
		$countrylist = Country::select('id', 'name')->where('status', 'active')->orderBy('id', 'asc')->get();
		
		$sizes = Size::select('id', 'name')->where('status', 'active')->orderBy('id', 'asc')->get();
		
		return view('backend.advertisements.create', compact('type', 'url', 'familieslist', 'countrylist','advertisement'));
	}

	/**
	 * check for unique Church
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function checkChurch(Request $request, $id = null) {
		if (isset($request->church_name)) {
			$check = $this->advertisement::where('name', $request->church_name);
			if (isset($id) && $id != null) {
				$check = $check->where('id', '!=', $id);
			}
			$check = $check->where('status', '!=', 'delete')->count();
			if ($check > 0) {
				return "false";
			} else {
				return "true";
			}

		} else {
			return "true";
		}
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		$input = $request->all();

		$validate = Validator($request->all(), [
			'advertisement_image' => 'mimes:jpeg,png,jpg,gif,svg',
			'families_id' => 'required',
			'countries_id' => 'required',
			'states_id' => 'required',
			'cities_id' =>'required',
			'region_id' =>'required',
			'auto_deactive'=>'required'
		]);

		$attr = [
			'url' => 'Url',
			'advertisement_image' => 'Please Select Image',
			'families_id' => 'Please Select Family',
			'countries_id' => 'Please Select Country',
			'states_id' => 'Please Select State',
			'cities_id' => 'Please Select City',
			'region_id'=>'Please Select Region',
			'auto_deactive'=>"Please Select Aauto Deactive"
		];

		$validate->setAttributeNames($attr);

		if ($validate->fails()) {
			return redirect()->back()->withInput($request->all())->withErrors($validate);
		} else {
			try {
				if(!File::exists(public_path().'/uploads/advertisements')) {
		            File::makeDirectory(public_path().'/uploads/advertisements', 0777, true, true);
		        }

				$imageName = '';
				if ($request->file('advertisement_image') != null) {
					$image = $request->file('advertisement_image');
					$imageName = time() . $image->getClientOriginalName();
					$imageName = str_replace(' ', '', $imageName);
					$imageName = str_replace('.jpeg', '.jpg', $imageName);
					$image->move(public_path('uploads/advertisements'), $imageName);
					//Helper::compress_image(public_path('uploads/churchs/' . $imageName), 100);
					$imageName = str_replace('.jpeg', '.jpg', $imageName);
				}
				$advertisement = new $this->advertisement;
				$advertisement->url = $request->post('url');
				$advertisement->status = trim($request->post('status'));
				$advertisement->families_id = implode(",",$request->post('families_id'));
				$advertisement->countries_id = implode(",",$request->post('countries_id'));
				$advertisement->state_id = implode(",",$request->post('states_id'));
				$advertisement->cities_id = implode(",",$request->post('cities_id'));
				$advertisement->region_id = implode(",",$request->post('region_id'));
				$advertisement->auto_deactive = Carbon::createFromFormat('d/m/Y H:i:s', $input['auto_deactive']);
				$advertisement->image = str_replace('.jpeg', '.jpg', $imageName);
				$advertisement->created_at = date('Y-m-d H:i:s');



				if ($advertisement->save()) {
					$request->session()->flash('success', 'Advertisement added successfully');
					return redirect()->route('advertisements');
				} else {
					$request->session()->flash('error', 'Something went wrong. Please try again later.');
					return redirect()->back();
				}
			} catch (Exception $e) {
				$request->session()->flash('error', 'Something went wrong. Please try again later.');
				return redirect()->back();
			}

		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(Request $request, $id = null) {
		if (isset($id) && $id != null) {
			$type = 'Show';
			$advertisement = $this->advertisement::where('id', $id)->first();
			if (isset($advertisement->id)) {
				$getfamily = Family::whereIn("id",explode(",",$advertisement->families_id))->where("status","active")->pluck("name")->toArray();
				$getCountry = Country::whereIn("id",explode(",",$advertisement->countries_id))->where("status","active")->pluck("name")->toArray();
				$getState = State::whereIn("id",explode(",",$advertisement->state_id))->where("status","active")->pluck("name")->toArray();
				$getCity = City::whereIn("id",explode(",",$advertisement->cities_id))->where("status","active")->pluck("name")->toArray();
				$getRegion = Region::whereIn("id",explode(",",$advertisement->region_id))->where("status","active")->pluck("name")->toArray();
				
				$advertisement->auto_deactive =  date('d-m-Y H:m:i A',strtotime($advertisement->auto_deactive)) ?? '-';

				$advertisement->family_name = implode(",",$getfamily) ?? '-';
				$advertisement->country_name = implode(",",$getCountry) ?? '-';
				$advertisement->state_name = implode(",",$getState) ?? '-';
				$advertisement->city_name = implode(",",$getCity) ?? '-';
				$advertisement->region_name = implode(",",$getRegion) ?? '-';
				return view('backend.advertisements.view', compact('advertisement', 'type'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('advertisements');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('advertisements');
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Request $request, $id = null) {
		if (isset($id) && $id != null) {
            $advertisement = $this->advertisement::where('id', $id)->first();
            if (isset($advertisement->id)) {
            	$familieslist = Family::select('id', 'name')->where('status', 'active')->orderBy('id', 'asc')->get();
            	$countrylist = Country::select("id","name")->where('status', 'active')->orderBy('id', 'asc')->get();
            	
            	$stateList = State::select("id","name")->where("status","active")->whereIn("country_id",explode(",",$advertisement->countries_id))->get();
            	$cityList = City::select("id","name")->where("status","active")->whereIn("state_id",explode(",",$advertisement->state_id))->get();
            	$regionList = Region::select("id","name")->where("status","active")->whereIn("city_id",explode(",",$advertisement->cities_id))->get();
                $type = 'edit';
                $advertisement->auto_deactive =  date('d/m/Y H:m:i',strtotime($advertisement->auto_deactive)) ?? '';

                $url = route('updateAdvertisements', ['id' => $advertisement->id]);
                return view('backend.advertisements.create', compact('advertisement', 'type', 'url','familieslist','countrylist','stateList','cityList','regionList'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('advertisements');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('advertisements');
        }
	}
	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id = null) {
		$input = $request->all();
	
		if (isset($id) && $id != null) {
			$advertisement = $this->advertisement::where('id', $id)->first();
			if (isset($advertisement->id)) {
				$validate = Validator($request->all(), [
					'advertisement_image' => 'mimes:jpeg,png,jpg,gif,svg',
					'families_id' => 'required',
					'countries_id' => 'required',
					'states_id' => 'required',
					'cities_id' =>'required',
					'region_id' =>'required',
					'auto_deactive'=>'required'
				]);

			$attr = [
				'url' => 'Url',
				'advertisement_image' => 'Please Select Image',
				'families_id' => 'Please Select Family',
				'countries_id' => 'Please Select Country',
				'states_id' => 'Please Select State',
				'cities_id' => 'Please Select City',
				'region_id'=>'Please Select Region',
				'auto_deactive'=>"Please Select Aauto Deactive"
			];

				$validate->setAttributeNames($attr);

				if ($validate->fails()) {
					return redirect()->back()->withInput($request->all())->withErrors($validate);
				} else {
					try {
						if(!File::exists(public_path().'/uploads/advertisements')) {
				            File::makeDirectory(public_path().'/uploads/advertisements', 0777, true, true);
				        }
						$imageName = '';
						if ($request->file('advertisement_image') != null) {
							$image = $request->file('advertisement_image');
							$imageName = time() . $image->getClientOriginalName();
							if ($advertisement->image != null && file_exists(public_path('uploads/advertisements/' . $advertisement->image))) {
								if ($advertisement->image != 'noimage.jpg') {
									unlink(public_path('uploads/advertisements/' . $advertisement->image));
								}
							}
							$imageName = str_replace(' ', '', $imageName);
							$imageName = str_replace('.jpeg', '.jpg', $imageName);
							$image->move(public_path('uploads/advertisements'), $imageName);
							//Helper::compress_image(public_path('uploads/advertisements/' . $imageName), 100);
							$advertisement->image = str_replace('.jpeg', '.jpg', $imageName);
						}
						$advertisement->url = $request->post('url');
						$advertisement->status = trim($request->post('status'));
						$advertisement->families_id = implode(",",$request->post('families_id'));
						$advertisement->countries_id = implode(",",$request->post('countries_id'));
						$advertisement->state_id = implode(",",$request->post('states_id'));
						$advertisement->cities_id = implode(",",$request->post('cities_id'));
						$advertisement->region_id = implode(",",$request->post('region_id'));
						$advertisement->auto_deactive = Carbon::createFromFormat('d/m/Y H:i:s', $input['auto_deactive']);
					
					
						if ($advertisement->save()) {
							
							$request->session()->flash('success', 'Advertisement updated successfully');
							return redirect()->route('advertisements');
						} else {
							$request->session()->flash('error', 'Something went wrong. Please try again later.');
							return redirect()->route('advertisements');
						}
					} catch (Exception $e) {
						$request->session()->flash('error', 'Something went wrong. Please try again later.');
						return redirect()->route('advertisements');
					}

				}
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('advertisements');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('advertisements');
		}

	}

	// activate/deactivate Advertisements
	public function updateStatus(Request $request) {

		if (isset($request->statusid) && $request->statusid != null) {
			$church = $this->advertisement::find($request->statusid);

			if (isset($church->id)) {
				$church->status = $request->status;
				if ($church->save()) {
					$request->session()->flash('success', 'Advertisements updated successfully.');
					return redirect()->back();
				} else {
					$request->session()->flash('error', 'Unable to update Advertisements. Please try again later.');
					return redirect()->back();
				}
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->back();
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->back();
		}

	}

	// activate/deactivate Advertisements
	public function updateStatusAjax(Request $request) {

		if (isset($request->statusid) && $request->statusid != null) {
			$church = $this->advertisement::find($request->statusid);

			if (isset($church->id)) {
				$church->status = $request->status;
				if ($church->save()) {
					echo json_encode(['status' => 1, 'message' => 'Advertisements updated successfully.']);
				} else {
					echo json_encode(['status' => 0, 'message' => 'Unable to update Advertisements. Please try again later.']);
				}
			} else {
				echo json_encode(['status' => 0, 'message' => 'Invalid Advertisements']);
			}
		} else {
			echo json_encode(['status' => 0, 'message' => 'Invalid Advertisements']);
		}

	}

	public function deleteItems($root, $level) {
		$child = $root->childCat;
		foreach ($child as $ch) {
			$ch->status = 'delete';
			$ch->save();
			$this->deleteItems($ch, ++$level);
		}
		$root = $child;
		return true;
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \Illuminate\Http\Request
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request) {
		if (isset($request->deleteid) && $request->deleteid != null) {
			$church = $this->advertisement::find($request->deleteid);

			if (isset($church->id)) {
				$church->status = 'delete';
				if ($church->save()) {

					//$this->deleteItems($church, 1);

					echo json_encode(['status' => 1, 'message' => 'advertisements deleted successfully.']);
				} else {
					echo json_encode(['status' => 0, 'message' => 'Unable to delete advertisements. Please try again later.']);
				}
			} else {
				echo json_encode(['status' => 0, 'message' => 'Invalid advertisements']);
			}
		} else {
			echo json_encode(['status' => 0, 'message' => 'Invalid advertisements']);
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \Illuminate\Http\Request
	 * @return \Illuminate\Http\Response
	 */
	public function destroyImageDelete(Request $request) {
		if (isset($request->deleteid) && $request->deleteid != null) {
			$church = ChurchImage::find($request->deleteid);

			if (isset($church->id)) {
				$church->status = 'delete';
				if ($church->save()) {

					//$this->deleteItems($church, 1);

					echo json_encode(['status' => 1, 'message' => 'advertisements Image deleted successfully.']);
				} else {
					echo json_encode(['status' => 0, 'message' => 'Unable to delete advertisements Image. Please try again later.']);
				}
			} else {
				echo json_encode(['status' => 0, 'message' => 'Invalid advertisements Image']);
			}
		} else {
			echo json_encode(['status' => 0, 'message' => 'Invalid advertisements Image']);
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
				$advertisement = $this->advertisement::find($id);

				if (isset($advertisement->id)) {
					$advertisement->status = 'delete';
					if ($advertisement->save()) {
						$count++;
					}
				}
			}
			if ($count == $ids) {
				echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Advertisements deleted successfully.']);
			} else {
				echo json_encode(["status" => 0, 'message' => 'Not all Advertisements were deleted. Please try again later.']);
			}
		} else {
			echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
		}
	}
	/**
	 * activate/deactivate multiple resource from storage.
	 *
	 * @param  \Illuminate\Http\Request
	 * @return \Illuminate\Http\Response
	 */
	public function bulkchangeStatus(Request $request) {

		if (isset($request->ids) && $request->ids != null) {
			$ids = count($request->ids);
			$count = 0;
			foreach ($request->ids as $id) {
				$church = $this->advertisement::find($id);

				if (isset($church->id)) {
					if ($church->status == 'active') {
						$church->status = 'inactive';
					} elseif ($church->status == 'inactive') {
						$church->status = 'active';
					}

					if ($church->save()) {
						$count++;
					}
				}
			}
			if ($count == $ids) {
				echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Advertisements updated successfully.']);
			} else {
				echo json_encode(["status" => 0, 'message' => 'Not all Advertisements were updated. Please try again later.']);
			}
		} else {
			echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
		}
	}
 
   	public function clears()
    {
        return view('backend.clear_record');
    }
    
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
               
                
           
                if ($request->type == 'all') {
                	DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                	
                	DB::table('otp')->truncate();
                	
                	
                	DB::table('users')->where('id', '!=', 1)->delete();
                	DB::statement('SET FOREIGN_KEY_CHECKS=1;'); 
	                session()->flash('success','All record successfully clear');
	                return redirect()->route('clears');
                }else{
                	DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                	
                	DB::statement('SET FOREIGN_KEY_CHECKS=1;'); 
	                session()->flash('success','Dummy related record successfully clear');
	                return redirect()->route('clears');
                }
	        }
        } catch (Exception $e) {
            $request->session()->flash('error', 'Something went wrong. Please try again later.');
            return redirect()->back();
        }
    }

    public function getStatelistByCountryId(Request $request){
    	if($request->selectedValues){
    	try{
    		$data = State::whereIn("country_id",$request->selectedValues)->where("status","active")->get(['id',"name"]);
	     	if ($data->count() > 0) {
	     		
	     		$response['status'] = true;
	     		$response['data'] = $data;
	     	} else {
	     		$response['status'] =  true;
	     		$response['data'] = null;
			}
			}catch (\Exception $ex) {
				$response['status'] =  false;
	     		$response['data'] = null;
			}
		}
		else{
				$response['status'] =  false;
     		$response['data'] = null;
	
		}
		return response()->json($response);
    }

    public function getCitylistByStateId(Request $request){
    	if($request->selectedValues){
 		
 		try{
    		$data = City::whereIn("state_id",$request->selectedValues)->where("status","active")->get(['id',"name"]);
	     	if ($data->count() > 0) {
	     		
	     		$response['status'] = true;
	     		$response['data'] = $data;
	     	} else {
	     		$response['status'] =  true;
	     		$response['data'] = null;
			}
			}catch (\Exception $ex) {
				$response['status'] =  false;
	     		$response['data'] = null;
			}
			return response()->json($response);	
    	}
    }

    public function getRegionlistByCityId(Request $request){
    	if($request->selectedValues){
	 		try{
	    		$data = Region::whereIn("city_id",$request->selectedValues)->where("status","active")->get(['id',"name"]);
		     	if ($data->count() > 0) {
		     		
		     		$response['status'] = true;
		     		$response['data'] = $data;
		     	} else {
		     		$response['status'] =  true;
		     		$response['data'] = null;
				}
			}catch (\Exception $ex) {
				$response['status'] =  false;
	     		$response['data'] = null;
			}
			return response()->json($response);	
    	}
    }

    
}
