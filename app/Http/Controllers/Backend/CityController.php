<?php

namespace App\Http\Controllers\Backend;
use App\Library\Helper;
use App\Library\Notify;    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\City;


use Spatie\Permission\Models\Role;
use DB;
use Hash;
use App\State;
use App\Region;
use App\Country;
use Illuminate\Support\Arr;

class CityController extends Controller {
	public $cities;
	public $columns;

	public function __construct() {
		$this->cities = new City;
		$this->columns = [
			"select","country_id","state_id", "name","created_at",'activate', "action",
		];

		$this->middleware('permission:cities-list|cities-edit', ['only' => ['index','update']]);
        $this->middleware('permission:cities-edit', ['only' => ['edit','update']]);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		// $getStateId = City::select("state_id")->groupby("state_id")->pluck("state_id")->toArray();
		// $getCountryId = State::whereIn("id",$getStateId)->select("country_id","id")->get()->toArray();
		// if($getCountryId){
		// 	foreach($getCountryId as $k=> $r){
		// 		City::where("state_id",$r['id'])->update(['country_id'=>$r['country_id']]);
		// 	}
		// }
		// die;
		return view('backend.cities.index');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function citiesAjax(Request $request) {
		if (isset($request->search['value'])) {
            $request->search = $request->search['value'];
        }else{
            $request->search = $request->search ?? '';
        }
		if (isset($request->order[0]['column'])) {
			$request->order_column = $request->order[0]['column'];
			$request->order_dir = $request->order[0]['dir'];
		}
		$records = $this->cities->fetchCity($request, $this->columns);
		$total = $records->get();
		if (isset($request->start)) {
			$cities = $records->offset($request->start)->limit($request->length)->get();
		} else {
			$cities = $records->offset($request->start)->limit(count($total))->get();
		}
		// echo $total;
		$result = [];
		$i = 1;
		foreach ($cities as $list) {
			$data = [];
			$data['sno'] = $i++;
			$data['country_name']= $list->getCountry->name ?? '';
			$data['state_name']= $list->getState->name ?? '';
		   	$data['select'] = '<div class="form-check form-check-flat"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="cities_id[]" value="' . $list->id . '"><i class="input-helper"></i></label></div>';
			
			$data['name'] = $list->name;
			$data['activate'] = '<div class="bt-switch"><div class="col-md-2"><input type="checkbox"' . ($list->status == 'active' ? ' checked' : '') . ' data-id="' . $list->id . '" data-code="' . $list->name . '" data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" class="statusCities"></div></div>';

			$data['created_at'] =  date('d-m-Y', strtotime($list->created_at));

			$action = '<div class="actionBtn d-flex align-itemss-center" style="gap:8px">';

			if (Helper::checkAccess(route('editCities'))) {
				$action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('editCities', ['id' => $list->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
			}

		  //$action .= '&nbsp;&nbsp;&nbsp;<a href="javascript:;" class="toolTip deleteCities" data-toggle="tooltip" data-id="' . $list->id . '" data-placement="bottom" title="Delete"><i class="fa fa-times"></i></a>';
			$action.="</div>";
			$data['action'] = $action;
	
			$result[] = $data;
		}
		$data = json_encode([
			'data' => $result,
			'recordsTotal' => count($total),
			'recordsFiltered' => count($total),
		]);
		echo $data;

	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		$type = 'Add';
		$url = route('storeCities');
		$statelist =null;//State::where("status",'active')->get();
		$cities =null;
		$countrylist = Country::where("status","active")->get();

		// dd($countrylist);
		
        return view('backend.cities.create', compact('type','statelist','cities','url','countrylist'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		$validate = Validator($request->all(), [
			'country_id' => 'required',
			'state_id' => 'required',
			'city_name' => 'required',
			
			
		]);
		$attr = [
			'country_id' => 'Select Country',
			'state_id' => 'Select State',
			'city_name' => 'City Name',
		
		];

		$validate->setAttributeNames($attr);

		if ($validate->fails()) {
			return redirect()->back()->withInput($request->all())->withErrors($validate);
		} else {
			  try {

				$cities = new City();
				$cities->country_id = $request->country_id;
				$cities->state_id = $request->state_id;
				$cities->name = $request->city_name;
				if ($cities->save()) {
                    $request->session()->flash('success', 'City added successfully');
                    return redirect()->route('cities');
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
		

			$statelist =State::where("status",'active')->get();
			$cities =City::where('id', $id)->first();

			if (isset($cities->id)) {
				return view('backend.cities.view', compact('cities'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('cities');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('cities');
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
			$url=	route('updateCities',['id'=>$id ?? '']);
			$statelist = State::where("status","active")->get();
			$countrylist = Country::where("status","active")->get();
			$cities = City::where('id', $id)->first();
			if (isset($cities->id)) {
				$type = 'Edit';
				return view('backend.cities.create', compact('cities', 'type','url','statelist','countrylist'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('cities');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('cities');
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
	
	
		if (isset($id) && $id != null) {

			$cities = City::where('id', $id)->first();

				if (isset($cities->id)) {
				try {
					$cities->country_id = $request->country_id;
					$cities->name = $request->city_name;
					$cities->state_id = $request->state_id;
					$cities->save();
					$request->session()->flash('success', 'City updated successfully');
					return redirect()->route('cities');

				} catch (Exception $e) {
					$request->session()->flash('error', 'Something went wrong. Please try again later.');
					return redirect()->route('cities');
				}

			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('cities');
			}
			
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('cities');
		}
	}

	

	public function checkCitiesName(Request  $request ,$id = null){
		 	
      	 if (isset($request->city_name)) {
		    $check = City::where('name', $request->city_name)->where("status","!=","delete");
            if (isset($id) && $id != null) {
                $check = $check->where('id', '!=', $id);
            }
            $check = $check->count();
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
	 * Remove the specified FQA from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */

	 public function destroy(Request $request) {
        if (isset($request->deleteid) && $request->deleteid != null) {
            $countries = Country::find($request->deleteid);

            if (isset($countries->id)) {
                $countries->status = 'delete';
                if ($countries->save()) {
                 
                  $getState =  State::where("country_id",$countries->id)->update(['status'=>'delete']);
                 

                    echo json_encode(["status" => 1, 'ids' => json_encode($request->deleteid), 'message' => 'Country deleted successfully.']);
                } else {
                    echo json_encode(["status" => 0, 'message' => 'Not all countries were deleted. Please try again later.']);
                }
            } else {
                echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
            }
        } else {
            echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
        }
    }

    public function bulkdelete(Request $request) {

        if (isset($request->ids) && $request->ids != null) {
            $ids = count($request->ids);
            $count = 0;
            foreach ($request->ids as $id) {
                $user = Country::find($id);

                if (isset($user->id)) {
                    $user->status = 'delete';
                    if ($user->save()) {
                    	State::where("country_id",$user->id)->update(['status'=>'delete']);
                        $count++;
                    }
                }
            }
            if ($count == $ids) {
                echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Country deleted successfully.']);
            } else {
                echo json_encode(["status" => 0, 'message' => 'Not all countries were deleted. Please try again later.']);
            }
        } else {
            echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
        }
    }

	public  function bulkchangeStatus(Request $request){
		  if (isset($request->ids) && $request->ids != null) {
            $ids = count($request->ids);
            $count = 0;
            foreach ($request->ids as $id) {
                $cities = City::find($id);

                if (isset($cities->id)) {
                    if ($cities->status == 'active') {
                        $cities->status = 'inactive';
                    } elseif ($cities->status == 'inactive') {
                        $cities->status = 'active';
                    }
                    if ($cities->save()) {
                    	$count++;
                    }
                }
            }
            if ($count == $ids) {
                echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'City status updated successfully.']);
            } else {
                echo json_encode(["status" => 0, 'message' => 'Not all city were updated. Please try again later.']);
            }
        } else {
            echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
        }
	}

 	// activate/deactivate Country
    public function updateStatusAjax(Request $request) {

        if (isset($request->statusid) && $request->statusid != null) {
            $cities = City::find($request->statusid);

            if (isset($cities->id)) {
                $cities->status = $request->status;
                if ($cities->save()) {
                	Region::where(['city_id'=>$cities->id])
                	->update(['status'=>$cities->status]);
                    echo json_encode(['status' => 1, 'message' => 'City status updated successfully.']);
                } else {
                    echo json_encode(['status' => 0, 'message' => 'Unable to update city. Please try again later.']);
                }
            } else {
                echo json_encode(['status' => 0, 'message' => 'Invalid data']);
            }
        } else {
            echo json_encode(['status' => 0, 'message' => 'Invalid data']);
        }
    }
}
