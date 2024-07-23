<?php

namespace App\Http\Controllers\Backend;
use App\Library\Helper;
use App\Library\Notify;    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Banner;
use App\User;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Image;
use URL; 
use Auth; 
use File; 
use Illuminate\Support\Arr;

class BannerController extends Controller {
	public $banner;
	public $columns;

	public function __construct() {
		$this->banner = new Banner;
		$this->columns = [
			"select", "s_no","name", "image", "status",
		];
		
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		$count = Banner::where('status', '!=', 'delete')->count();
		return view('backend.banners.index', compact('count'));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function bannerAjax(Request $request) {
		if (isset($request->search['value'])) {
            $request->search = $request->search['value'];
        }else{
            $request->search = '';
        }
		if (isset($request->order[0]['column'])) {
			$request->order_column = $request->order[0]['column'];
			$request->order_dir = $request->order[0]['dir'];
		}
		$records = $this->banner->fetchBanners($request, $this->columns);
        
		$count = $records->get();
		if (isset($request->start)) {
			$list = $records->offset($request->start)->limit($request->length)->get();
		} else {
			$list = $records->offset($request->start)->limit(count($count))->get();
		}
	
		$result = [];
		
		$total = count($count);
	
		$i = 1;
		foreach ($list as $cat) { 
			$data = [];
			$data['select'] = '<div class="form-check form-check-flat"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="banner_id[]" value="' . $cat->id . '"><i class="input-helper"></i></label></div>';
			$data['sno'] = $request->start + $i++;
			$data['name'] = !empty($cat->name) ?  ucfirst($cat->name) : '-' ;
            $data['image'] = ($cat->image != null) ? '<img src="'.$cat->image.'" width="70" />' : '-';
			$data['created_at'] =  date('d-m-Y', strtotime($cat->created_at));
			$data['activate'] = '<div class="bt-switch"><div ><input type="checkbox"' . ($cat->status == 'active' ? ' checked' : '') . ' data-id="' . $cat->id . '" data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" class="statusBanner"></div></div>';

			$action = '';
			
			if (Helper::checkAccess(route('editBanner'))) {
				$action .= '<a href="' . route('editBanner', ['id' => $cat->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
			}
			$action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('viewBanner', ['id' => $cat->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';
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
		$url = route('addBanner');
		$banner = new Banner;
		return view('backend.banners.create', compact('type', 'url', 'banner'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		$validate = Validator($request->all(), [
			'banner_name' => 'required',
		]);
		$attr = [
			'banner_name' => 'Banner Name',
		];
		$validate->setAttributeNames($attr);
		if ($validate->fails()) {
			return redirect()->route('createBanner')->withInput($request->all())->withErrors($validate);
		} else {
			try {
				if(!File::exists(public_path().'/uploads/banners')) {
		            File::makeDirectory(public_path().'/uploads/banners', 0777, true, true);
		        }
                 $userId = Auth::id();
				$banner = new Banner;
				$imageName = '';
				if ($request->file('image') != null) {
					$image = $request->file('image');
					$imageName = time() . $image->getClientOriginalName();
					$imageName = str_replace(' ', '', $imageName);
					$imageName = str_replace('.jpeg', '.jpg', $imageName);
					$image->move(public_path('uploads/banners'), $imageName);
					$imageName = str_replace('.jpeg', '.jpg', $imageName);
				}
		
				$banner->image = str_replace('.jpeg', '.jpg', $imageName);
                $banner->name = $request->post('banner_name');
				$banner->user_id = $userId;
				$banner->created_at = date('Y-m-d H:i:s');
				if ($banner->save()) {
					
					$request->session()->flash('success', 'Banner added successfully');
					return redirect()->route('banners');
				} else {
					$request->session()->flash('error', 'Something went wrong. Please try again later.');
					return redirect()->route('banners');
				}
			} catch (Exception $e) {
				$request->session()->flash('error', 'Something went wrong. Please try again later.');
				return redirect()->route('banners');
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
			$banner = Banner::where('id', $id)->first();
			if (isset($banner->id)) {
				return view('backend.banners.view', compact('banner', 'type'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('banners');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('banners');
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
            $banner = Banner::where('id', $id)->first();
            if (isset($banner->id)) {
                $type = 'edit';
                $url = route('updateBanner', ['id' => $banner->id]);
                return view('backend.banners.create', compact('banner', 'type', 'url'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('banners');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('banners');
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
			$banner = Banner::where('id', $id)->first();
            $userId = Auth::id();
			if (isset($banner->id)) {
				$validate = Validator($request->all(), [
					'banner_name' => 'required',
				]);
				$attr = [
					'banner_name' => 'Banner Name',
				];
				$validate->setAttributeNames($attr);
				if ($validate->fails()) {
					return redirect()->route('createBanner')->withInput($request->all())->withErrors($validate);
				} else {
					try {
						if(!File::exists(public_path().'/uploads/banners')) {
				            File::makeDirectory(public_path().'/uploads/banners', 0777, true, true);
				        }
						$imageName = '';
						if ($request->file('image') != null) {
							$image = $request->file('image');
							$imageName = time() . $image->getClientOriginalName();
							if ($banner->image != null && file_exists($banner->image)) {
								if ($banner->image != 'noimage.jpg') {
									unlink($banner->image);
								}
							}
							$imageName = str_replace(' ', '', $imageName);
							$imageName = str_replace('.jpeg', '.jpg', $imageName);
							$image->move(public_path('uploads/banners'), $imageName);
							$banner->image = str_replace('.jpeg', '.jpg', $imageName);
						}
						$banner->name = $request->post('banner_name');
						$banner->user_id = $userId;
						
						if ($banner->save()) {

							
							$request->session()->flash('success', 'Banner updated successfully');
							return redirect()->route('banners');
						} else {
							$request->session()->flash('error', 'Something went wrong. Please try again later.');
							return redirect()->route('banners');
						}
					} catch (Exception $e) {
						$request->session()->flash('error', 'Something went wrong. Please try again later.');
						return redirect()->route('banners');
					}
				}
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('banners');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('banners');
		}
	}

	// activate/deactivate SplashScreen
	public function updateStatus(Request $request) {

		if (isset($request->statusid) && $request->statusid != null) {
			$banner = Banner::find($request->statusid);

			if (isset($banner->id)) {
				$banner->status = $request->status;
				if ($banner->save()) {
					$request->session()->flash('success', 'Banner updated successfully.');
					return redirect()->back();
				} else {
					$request->session()->flash('error', 'Unable to update Banner. Please try again later.');
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
			$banner = Banner::find($request->statusid);
            
			if (isset($banner->id)) {
				$banner->status = $request->status;
				if ($banner->save()) {
					echo json_encode(['status' => 1, 'message' => 'Banner status updated successfully.']);
				} else {
					echo json_encode(['status' => 0, 'message' => 'Unable to update Banner. Please try again later.']);
				}
			} else {
				echo json_encode(['status' => 0, 'message' => 'Invalid Banner']);
			}
		} else {
			echo json_encode(['status' => 0, 'message' => 'Invalid Banner']);
		}

	}

	public function deleteBanners($root, $level) {
		$child = $root->childCat;
		foreach ($child as $ch) {
			$ch->status = 'delete';
			$ch->save();
			$this->deleteBanners($ch, ++$level);
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
			$recommendedBanner = Banner::find($request->deleteid);
			if (isset($recommendedBanner->id)) {
				$recommendedBanner->status = 'delete';
				if ($recommendedBanner->save()) {
					echo json_encode(['status' => 1, 'message' => 'Banner deleted successfully.']);
				} else {
					echo json_encode(['status' => 0, 'message' => 'Unable to delete Banner. Please try again later.']);
				}
			} else {
				echo json_encode(['status' => 0, 'message' => 'Invalid Banner']);
			}
		} else {
			echo json_encode(['status' => 0, 'message' => 'Invalid Banner']);
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
				$recommendedBanner = Banner::find($id);
				if (isset($recommendedBanner->id)) {
					$recommendedBanner->status = 'delete';
					if ($recommendedBanner->save()) {
						$count++;
					}
				}
			}
			if ($count == $ids) {
				echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Banner deleted successfully.']);
			} else {
				echo json_encode(["status" => 0, 'message' => 'Not all Banner were deleted. Please try again later.']);
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
				$recommendedBanner = Banner::find($id);
				if (isset($recommendedBanner->id)) {
					if ($recommendedBanner->status == 'active') {
						$recommendedBanner->status = 'inactive';
					} elseif ($recommendedBanner->status == 'inactive') {
						$recommendedBanner->status = 'active';
					}
					if ($recommendedBanner->save()) {
						$count++;
					}
				}
			}
			if ($count == $ids) {
				echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Banner status updated successfully.']);
			} else {
				echo json_encode(["status" => 0, 'message' => 'Not all Banner were updated. Please try again later.']);
			}
		} else {
			echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
		}
	}

}
