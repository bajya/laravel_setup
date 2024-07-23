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
use App\Region;
use App\Country;
use App\State;
use Illuminate\Support\Arr;

class RegionController extends Controller {
	public $regions;
	public $columns;

	public function __construct() {
		$this->regions = new Region;
		$this->columns = [
			"select","country_id","state_id","city_id","name","created_at",'activate', "action",
		];

		$this->middleware('permission:regions-list|regions-edit', ['only' => ['index','update']]);
        $this->middleware('permission:regions-edit', ['only' => ['edit','update']]);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		return view('backend.regions.index');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function regionsAjax(Request $request) {
		if (isset($request->searchval)) {
            $request->search = $request->searchval;
        }else{
            $request->search =  '';
        }

		if (isset($request->order[0]['column'])) {
			$request->order_column = $request->order[0]['column'];
			$request->order_dir = $request->order[0]['dir'];
		}
		$records = $this->regions->fetchRegion($request, $this->columns);
		$total = $records->get();
		if (isset($request->start)) {
			$regions = $records->offset($request->start)->limit($request->length)->get();
		} else {
			$regions = $records->offset($request->start)->limit(count($total))->get();
		}
		// echo $total;
		$result = [];
		$i = 1;
		foreach ($regions as $list) {
			$data = [];
			$data['sno'] = $i++;
			$data['country_name'] = $list->getCountry->name ?? '';
			$data['state_name'] = $list->getState->name ?? '';
			$data['city_name']= $list->getCity->name ?? '';
			$data['name']= $list->name ?? '';
			
		   	$data['select'] = '<div class="form-check form-check-flat"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="regions_id[]" value="' . $list->id . '"><i class="input-helper"></i></label></div>';
			
			$data['name'] = $list->name;
			$data['activate'] = '<div class="bt-switch"><div class="col-md-2"><input type="checkbox"' . ($list->status == 'active' ? ' checked' : '') . ' data-id="' . $list->id . '" data-code="' . $list->name . '" data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" class="statusRegions"></div></div>';

			$data['created_at'] =  date('d-m-Y', strtotime($list->created_at));

			$action = '<div class="actionBtn d-flex align-itemss-center" style="gap:8px">';

			if (Helper::checkAccess(route('editRegions'))) {
				$action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('editRegions', ['id' => $list->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
			}

		  //$action .= '&nbsp;&nbsp;&nbsp;<a href="javascript:;" class="toolTip deleteRegions" data-toggle="tooltip" data-id="' . $list->id . '" data-placement="bottom" title="Delete"><i class="fa fa-times"></i></a>';
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
		$url = route('storeRegions');
		return view('backend.regions.create', compact('type','url'));
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
			'city_id' => 'required',
			'region_name' => 'required',
		]);
		$attr = [
			'country_id' => 'Select Country',
			'state_id' => 'Select State',
			'city_id' => 'Select City',
			'region_name' => 'Region Name',
		];

		$validate->setAttributeNames($attr);

		if ($validate->fails()) {
			return redirect()->back()->withInput($request->all())->withErrors($validate);
		} else {
			  try {
				$regions = new Region();
				$regions->country_id = $request->country_id;
				$regions->state_id = $request->state_id;
				$regions->city_id = $request->city_id;
				$regions->name = $request->region_name;
				if ($regions->save()) {
                    $request->session()->flash('success', 'Region added successfully');
                    return redirect()->route('regions');
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
		

			$regions =Region::where('id', $id)->first();

			if (isset($regions->id)) {
				return view('backend.regions.view', compact('regions'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('regions');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('regions');
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
			$url=	route('updateRegions',['id'=>$id ?? '']);
			$regions= Region::where('id', $id)->first();
			if (isset($regions->id)) {
				$type = 'Edit';
				return view('backend.regions.create', compact('regions', 'type','url'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('regions');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('regions');
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

			$regions = Region::where('id', $id)->first();

				if (isset($regions->id)) {
				try {
				$regions->country_id = $request->country_id;
				$regions->state_id = $request->state_id;
				$regions->city_id = $request->city_id;
				$regions->name = $request->region_name;
					$regions->save();
					$request->session()->flash('success', 'Region updated successfully');
					return redirect()->route('regions');

				} catch (Exception $e) {
					$request->session()->flash('error', 'Something went wrong. Please try again later.');
					return redirect()->route('regions');
				}

			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('regions');
			}
			
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('regions');
		}

	}

	

	public function checkRegionsName(Request  $request ,$id = null){
		 	
      	 if (isset($request->region_name)) {
		    $check = Region::where('name', $request->region_name)->where("status","!=","delete");
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
            $countries = Regionfind($request->deleteid);

            if (isset($countries->id)) {
                $countries->status = 'delete';
                if ($countries->save()) {
                 
                    echo json_encode(["status" => 1, 'ids' => json_encode($request->deleteid), 'message' => 'Region deleted successfully.']);
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
                $user = Region::find($id);

                if (isset($user->id)) {
                    $user->status = 'delete';
                    if ($user->save()) {
                        $count++;
                    }
                }
            }
            if ($count == $ids) {
                echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Region deleted successfully.']);
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
                $regions = Region::find($id);

                if (isset($regions->id)) {
                    if ($regions->status == 'active') {
                        $regions->status = 'inactive';
                    } elseif ($regions->status == 'inactive') {
                        $regions->status = 'active';
                    }
                    if ($regions->save()) {
                    	$count++;
                    }
                }
            }
            if ($count == $ids) {
                echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Region status updated successfully.']);
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
            $regions = Region::find($request->statusid);

            if (isset($regions->id)) {
                $regions->status = $request->status;
                if ($regions->save()) {
                    echo json_encode(['status' => 1, 'message' => 'Region status updated successfully.']);
                } else {
                    echo json_encode(['status' => 0, 'message' => 'Unable to update Region. Please try again later.']);
                }
            } else {
                echo json_encode(['status' => 0, 'message' => 'Invalid data']);
            }
        } else {
            echo json_encode(['status' => 0, 'message' => 'Invalid data']);
        }
    }
}
