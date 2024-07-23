<?php

namespace App\Http\Controllers\Backend;
use App\Library\Helper;
use App\Library\Notify;    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Country;
use App\State;
use App\City;
use App\Region;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Illuminate\Support\Arr;

class StateController extends Controller {
	public $state;
	public $columns;

	public function __construct() {
		$this->states = new State;
		$this->columns = [
			"select", "name",'country_id',"created_at", "activate", "action",
		];

		$this->middleware('permission:states-list|states-edit', ['only' => ['index','update']]);
        $this->middleware('permission:states-edit', ['only' => ['edit','update']]);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		return view('backend.states.index');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function statesAjax(Request $request) {

		if (isset($request->search['value'])) {
            $request->search = $request->search['value'];
        }else{
            $request->search = $request->search ?? '';
        }
		if (isset($request->order[0]['column'])) {
			$request->order_column = $request->order[0]['column'];
			$request->order_dir = $request->order[0]['dir'];
		}
		$records = $this->states->fetchStates($request, $this->columns);
		$total = $records->get();
		
		if (isset($request->start)) {
			$states = $records->offset($request->start)->limit($request->length)->get();
		} else {
			$states = $records->offset($request->start)->limit(count($total))->get();
		}
		
		// echo $total;
		$result = [];
		$i = 1;
		foreach ($states as $list) {
			$data = [];
	
			if(isset($list->getCountry) && ($list->getCountry->name))
			{
			$data['sno'] = $i++;
		   	$data['select'] = '<div class="form-check form-check-flat"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="states_id[]" value="' . $list->id . '"><i class="input-helper"></i></label></div>';
			$data['country_name'] = $list->getCountry->name ?? '';
			$data['name'] = $list->name;
			$data['created_at'] =  date('d-m-Y', strtotime($list->created_at));

			//$data['status'] = ucfirst(config('constants.STATUS.' . $list->status));
            $data['activate'] = '<div class="bt-switch"><div class="col-md-2"><input type="checkbox"' . ($list->status == 'active' ? ' checked' : '') . ' data-id="' . $list->id . '" data-code="' . $list->name . '" data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" class="statusStates"></div></div>';
			
			$action = '<div class="actionBtn d-flex align-itemss-center" style="gap:8px">';
         
			if (Helper::checkAccess(route('editStates'))) {
				$action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('editStates', ['id' => $list->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
			}
			// $action .= '&nbsp;&nbsp;&nbsp;<a href="javascript:;" class="toolTip deleteStates" data-toggle="tooltip" data-id="' . $list->id . '" data-placement="bottom" title="Delete"><i class="fa fa-times"></i></a>';
			$action.="</div>";
			$data['action'] = $action;

			
			
			$result[] = $data;
			}
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
		$url = route('storeStates');
		$states =null;
		$countrylist = Country::where("status","active")->get();
        return view('backend.states.create', compact('type','states','url','countrylist'));
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
			'state_name' => 'required',
		]);
		$attr = [
			'country_id' => 'Please Select Country',
			'state_namee' => 'Please State Name',
		];
		$validate->setAttributeNames($attr);

		if ($validate->fails()) {
			return redirect()->back()->withInput($request->all())->withErrors($validate);
		} else {
			  try {
				$states = new State();
				$states->country_id = $request->country_id;
				$states->name = $request->state_name;
				if ($states->save()) {
                    $request->session()->flash('success', 'State added successfully');
                    return redirect()->route('states');
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
			$states = $this->states::where('id', $id)->first();
			if (isset($states->id)) {
				return view('backend.states.view', compact('states'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('states');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('states');
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
			$url=	route('updateStates',['id'=>$id ?? '']);
			$countrylist = Country::where("status","active")->get();
			$states = $this->states::where('id', $id)->first();
			if (isset($states->id)) {
				$type = 'Edit';
				return view('backend.states.create', compact('states', 'type','url','countrylist'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('states');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('states');
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

			$states = $this->states::where('id', $id)->first();

			if (isset($states->id)) {
				try {
					$states->country_id = $request->country_id;
					$states->name = $request->state_name;
					$states->save();
					$request->session()->flash('success', 'State updated successfully');
					return redirect()->route('states');

				} catch (Exception $e) {
					$request->session()->flash('error', 'Something went wrong. Please try again later.');
					return redirect()->route('states');
				}

			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('states');
			}
			
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('states');
		}

	}

	

	public function checkStatesName(Request  $request ,$id = null){
		 	
      	 if (isset($request->state_name)) {
		    $check = State::where('name', $request->state_name)->where("status","=","active");
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
            $state = State::find($request->deleteid);

            if (isset($state->id)) {
                $state->status = 'delete';
                if ($state->save()) {
                   City::where("state_id",$state->id)->update(['status'=>$state->status]);
                    echo json_encode(["status" => 1, 'ids' => json_encode($request->deleteid), 'message' => 'State deleted successfully.']);
                } else {
                    echo json_encode(["status" => 0, 'message' => 'Not all state were deleted. Please try again later.']);
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
                $user = State::find($id);

                if (isset($user->id)) {
                    $user->status = 'delete';
                    if ($user->save()) {
                    City::where("state_id",$state->id)->update(['status'=>$state->status]);
                        $count++;
                    }
                }
            }
            if ($count == $ids) {
                echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'State deleted successfully.']);
            } else {
                echo json_encode(["status" => 0, 'message' => 'Not all state were deleted. Please try again later.']);
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
                $state = State::find($id);

                if (isset($state->id)) {
                    if ($state->status == 'active') {
                        $state->status = 'inactive';
                    } elseif ($state->status == 'inactive') {
                        $state->status = 'active';
                    }

                    if ($state->save()) {
                    	City::where("state_id",$state->id)->update(['status'=>$state->status]);
                        $count++;
                    }
                }
            }
            if ($count == $ids) {
                echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'states status updated successfully.']);
            } else {
                echo json_encode(["status" => 0, 'message' => 'Not all states were updated. Please try again later.']);
            }
        } else {
            echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
        }
	}

 	// activate/deactivate Country
    public function updateStatusAjax(Request $request) {

        if (isset($request->statusid) && $request->statusid != null) {
            $states = State::find($request->statusid);

            if (isset($states->id)) {
                $states->status = $request->status;
               
                if ($states->save()) {
                		City::where("state_id",$states->id)->update(['status'=>$states->status]);

                		$getCityId = City::where("state_id",$states->id)->pluck('id')->toArray();
                		if(Region::where(["country_id"=>$states->country_id,'state_id'=>$states->id])->count());
                		{  
							$getRegion = Region::where(["country_id"=>$states->country_id,'state_id'=>$states->id])->update(['status'=>$states->status]);
                		}
                		
                    echo json_encode(['status' => 1, 'message' => 'State status updated successfully.']);
                } else {
                    echo json_encode(['status' => 0, 'message' => 'Unable to update states. Please try again later.']);
                }
            } else {
                echo json_encode(['status' => 0, 'message' => 'Invalid states']);
            }
        } else {
            echo json_encode(['status' => 0, 'message' => 'Invalid states']);
        }
    }
	
}
