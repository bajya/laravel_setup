<?php

namespace App\Http\Controllers\Backend;
use App\Library\Helper;
use App\Library\Notify;    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\VendorAttribute;
use App\User;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Image;
use URL; 
use Auth; 
use File; 
use Illuminate\Support\Arr;

class AttributeController extends Controller {
	public $attribute;
	public $columns;

	public function __construct() {
		$this->attribute = new VendorAttribute;
		$this->columns = [
			"select", "s_no","name", "vendor_id", "image", "status", "activate", "action"
		];
		
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		$count = VendorAttribute::where('status', '!=', 'delete')->count();
		return view('backend.attributes.index', compact('count'));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function attributeAjax(Request $request) {
		if (isset($request->search['value'])) {
            $request->search = $request->search['value'];
        }else{
            $request->search = '';
        }
		if (isset($request->order[0]['column'])) {
			$request->order_column = $request->order[0]['column'];
			$request->order_dir = $request->order[0]['dir'];
		}
		$records = $this->attribute->fetchAttributes($request, $this->columns);
        
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
			$data['select'] = '<div class="form-check form-check-flat"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="attribute_id[]" value="' . $cat->id . '"><i class="input-helper"></i></label></div>';
			$data['sno'] = $request->start + $i++;
			$data['name'] = !empty($cat->name) ?  ucfirst($cat->name) : '-' ;
            $data['vendor_id'] = isset($cat->vendor->store_name) && !empty($cat->vendor->store_name) ? ucfirst($cat->vendor->store_name) : '-';
			$data['image'] = ($cat->image != null) ? '<img src="'.$cat->image.'" width="70" />' : '-';
			$data['created_at'] =  date('d-m-Y', strtotime($cat->created_at));
			$data['activate'] = '<div class="bt-switch"><div ><input type="checkbox"' . ($cat->status == 'active' ? ' checked' : '') . ' data-id="' . $cat->id . '" data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" class="statusAttribute"></div></div>';
			



			$action = '';
			
			if (Helper::checkAccess(route('editAttribute'))) {
				$action .= '<a href="' . route('editAttribute', ['id' => $cat->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
			}
			$action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('viewAttribute', ['id' => $cat->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';
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
		$url = route('addAttribute');
		$attribute = new VendorAttribute;
        $list = User::where("status","active")->where('role','Vendor')->get(['id',"store_name"]);
		return view('backend.attributes.create', compact('type', 'url', 'attribute','list'));
	}

	/**
	 * check for unique splashscreen
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	// public function checkAttribute(Request $request, $id = null) {
	// 	if (isset($request->box_type)) {
	// 		$check = VendorAttribute::where('name', $request->box_type);
	// 		if (isset($id) && $id != null) {
	// 			$check = $check->where('id', '!=', $id);
	// 		}
	// 		$check = $check->where('status', '!=', 'delete')->count();
	// 		if ($check > 0) {
	// 			return "false";
	// 		} else {
	// 			return "true";
	// 		}
	// 	} else {
	// 		return "true";
	// 	}
	// }

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		$validate = Validator($request->all(), [
			'name' => 'required',
		]);
		$attr = [
			'name' => 'Attribute Name',
		];
		$validate->setAttributeNames($attr);
		if ($validate->fails()) {
			return redirect()->route('createAttribute')->withInput($request->all())->withErrors($validate);
		} else {
			try {
				if(!File::exists(public_path().'/uploads/vendorattributes')) {
		            File::makeDirectory(public_path().'/uploads/vendorattributes', 0777, true, true);
		        }
				$attribute = new VendorAttribute;
				$imageName = '';
				if ($request->file('image') != null) {
					$image = $request->file('image');
					$imageName = time() . $image->getClientOriginalName();
					$imageName = str_replace(' ', '', $imageName);
					$imageName = str_replace('.jpeg', '.jpg', $imageName);
					$image->move(public_path('uploads/vendorattributes'), $imageName);
					$imageName = str_replace('.jpeg', '.jpg', $imageName);
				}
		
				$attribute->image = str_replace('.jpeg', '.jpg', $imageName);
                $attribute->name = $request->post('name');
				$attribute->vendor_id = $request->post('vendor_id');;
				$attribute->created_at = date('Y-m-d H:i:s');
				if ($attribute->save()) {
					
					$request->session()->flash('success', 'Attribute added successfully');
					return redirect()->route('attributes');
				} else {
					$request->session()->flash('error', 'Something went wrong. Please try again later.');
					return redirect()->route('attributes');
				}
			} catch (Exception $e) {
				$request->session()->flash('error', 'Something went wrong. Please try again later.');
				return redirect()->route('attributes');
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
			$attribute = VendorAttribute::where('id', $id)->first();
			if (isset($attribute->id)) {
				return view('backend.attributes.view', compact('attribute', 'type'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('attributes');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('attributes');
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
            $attribute = VendorAttribute::where('id', $id)->first();
            if (isset($attribute->id)) {
                $type = 'edit';
                $url = route('updateAttribute', ['id' => $attribute->id]);
                $list = User::where("status","active")->where('role','Vendor')->get(['id',"store_name"]);
                return view('backend.attributes.create', compact('attribute', 'type', 'url','list'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('attributes');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('attributes');
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
			$attribute = VendorAttribute::where('id', $id)->first();
			if (isset($attribute->id)) {
				$validate = Validator($request->all(), [
					'name' => 'required',
				]);
				$attr = [
					'name' => 'Attribute Name',
				];
				$validate->setAttributeNames($attr);
				if ($validate->fails()) {
					return redirect()->route('createAttribute')->withInput($request->all())->withErrors($validate);
				} else {
					try {
						if(!File::exists(public_path().'/uploads/vendorattributes')) {
				            File::makeDirectory(public_path().'/uploads/vendorattributes', 0777, true, true);
				        }
						$imageName = '';
						if ($request->file('image') != null) {
							$image = $request->file('image');
							$imageName = time() . $image->getClientOriginalName();
							if ($attribute->image != null && file_exists($attribute->image)) {
								if ($attribute->image != 'noimage.jpg') {
									unlink($attribute->image);
								}
							}
							$imageName = str_replace(' ', '', $imageName);
							$imageName = str_replace('.jpeg', '.jpg', $imageName);
							$image->move(public_path('uploads/vendorattributes'), $imageName);
							$attribute->image = str_replace('.jpeg', '.jpg', $imageName);
						}
						$attribute->name = $request->post('name');
						$attribute->vendor_id = $request->post('vendor_id');
						
						if ($attribute->save()) {

							
							$request->session()->flash('success', 'Attribute updated successfully');
							return redirect()->route('attributes');
						} else {
							$request->session()->flash('error', 'Something went wrong. Please try again later.');
							return redirect()->route('attributes');
						}
					} catch (Exception $e) {
						$request->session()->flash('error', 'Something went wrong. Please try again later.');
						return redirect()->route('attributes');
					}
				}
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('attributes');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('attributes');
		}
	}

	// activate/deactivate SplashScreen
	public function updateStatus(Request $request) {

		if (isset($request->statusid) && $request->statusid != null) {
			$attribute = VendorAttribute::find($request->statusid);

			if (isset($attribute->id)) {
				$attribute->status = $request->status;
				if ($attribute->save()) {
					$request->session()->flash('success', 'Attribute updated successfully.');
					return redirect()->back();
				} else {
					$request->session()->flash('error', 'Unable to update Attribute. Please try again later.');
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
			$attribute = VendorAttribute::find($request->statusid);

			if (isset($attribute->id)) {
				$attribute->status = $request->status;
				if ($attribute->save()) {
					echo json_encode(['status' => 1, 'message' => 'Attribute status updated successfully.']);
				} else {
					echo json_encode(['status' => 0, 'message' => 'Unable to update Attribute. Please try again later.']);
				}
			} else {
				echo json_encode(['status' => 0, 'message' => 'Invalid Attribute']);
			}
		} else {
			echo json_encode(['status' => 0, 'message' => 'Invalid Attribute']);
		}

	}

	public function deleteAttributes($root, $level) {
		$child = $root->childCat;
		foreach ($child as $ch) {
			$ch->status = 'delete';
			$ch->save();
			$this->deleteAttributes($ch, ++$level);
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
			$recommendedAttribute = VendorAttribute::find($request->deleteid);
			if (isset($recommendedAttribute->id)) {
				$recommendedAttribute->status = 'delete';
				if ($recommendedAttribute->save()) {
					echo json_encode(['status' => 1, 'message' => 'Attribute deleted successfully.']);
				} else {
					echo json_encode(['status' => 0, 'message' => 'Unable to delete Attribute. Please try again later.']);
				}
			} else {
				echo json_encode(['status' => 0, 'message' => 'Invalid Attribute']);
			}
		} else {
			echo json_encode(['status' => 0, 'message' => 'Invalid Attribute']);
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
				$recommendedAttribute = VendorAttribute::find($id);
				if (isset($recommendedAttribute->id)) {
					$recommendedAttribute->status = 'delete';
					if ($recommendedAttribute->save()) {
						$count++;
					}
				}
			}
			if ($count == $ids) {
				echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Attribute deleted successfully.']);
			} else {
				echo json_encode(["status" => 0, 'message' => 'Not all Attribute were deleted. Please try again later.']);
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
				$recommendedAttribute = VendorAttribute::find($id);
				if (isset($recommendedAttribute->id)) {
					if ($recommendedAttribute->status == 'active') {
						$recommendedAttribute->status = 'inactive';
					} elseif ($recommendedAttribute->status == 'inactive') {
						$recommendedAttribute->status = 'active';
					}
					if ($recommendedAttribute->save()) {
						$count++;
					}
				}
			}
			if ($count == $ids) {
				echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Attribute status updated successfully.']);
			} else {
				echo json_encode(["status" => 0, 'message' => 'Not all Attribute were updated. Please try again later.']);
			}
		} else {
			echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
		}
	}

}
