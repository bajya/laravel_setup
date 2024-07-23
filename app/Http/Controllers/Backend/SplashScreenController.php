<?php

namespace App\Http\Controllers\Backend;
use App\Library\Helper;
use App\Library\Notify;    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SplashScreen;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Image;
use URL; 
use Auth; 
use File; 
use Illuminate\Support\Arr;

class SplashScreenController extends Controller {
	public $splashscreen;
	public $columns;

	public function __construct() {
		$this->splashscreen = new SplashScreen;
		$this->columns = [
			"select", "s_no", "name", "image", "description", "status", "activate", "action"
		];
		/*$this->middleware('permission:splashscreen-list|splashscreen-create|splashscreen-edit|splashscreen-delete', ['only' => ['index','store']]);
        $this->middleware('permission:splashscreen-create', ['only' => ['create','store']]);
        $this->middleware('permission:splashscreen-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:splashscreen-delete', ['only' => ['destroy']]);*/
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		$count = SplashScreen::where('status', '!=', 'delete')->count();
		return view('backend.splashscreens.index', compact('count'));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function splashscreenAjax(Request $request) {
		if (isset($request->search['value'])) {
            $request->search = $request->search['value'];
        }else{
            $request->search = '';
        }
		if (isset($request->order[0]['column'])) {
			$request->order_column = $request->order[0]['column'];
			$request->order_dir = $request->order[0]['dir'];
		}
		$records = $this->splashscreen->fetchSplashScreens($request, $this->columns);
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
			$data['select'] = '<div class="form-check form-check-flat"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="splashscreen_id[]" value="' . $cat->id . '"><i class="input-helper"></i></label></div>';
			$data['sno'] = $request->start + $i++;
			$data['name'] = ($cat->name != null) ? \Str::limit($cat->name, 50, '...') : '-'; 
			$data['image'] = ($cat->image != null) ? '<img src="'.$cat->image.'" width="70" />' : '-';
			$data['description'] = ($cat->description != null) ? \Str::limit($cat->description, 50, '...') : '-';
			$data['status'] = ucfirst(config('constants.STATUS.' . $cat->status));
			$data['created_at'] =  date('d-m-Y', strtotime($cat->created_at));
			$data['activate'] = '<div class="bt-switch"><div class="col-md-2"><input type="checkbox"' . ($cat->status == 'active' ? ' checked' : '') . ' data-id="' . $cat->id . '" data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" class="statusSplashScreen"></div></div>';
			
			$action = '';
			
			if (Helper::checkAccess(route('editSplashScreen'))) {
				$action .= '<a href="' . route('editSplashScreen', ['id' => $cat->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
			}
			$action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('viewSplashScreen', ['id' => $cat->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';
			/*if (Helper::checkAccess(route('deleteSplashScreen'))) {
				$action .= '&nbsp;&nbsp;&nbsp;<a href="javascript:;" class="toolTip deleteSplashScreen" data-toggle="tooltip" data-placement="bottom" data-id="' . $cat->id . '" title="Delete"><i class="fa fa-times"></i></a>';
			}*/
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
		$url = route('addSplashScreen');
		$splashscreen = new SplashScreen;
		return view('backend.splashscreens.create', compact('type', 'url', 'splashscreen'));
	}

	/**
	 * check for unique splashscreen
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function checkSplashScreen(Request $request, $id = null) {
		if (isset($request->splashscreen_name)) {
			$check = SplashScreen::where('name', $request->splashscreen_name);
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
		$validate = Validator($request->all(), [
			'splashscreen_name' => 'required',
			'description' => 'required',
			'splashscreen_image' => 'mimes:jpeg,png,jpg,gif,svg',
		]);
		$attr = [
			'splashscreen_name' => 'Splash Screen Name',
			'description' => 'Description',
			'splashscreen_image' => 'Image',
		];
		$validate->setAttributeNames($attr);
		if ($validate->fails()) {
			return redirect()->route('createSplashScreen')->withInput($request->all())->withErrors($validate);
		} else {
			try {
				if(!File::exists(public_path().'/uploads/splashscreens')) {
		            File::makeDirectory(public_path().'/uploads/splashscreens', 0777, true, true);
		        }
				$splashscreen = new SplashScreen;
				$imageName = '';
				if ($request->file('splashscreen_image') != null) {
					$image = $request->file('splashscreen_image');
					$imageName = time() . $image->getClientOriginalName();
					$imageName = str_replace(' ', '', $imageName);
					$imageName = str_replace('.jpeg', '.jpg', $imageName);
					$image->move(public_path('uploads/splashscreens'), $imageName);
					$imageName = str_replace('.jpeg', '.jpg', $imageName);
				}
				$splashscreen->name = $request->post('splashscreen_name');
				$splashscreen->user_id = Auth::user()->id;
				$splashscreen->image = str_replace('.jpeg', '.jpg', $imageName);
				$splashscreen->description = $request->post('description');
				$splashscreen->status = trim($request->post('status'));
				$splashscreen->created_at = date('Y-m-d H:i:s');
				if ($splashscreen->save()) {
					
					$request->session()->flash('success', 'Splash Screen added successfully');
					return redirect()->route('splashscreens');
				} else {
					$request->session()->flash('error', 'Something went wrong. Please try again later.');
					return redirect()->route('splashscreens');
				}
			} catch (Exception $e) {
				$request->session()->flash('error', 'Something went wrong. Please try again later.');
				return redirect()->route('splashscreens');
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
			$splashscreen = SplashScreen::where('id', $id)->first();
			if (isset($splashscreen->id)) {
				return view('backend.splashscreens.view', compact('splashscreen', 'type'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('splashscreens');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('splashscreens');
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
            $splashscreen = SplashScreen::where('id', $id)->first();
            if (isset($splashscreen->id)) {
                $type = 'edit';
                $url = route('updateSplashScreen', ['id' => $splashscreen->id]);
                return view('backend.splashscreens.create', compact('splashscreen', 'type', 'url'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('splashscreens');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('splashscreens');
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
			$splashscreen = SplashScreen::where('id', $id)->first();
			if (isset($splashscreen->id)) {
				$validate = Validator($request->all(), [
					'splashscreen_name' => 'required',
					'description' => 'required',
				]);
				$attr = [
					'splashscreen_name' => 'Splash Screen Name',
					'description' => 'Description',
				];
				$validate->setAttributeNames($attr);
				if ($validate->fails()) {
					return redirect()->route('createSplashScreen')->withInput($request->all())->withErrors($validate);
				} else {
					try {
						if(!File::exists(public_path().'/uploads/splashscreens')) {
				            File::makeDirectory(public_path().'/uploads/splashscreens', 0777, true, true);
				        }
						$imageName = '';
						if ($request->file('splashscreen_image') != null) {
							$image = $request->file('splashscreen_image');
							$imageName = time() . $image->getClientOriginalName();
							if ($splashscreen->image != null && file_exists($splashscreen->image)) {
								if ($splashscreen->image != 'noimage.jpg') {
									unlink($splashscreen->image);
								}
							}
							$imageName = str_replace(' ', '', $imageName);
							$imageName = str_replace('.jpeg', '.jpg', $imageName);
							$image->move(public_path('uploads/splashscreens'), $imageName);
							$splashscreen->image = str_replace('.jpeg', '.jpg', $imageName);
						}
						$splashscreen->name = $request->post('splashscreen_name');
						$splashscreen->description = $request->post('description');
						$splashscreen->status = trim($request->post('status'));

						if ($splashscreen->save()) {

							
							$request->session()->flash('success', 'Splash Screen updated successfully');
							return redirect()->route('splashscreens');
						} else {
							$request->session()->flash('error', 'Something went wrong. Please try again later.');
							return redirect()->route('splashscreens');
						}
					} catch (Exception $e) {
						$request->session()->flash('error', 'Something went wrong. Please try again later.');
						return redirect()->route('splashscreens');
					}
				}
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('splashscreens');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('splashscreens');
		}
	}

	// activate/deactivate SplashScreen
	public function updateStatus(Request $request) {

		if (isset($request->statusid) && $request->statusid != null) {
			$splashscreen = SplashScreen::find($request->statusid);

			if (isset($splashscreen->id)) {
				$splashscreen->status = $request->status;
				if ($splashscreen->save()) {
					$request->session()->flash('success', 'Splash Screen status updated successfully.');
					return redirect()->back();
				} else {
					$request->session()->flash('error', 'Unable to update SplashScreen. Please try again later.');
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
  
	// activate/deactivate SplashScreen
	public function updateStatusAjax(Request $request) {

		if (isset($request->statusid) && $request->statusid != null) {
			$splashscreen = SplashScreen::find($request->statusid);

			if (isset($splashscreen->id)) {
				$splashscreen->status = $request->status;
				if ($splashscreen->save()) {
					echo json_encode(['status' => 1, 'message' => 'Splash Screen status updated successfully.']);
				} else {
					echo json_encode(['status' => 0, 'message' => 'Unable to update Splash Screen. Please try again later.']);
				}
			} else {
				echo json_encode(['status' => 0, 'message' => 'Invalid Splash Screen']);
			}
		} else {
			echo json_encode(['status' => 0, 'message' => 'Invalid Splash Screen']);
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
			$recommendedSplashScreen = SplashScreen::find($request->deleteid);
			if (isset($recommendedSplashScreen->id)) {
				$recommendedSplashScreen->status = 'delete';
				if ($recommendedSplashScreen->save()) {
					echo json_encode(['status' => 1, 'message' => 'Splash Screen deleted successfully.']);
				} else {
					echo json_encode(['status' => 0, 'message' => 'Unable to delete Splash Screen. Please try again later.']);
				}
			} else {
				echo json_encode(['status' => 0, 'message' => 'Invalid Splash Screen']);
			}
		} else {
			echo json_encode(['status' => 0, 'message' => 'Invalid Splash Screen']);
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
				$recommendedSplashScreen = SplashScreen::find($id);
				if (isset($recommendedSplashScreen->id)) {
					$recommendedSplashScreen->status = 'delete';
					if ($recommendedSplashScreen->save()) {
						$count++;
					}
				}
			}
			if ($count == $ids) {
				echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Splash Screen deleted successfully.']);
			} else {
				echo json_encode(["status" => 0, 'message' => 'Not all Splash Screen were deleted. Please try again later.']);
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
				$recommendedSplashScreen = SplashScreen::find($id);
				if (isset($recommendedSplashScreen->id)) {
					if ($recommendedSplashScreen->status == 'active') {
						$recommendedSplashScreen->status = 'inactive';
					} elseif ($recommendedSplashScreen->status == 'inactive') {
						$recommendedSplashScreen->status = 'active';
					}
					if ($recommendedSplashScreen->save()) {
						$count++;
					}
				}
			}
			if ($count == $ids) {
				echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Splash Screen updated successfully.']);
			} else {
				echo json_encode(["status" => 0, 'message' => 'Not all Splash Screens were updated. Please try again later.']);
			}
		} else {
			echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
		}
	}

}
