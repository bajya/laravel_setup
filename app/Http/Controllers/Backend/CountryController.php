<?php

namespace App\Http\Controllers\Backend;
use App\Library\Helper;
use App\Library\Notify;    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Country;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use App\State;
use App\City;
use App\Region;

use Illuminate\Support\Arr;

class CountryController extends Controller {
	public $countries;
	public $columns;

	public function __construct() {
		$this->countries = new Country;
		$this->columns = [
			"select", "name","created_at",'name', "action",
		];

		$this->middleware('permission:country-list|country-edit', ['only' => ['index','update']]);
        $this->middleware('permission:country-edit', ['only' => ['edit','update']]);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		return view('backend.countries.index');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function countryAjax(Request $request) {
		if (isset($request->search['value'])) {
            $request->search = $request->search['value'];
        }else{
            $request->search = $request->search ?? '';
        }
		if (isset($request->order[0]['column'])) {
			$request->order_column = $request->order[0]['column'];
			$request->order_dir = $request->order[0]['dir'];
		}
		$records = $this->countries->fetchCountry($request, $this->columns);
		$total = $records->get();
		if (isset($request->start)) {
			$countries = $records->offset($request->start)->limit($request->length)->get();
		} else {
			$countries = $records->offset($request->start)->limit(count($total))->get();
		}
		// echo $total;
		$result = [];
		$i = 1;
		foreach ($countries as $list) {
			$data = [];
			$data['sno'] = $i++;
		    $data['select'] = '<div class="form-check form-check-flat"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="country_id[]" value="' . $list->id . '"><i class="input-helper"></i></label></div>';
			$data['shortname'] = $list->shortname;
			$data['name'] = $list->name;
			$data['phonecode'] = $list->phonecode;
			// $data['status'] = ucfirst(config('constants.STATUS.' . $list->status));
            $data['activate'] = '<div class="bt-switch"><div class="col-md-2"><input type="checkbox"' . ($list->status == 'active' ? ' checked' : '') . ' data-id="' . $list->id . '" data-code="' . $list->name . '" data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" class="statusCountry"></div></div>';

			$data['created_at'] =  date('d-m-Y', strtotime($list->created_at));

			$action = '<div class="actionBtn d-flex align-itemss-center" style="gap:8px">';

			if (Helper::checkAccess(route('editCountries'))) {
				$action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('editCountries', ['id' => $list->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
			}
		 // $action .= '&nbsp;&nbsp;&nbsp;<a href="javascript:;" class="toolTip deleteCounties" data-toggle="tooltip" data-id="' . $list->id . '" data-placement="bottom" title="Delete"><i class="fa fa-times"></i></a>';
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
		$url = route('storeCountries');
		$countries =null;
        return view('backend.countries.create', compact('type','countries','url'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		$validate = Validator($request->all(), [
			'country_name' => 'required',
			'country_shortname' => 'required',
			'country_code' => 'required|min:2|numeric',
		]);
		$attr = [
			'country_name' => 'Country Name',
			'country_shortname' => 'Country Short Name',
			'country_code' => 'Country Code',
		];

		$validate->setAttributeNames($attr);

		if ($validate->fails()) {
			return redirect()->back()->withInput($request->all())->withErrors($validate);
		} else {
			  try {

				$countries = new Country();
				$countries->shortname = $request->country_shortname;
				$countries->name = $request->country_name;
				$countries->phonecode = $request->country_code;
			
				if ($countries->save()) {
                    $request->session()->flash('success', 'Country added successfully');
                    return redirect()->route('countries');
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
			$countries = $this->countries::where('id', $id)->first();
			if (isset($countries->id)) {
				return view('backend.countries.view', compact('countries'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('countries');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('countries');
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
			$url=	route('updateCountries',['id'=>$id ?? '']);

			$countries = $this->countries::where('id', $id)->first();
			if (isset($countries->id)) {
				$type = 'Edit';
				return view('backend.countries.create', compact('countries', 'type','url'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('countries');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('countries');
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

			$countries = $this->countries::where('id', $id)->first();

				if (isset($countries->id)) {
				try {
					$countries->name = $request->country_name;
					$countries->shortname = $request->country_shortname;
					$countries->phonecode = $request->country_code;
					$countries->save();
					$request->session()->flash('success', 'Country updated successfully');
					return redirect()->route('countries');

				} catch (Exception $e) {
					$request->session()->flash('error', 'Something went wrong. Please try again later.');
					return redirect()->route('countries');
				}

			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('countries');
			}
			
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('countries');
		}

	}

	

	public function checkCountryName(Request  $request ,$id = null){
		 	
      	 if (isset($request->country_name)) {
		    $check = $this->countries::where('name', $request->country_name)->where("status","!=","delete");
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
                 
                    State::where("country_id",$countries->id)->update(['status'=>'delete']);
                 	$getStateId = State::where("country_id",$countries->id)->pluck("id")->toArray();
        			City::whereIn("state_id",$getStateId)->update(['status'=>'delete']);

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
                $countries = Country::find($id);

                if (isset($countries->id)) {
                    $countries->status = 'delete';
                    if ($countries->save()) {
                    State::where("country_id",$countries->id)->update(['status'=>'delete']);
            		$getStateId = State::where("country_id",$countries->id)->pluck("id")->toArray();
        			City::whereIn("state_id",$getStateId)->update(['status'=>'delete']);
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
                $countries = Country::find($id);

                if (isset($countries->id)) {
                    if ($countries->status == 'active') {
                        $countries->status = 'inactive';
                    } elseif ($countries->status == 'inactive') {
                        $countries->status = 'active';
                    }

                    if ($countries->save()) {
                    	  State::where("country_id",$countries->id)->update(['status'=>$countries->status]);
                		 $getStateId = State::where("country_id",$countries->id)->pluck("id")->toArray();
            			 City::whereIn("state_id",$getStateId)->update(['status'=>$countries->status]);
        			  	$getCityId = City::whereIn("state_id",$getStateId)->pluck("id")->toArray();
                       	 Region::whereIn("city_id",$getCityId)->update(['status'=>$countries->status]);
                        $count++;
                    }
                }
            }
            if ($count == $ids) {
                echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Country status updated successfully.']);
            } else {
                echo json_encode(["status" => 0, 'message' => 'Not all country were updated. Please try again later.']);
            }
        } else {
            echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
        }
	}

 	// activate/deactivate Country
    public function updateStatusAjax(Request $request) {

        if (isset($request->statusid) && $request->statusid != null) {
            $countries = Country::find($request->statusid);

            if (isset($countries->id)) {
                $countries->status = $request->status;
                if ($countries->save()) {
                	State::where("country_id",$countries->id)->update(['status'=>$countries->status]);

                	$getStateId = State::where("country_id",$countries->id)->pluck("id")->toArray();
                	
                	City::whereIn("state_id",$getStateId)->update(['status'=>$countries->status]);
            	 	
            	 	$getCityId = City::whereIn("state_id",$getStateId)->pluck("id")->toArray();

                	Region::where("country_id",$countries->id)
                	->whereIn("state_id",$getStateId)
                	->whereIn('city_id',$getCityId)
                	->update(['status'=>$countries->status]);
                    echo json_encode(['status' => 1, 'message' => 'Country status updated successfully.']);
                } else {
                    echo json_encode(['status' => 0, 'message' => 'Unable to update country. Please try again later.']);
                }
            } else {
                echo json_encode(['status' => 0, 'message' => 'Invalid data']);
            }
        } else {
            echo json_encode(['status' => 0, 'message' => 'Invalid data']);
        }
    }
}
