<?php

namespace App\Http\Controllers\Backend;
use App\Library\Helper;
use App\Library\Notify;    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\TutorialScreen;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Image;
use URL; 
use Auth; 
use File; 
use Illuminate\Support\Arr;

class TutorialScreenController extends Controller {
	public $tutorialscreen;
	public $columns;

	public function __construct() {
		$this->tutorialscreen = new TutorialScreen;
		$this->columns = [
			"select", "s_no", "name", "image", "description", "status", "activate", "action"
		];
	
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		$count = TutorialScreen::where('status', '!=', 'delete')->count();
		return view('backend.tutorialscreens.index', compact('count'));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function tutorialscreenAjax(Request $request) {
		if (isset($request->search['value'])) {
            $request->search = $request->search['value'];
        }else{
            $request->search = '';
        }
		if (isset($request->order[0]['column'])) {
			$request->order_column = $request->order[0]['column'];
			$request->order_dir = $request->order[0]['dir'];
		}
		$records = $this->tutorialscreen->fetchTutorialScreens($request, $this->columns);
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
			$data['select'] = '<div class="form-check form-check-flat"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="tutorialscreen_id[]" value="' . $cat->id . '"><i class="input-helper"></i></label></div>';
			$data['sno'] = $request->start + $i++;
			$data['name'] = ($cat->name != null) ? \Str::limit($cat->name, 50, '...') : '-'; 
			$data['image'] = ($cat->image != null) ? '<img src="'.$cat->image.'" width="70" />' : '-';


			$data['description'] = ($cat->description != null) ? \Str::limit($cat->description, 50, '...') : '-';
			$data['status'] = ucfirst(config('constants.STATUS.' . $cat->status));
$data['created_at'] =  date('d-m-Y', strtotime($cat->created_at));
			$data['activate'] = '<div class="bt-switch"><div class="col-md-2"><input type="checkbox"' . ($cat->status == 'active' ? ' checked' : '') . ' data-id="' . $cat->id . '" data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" class="statusTutorialScreen"></div></div>';
			

			$action = '';
			
			if (Helper::checkAccess(route('editTutorialScreen'))) {
				$action .= '<a href="' . route('editTutorialScreen', ['id' => $cat->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
			}
			$action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('viewTutorialScreen', ['id' => $cat->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';
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
		$url = route('addTutorialScreen');
		$tutorialscreen = new TutorialScreen;
		return view('backend.tutorialscreens.create', compact('type', 'url', 'tutorialscreen'));
	}

	/**
	 * check for unique TutorialScreen
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function checkTutorialScreen(Request $request, $id = null) {
		if (isset($request->tutorialscreen_name)) {
			$check = TutorialScreen::where('name', $request->tutorialscreen_name);
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
			'tutorialscreen_name' => 'required',
			'description' => 'required',
			'tutorialscreen_image' => 'mimes:jpeg,png,jpg,gif,svg',
		]);
		$attr = [
			'tutorialscreen_name' => 'Tutorial Screen Name',
			'description' => 'Description',
			'tutorialscreen_image' => 'Image',
		];
		$validate->setAttributeNames($attr);
		if ($validate->fails()) {
			return redirect()->route('createTutorialScreen')->withInput($request->all())->withErrors($validate);
		} else {
			try {
				if(!File::exists(public_path().'/uploads/tutorialscreens')) {
		            File::makeDirectory(public_path().'/uploads/tutorialscreens', 0777, true, true);
		        }
				$tutorialscreen = new TutorialScreen;
				$imageName = '';
				if ($request->file('tutorialscreen_image') != null) {
					$image = $request->file('tutorialscreen_image');
					$imageName = time() . $image->getClientOriginalName();
					$imageName = str_replace(' ', '', $imageName);
					$imageName = str_replace('.jpeg', '.jpg', $imageName);
					$image->move(public_path('uploads/tutorialscreens'), $imageName);
					$imageName = str_replace('.jpeg', '.jpg', $imageName);
				}
				$tutorialscreen->name = $request->post('tutorialscreen_name');
				$tutorialscreen->user_id = Auth::user()->id;
				$tutorialscreen->image = str_replace('.jpeg', '.jpg', $imageName);
				$tutorialscreen->description = $request->post('description');
				$tutorialscreen->status = trim($request->post('status'));
				$tutorialscreen->created_at = date('Y-m-d H:i:s');
				if ($tutorialscreen->save()) {
					
					$request->session()->flash('success', 'Tutorial Screen added successfully');
					return redirect()->route('tutorialscreens');
				} else {
					$request->session()->flash('error', 'Something went wrong. Please try again later.');
					return redirect()->route('tutorialscreens');
				}
			} catch (Exception $e) {
				$request->session()->flash('error', 'Something went wrong. Please try again later.');
				return redirect()->route('tutorialscreens');
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
			$tutorialscreen = TutorialScreen::where('id', $id)->first();
			if (isset($tutorialscreen->id)) {
				return view('backend.tutorialscreens.view', compact('tutorialscreen', 'type'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('tutorialscreens');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('tutorialscreens');
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
            $tutorialscreen = TutorialScreen::where('id', $id)->first();
            if (isset($tutorialscreen->id)) {
                $type = 'edit';
                $url = route('updateTutorialScreen', ['id' => $tutorialscreen->id]);
                return view('backend.Tutorialscreens.create', compact('tutorialscreen', 'type', 'url'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('tutorialscreens');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('tutorialscreens');
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
			$tutorialscreen = TutorialScreen::where('id', $id)->first();
			if (isset($tutorialscreen->id)) {
				$validate = Validator($request->all(), [
					'tutorialscreen_name' => 'required',
					'description' => 'required',
				]);
				$attr = [
					'tutorialscreen_name' => 'Tutorial Screen Name',
					'description' => 'Description',
				];
				$validate->setAttributeNames($attr);
				if ($validate->fails()) {
					return redirect()->route('createTutorialScreen')->withInput($request->all())->withErrors($validate);
				} else {
					try {
						if(!File::exists(public_path().'/uploads/tutorialscreens')) {
				            File::makeDirectory(public_path().'/uploads/tutorialscreens', 0777, true, true);
				        }
						$imageName = '';
						if ($request->file('tutorialscreen_image') != null) {
							$image = $request->file('tutorialscreen_image');
							$imageName = time() . $image->getClientOriginalName();
							if ($tutorialscreen->image != null && file_exists($tutorialscreen->image)) {
								if ($tutorialscreen->image != 'noimage.jpg') {
									unlink($tutorialscreen->image);
								}
							}
							$imageName = str_replace(' ', '', $imageName);
							$imageName = str_replace('.jpeg', '.jpg', $imageName);
							$image->move(public_path('uploads/tutorialscreens'), $imageName);
							$tutorialscreen->image = str_replace('.jpeg', '.jpg', $imageName);
						}
						$tutorialscreen->name = $request->post('tutorialscreen_name');
						$tutorialscreen->description = $request->post('description');
						$tutorialscreen->status = trim($request->post('status'));

						if ($tutorialscreen->save()) {

							
							$request->session()->flash('success', 'Tutorial Screen updated successfully');
							return redirect()->route('tutorialscreens');
						} else {
							$request->session()->flash('error', 'Something went wrong. Please try again later.');
							return redirect()->route('tutorialscreens');
						}
					} catch (Exception $e) {
						$request->session()->flash('error', 'Something went wrong. Please try again later.');
						return redirect()->route('tutorialscreens');
					}
				}
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('splastutorialscreenshscreens');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('tutorialscreens');
		}
	}

	// activate/deactivate tutorialscreen
	public function updateStatus(Request $request) {

		if (isset($request->statusid) && $request->statusid != null) {
			$tutorialscreen = TutorialScreen::find($request->statusid);

			if (isset($tutorialscreen->id)) {
				$tutorialscreen->status = $request->status;
				if ($tutorialscreen->save()) {
					$request->session()->flash('success', 'Tutorial Screen status updated successfully.');
					return redirect()->back();
				} else {
					$request->session()->flash('error', 'Unable to update TutorialScreen. Please try again later.');
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
  
	// activate/deactivate tutorialscreen
	public function updateStatusAjax(Request $request) {

		if (isset($request->statusid) && $request->statusid != null) {
			$tutorialscreen = TutorialScreen::find($request->statusid);

			if (isset($tutorialscreen->id)) {
				$tutorialscreen->status = $request->status;
				if ($tutorialscreen->save()) {
					echo json_encode(['status' => 1, 'message' => 'Tutorial Screen status updated successfully.']);
				} else {
					echo json_encode(['status' => 0, 'message' => 'Unable to update Tutorial Screen. Please try again later.']);
				}
			} else {
				echo json_encode(['status' => 0, 'message' => 'Invalid Tutorial Screen']);
			}
		} else {
			echo json_encode(['status' => 0, 'message' => 'Invalid Tutorial Screen']);
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
			$recommendedTutorialScreen = TutorialScreen::find($request->deleteid);
			if (isset($recommendedTutorialScreen->id)) {
				$recommendedTutorialScreen->status = 'delete';
				if ($recommendedTutorialScreen->save()) {
					echo json_encode(['status' => 1, 'message' => 'Tutorial Screen deleted successfully.']);
				} else {
					echo json_encode(['status' => 0, 'message' => 'Unable to delete Tutorial Screen. Please try again later.']);
				}
			} else {
				echo json_encode(['status' => 0, 'message' => 'Invalid Tutorial Screen']);
			}
		} else {
			echo json_encode(['status' => 0, 'message' => 'Invalid Tutorial Screen']);
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
				$recommendedTutorialScreen = TutorialScreen::find($id);
				if (isset($recommendedTutorialScreen->id)) {
					$recommendedTutorialScreen->status = 'delete';
					if ($recommendedTutorialScreen->save()) {
						$count++;
					}
				}
			}
			if ($count == $ids) {
				echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Tutorial Screen deleted successfully.']);
			} else {
				echo json_encode(["status" => 0, 'message' => 'Not all Tutorial Screen were deleted. Please try again later.']);
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
				$recommendedTutorialScreen = TutorialScreen::find($id);
				if (isset($recommendedTutorialScreen->id)) {
					if ($recommendedTutorialScreen->status == 'active') {
						$recommendedTutorialScreen->status = 'inactive';
					} elseif ($recommendedTutorialScreen->status == 'inactive') {
						$recommendedTutorialScreen->status = 'active';
					}
					if ($recommendedTutorialScreen->save()) {
						$count++;
					}
				}
			}
			if ($count == $ids) {
				echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Tutorial Screen updated successfully.']);
			} else {
				echo json_encode(["status" => 0, 'message' => 'Not all Tutorial Screens were updated. Please try again later.']);
			}
		} else {
			echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
		}
	}

}
