<?php

namespace App\Http\Controllers\Backend;
use App\Library\Helper;
use App\Library\Notify;    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Testimonial;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Image;
use URL; 
use Auth; 
use File; 
use Illuminate\Support\Arr;

class TestimonialController extends Controller {
	public $testimonial;
	public $columns;

	public function __construct() {
		$this->testimonial = new Testimonial;
		$this->columns = [
			"select", "s_no", "name", "image", "description", "status", "activate"
		];
		/*$this->middleware('permission:testimonial-list|testimonial-create|testimonial-edit|testimonial-delete', ['only' => ['index','store']]);
        $this->middleware('permission:testimonial-create', ['only' => ['create','store']]);
        $this->middleware('permission:testimonial-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:testimonial-delete', ['only' => ['destroy']]);*/
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		$count = Testimonial::where('status', '!=', 'delete')->count();
		return view('backend.testimonials.index', compact('count'));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function testimonialAjax(Request $request) {
		if (isset($request->search['value'])) {
            $request->search = $request->search['value'];
        }else{
            $request->search = '';
        }
		if (isset($request->order[0]['column'])) {
			$request->order_column = $request->order[0]['column'];
			$request->order_dir = $request->order[0]['dir'];
		}
		$records = $this->testimonial->fetchTestimonials($request, $this->columns);
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
			$data['select'] = '<div class="form-check form-check-flat"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="testimonial_id[]" value="' . $cat->id . '"><i class="input-helper"></i></label></div>';
			$data['sno'] = $request->start + $i++;
			$data['name'] = ($cat->name != null) ? \Str::limit($cat->name, 50, '...') : '-'; 
			$data['image'] = ($cat->image != null) ? '<img src="'.$cat->image.'" width="70" />' : '-';
			$data['description'] = ($cat->description != null) ? \Str::limit($cat->description, 50, '...') : '-';
			$data['status'] = ucfirst(config('constants.STATUS.' . $cat->status));
			$data['created_at'] =  date('d-m-Y', strtotime($cat->created_at));
			$data['activate'] = '<div class="bt-switch"><div class="col-md-2"><input type="checkbox"' . ($cat->status == 'active' ? ' checked' : '') . ' data-id="' . $cat->id . '" data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" class="statusTestimonial"></div></div>';
			
			$action = '';
			
			if (Helper::checkAccess(route('editTestimonial'))) {
				$action .= '<a href="' . route('editTestimonial', ['id' => $cat->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
			}
			$action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('viewTestimonial', ['id' => $cat->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';
			/*if (Helper::checkAccess(route('deleteTestimonial'))) {
				$action .= '&nbsp;&nbsp;&nbsp;<a href="javascript:;" class="toolTip deleteTestimonial" data-toggle="tooltip" data-placement="bottom" data-id="' . $cat->id . '" title="Delete"><i class="fa fa-times"></i></a>';
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
		$url = route('addTestimonial');
		$testimonial = new Testimonial;
		return view('backend.testimonials.create', compact('type', 'url', 'testimonial'));
	}

	/**
	 * check for unique testimonial
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function checkTestimonial(Request $request, $id = null) {
		if (isset($request->testimonial_name)) {
			$check = Testimonial::where('name', $request->testimonial_name);
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
			'testimonial_name' => 'required',
			'description' => 'required',
			'testimonial_image' => 'mimes:jpeg,png,jpg,gif,svg',
		]);
		$attr = [
			'testimonial_name' => 'Testimonial Name',
			'description' => 'Description',
			'testimonial_image' => 'Image',
		];
		$validate->setAttributeNames($attr);
		if ($validate->fails()) {
			return redirect()->route('createTestimonial')->withInput($request->all())->withErrors($validate);
		} else {
			try {
				if(!File::exists(public_path().'/uploads/testimonials')) {
		            File::makeDirectory(public_path().'/uploads/testimonials', 0777, true, true);
		        }
				$testimonial = new Testimonial;
				$imageName = '';
				if ($request->file('testimonial_image') != null) {
					$image = $request->file('testimonial_image');
					$imageName = time() . $image->getClientOriginalName();
					$imageName = str_replace(' ', '', $imageName);
					$imageName = str_replace('.jpeg', '.jpg', $imageName);
					$image->move(public_path('uploads/testimonials'), $imageName);
					$imageName = str_replace('.jpeg', '.jpg', $imageName);
				}
				$testimonial->name = $request->post('testimonial_name');
				$testimonial->designation = $request->post('designation');
				$testimonial->company_name = $request->post('company_name');
				$testimonial->image = str_replace('.jpeg', '.jpg', $imageName);
				$testimonial->description = $request->post('description');
				$testimonial->status = trim($request->post('status'));
				$testimonial->created_at = date('Y-m-d H:i:s');
				if ($testimonial->save()) {
					
					$request->session()->flash('success', 'Testimonial added successfully');
					return redirect()->route('testimonials');
				} else {
					$request->session()->flash('error', 'Something went wrong. Please try again later.');
					return redirect()->route('testimonials');
				}
			} catch (Exception $e) {
				$request->session()->flash('error', 'Something went wrong. Please try again later.');
				return redirect()->route('testimonials');
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
			$testimonial = Testimonial::where('id', $id)->first();
			if (isset($testimonial->id)) {
				return view('backend.testimonials.view', compact('testimonial', 'type'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('testimonials');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('testimonials');
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
            $testimonial = Testimonial::where('id', $id)->first();
            if (isset($testimonial->id)) {
                $type = 'edit';
                $url = route('updateTestimonial', ['id' => $testimonial->id]);
                return view('backend.testimonials.create', compact('testimonial', 'type', 'url'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('testimonials');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('testimonials');
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
			$testimonial = Testimonial::where('id', $id)->first();
			if (isset($testimonial->id)) {
				$validate = Validator($request->all(), [
					'testimonial_name' => 'required',
					'description' => 'required',
				]);
				$attr = [
					'testimonial_name' => 'Testimonial Name',
					'description' => 'Description',
				];
				$validate->setAttributeNames($attr);
				if ($validate->fails()) {
					return redirect()->route('createTestimonial')->withInput($request->all())->withErrors($validate);
				} else {
					try {
						if(!File::exists(public_path().'/uploads/testimonials')) {
				            File::makeDirectory(public_path().'/uploads/testimonials', 0777, true, true);
				        }
						$imageName = '';
						if ($request->file('testimonial_image') != null) {
							$image = $request->file('testimonial_image');
							$imageName = time() . $image->getClientOriginalName();
							if ($testimonial->image != null && file_exists($testimonial->image)) {
								if ($testimonial->image != 'noimage.jpg') {
									unlink($testimonial->image);
								}
							}
							$imageName = str_replace(' ', '', $imageName);
							$imageName = str_replace('.jpeg', '.jpg', $imageName);
							$image->move(public_path('uploads/testimonials'), $imageName);
							$testimonial->image = str_replace('.jpeg', '.jpg', $imageName);
						}
						$testimonial->name = $request->post('testimonial_name');
						$testimonial->designation = $request->post('designation');
						$testimonial->company_name = $request->post('company_name');
						$testimonial->description = $request->post('description');
						$testimonial->status = trim($request->post('status'));

						if ($testimonial->save()) {

							
							$request->session()->flash('success', 'Testimonial updated successfully');
							return redirect()->route('testimonials');
						} else {
							$request->session()->flash('error', 'Something went wrong. Please try again later.');
							return redirect()->route('testimonials');
						}
					} catch (Exception $e) {
						$request->session()->flash('error', 'Something went wrong. Please try again later.');
						return redirect()->route('testimonials');
					}
				}
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('testimonials');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('testimonials');
		}
	}

	// activate/deactivate Testimonial
	public function updateStatus(Request $request) {

		if (isset($request->statusid) && $request->statusid != null) {
			$testimonial = Testimonial::find($request->statusid);

			if (isset($testimonial->id)) {
				$testimonial->status = $request->status;
				if ($testimonial->save()) {
					$request->session()->flash('success', 'Testimonial status updated successfully.');
					return redirect()->back();
				} else {
					$request->session()->flash('error', 'Unable to update Testimonial. Please try again later.');
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
  
	// activate/deactivate Testimonial
	public function updateStatusAjax(Request $request) {

		if (isset($request->statusid) && $request->statusid != null) {
			$testimonial = Testimonial::find($request->statusid);

			if (isset($testimonial->id)) {
				$testimonial->status = $request->status;
				if ($testimonial->save()) {
					echo json_encode(['status' => 1, 'message' => 'Testimonial status updated successfully.']);
				} else {
					echo json_encode(['status' => 0, 'message' => 'Unable to update Testimonial. Please try again later.']);
				}
			} else {
				echo json_encode(['status' => 0, 'message' => 'Invalid Testimonial']);
			}
		} else {
			echo json_encode(['status' => 0, 'message' => 'Invalid Testimonial']);
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
			$recommendedTestimonial = Testimonial::find($request->deleteid);
			if (isset($recommendedTestimonial->id)) {
				$recommendedTestimonial->status = 'delete';
				if ($recommendedTestimonial->save()) {
					echo json_encode(['status' => 1, 'message' => 'Testimonial deleted successfully.']);
				} else {
					echo json_encode(['status' => 0, 'message' => 'Unable to delete Testimonial. Please try again later.']);
				}
			} else {
				echo json_encode(['status' => 0, 'message' => 'Invalid Testimonial']);
			}
		} else {
			echo json_encode(['status' => 0, 'message' => 'Invalid Testimonial']);
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
				$recommendedTestimonial = Testimonial::find($id);
				if (isset($recommendedTestimonial->id)) {
					$recommendedTestimonial->status = 'delete';
					if ($recommendedTestimonial->save()) {
						$count++;
					}
				}
			}
			if ($count == $ids) {
				echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Testimonial deleted successfully.']);
			} else {
				echo json_encode(["status" => 0, 'message' => 'Not all Testimonial were deleted. Please try again later.']);
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
				$recommendedTestimonial = Testimonial::find($id);
				if (isset($recommendedTestimonial->id)) {
					if ($recommendedTestimonial->status == 'active') {
						$recommendedTestimonial->status = 'inactive';
					} elseif ($recommendedTestimonial->status == 'inactive') {
						$recommendedTestimonial->status = 'active';
					}
					if ($recommendedTestimonial->save()) {
						$count++;
					}
				}
			}
			if ($count == $ids) {
				echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Testimonial updated successfully.']);
			} else {
				echo json_encode(["status" => 0, 'message' => 'Not all Testimonials were updated. Please try again later.']);
			}
		} else {
			echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
		}
	}

}
