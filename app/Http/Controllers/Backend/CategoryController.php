<?php

namespace App\Http\Controllers\Backend;
use App\Library\Helper;
use App\Library\Notify;    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Category;
use App\Gift;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use App\State;
use App\City;
use App\Region;

use Illuminate\Support\Arr;

class CategoryController extends Controller {
	public $categories;
	public $columns;

	public function __construct() {
		$this->categories = new Category;
		$this->columns = [
			"select", "name","created_at"
		];

		// $this->middleware('permission:category-list|category-edit', ['only' => ['index','update']]);
        // $this->middleware('permission:category-edit', ['only' => ['edit','update']]);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		return view('backend.categories.index');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function categoriesAjax(Request $request) {

		if (isset($request->search['value'])) {
            $request->search = $request->search['value'];
        }else{
            $request->search = $request->search ?? '';
        }
		if (isset($request->order[0]['column'])) {
			$request->order_column = $request->order[0]['column'];
			$request->order_dir = $request->order[0]['dir'];
		}
		$records = $this->categories->fetchCategory($request, $this->columns);
		// dd($records);
		$total = $records->get();
		if (isset($request->start)) {
			$categories = $records->offset($request->start)->limit($request->length)->get();
		} else {
			$categories = $records->offset($request->start)->limit(count($total))->get();
		}
		// echo $total;
		$result = [];
		$i = 1;
		foreach ($categories as $list) {
			$data = [];
			$data['sno'] = $i++;
		   $data['select'] = '<div class="form-check form-check-flat"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="category_id[]" value="' . $list->id . '"><i class="input-helper"></i></label></div>';
			
			$data['name'] = $list->name;
			
			// $data['status'] = ucfirst(config('constants.STATUS.' . $list->status));
            $data['activate'] = '<div class="bt-switch"><div class=""><input type="checkbox"' . ($list->status == 'active' ? ' checked' : '') . ' data-id="' . $list->id . '" data-code="' . $list->name . '" data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" class="statusCategory"></div></div>';

			$data['created_at'] =  date('d-m-Y', strtotime($list->created_at));

			$action = '<div class="actionBtn ">';

			if (Helper::checkAccess(route('editCategories'))) {
				$action .= '<a href="' . route('editCategories', ['id' => $list->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
			}
		//  $action .= '&nbsp;&nbsp;&nbsp;<a href="javascript:;" class="toolTip deleteCategory" data-toggle="tooltip" data-id="' . $list->id . '" data-placement="bottom" title="Delete"><i class="fa fa-times"></i></a>';
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
		$url = route('storeCategories');
		$categories = new Category;
		$categorieLists = Category::where("status", "active")->where('parent_id','0')->get(['id',"name"]);
        return view('backend.categories.create', compact('type','categories','url', 'categorieLists'));
	}

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
			'name' => 'Category Name',
		];

		$validate->setAttributeNames($attr);

		if ($validate->fails()) {
			return redirect()->back()->withInput($request->all())->withErrors($validate);
		} else {
			  try {

				$categories = new Category();
				$categories->name = $request->name;
				$categories->parent_id = $request->parent_id;
				if ($request->hasfile('category_image')) {
                    $file = $request->file('category_image');
                    $filename = time() . $file->getClientOriginalName();
                    $filename = str_replace(' ', '', $filename);
                    $filename = str_replace('.jpeg', '.jpg', $filename);
                    $file->move(public_path('img/categories'), $filename);
                    $categories->image = $filename;
                }
				if ($categories->save()) {
                    $request->session()->flash('success', 'Category added successfully');
                    return redirect()->route('categories');
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
			$categories = $this->categories::where('id', $id)->first();
			if (isset($categories->id)) {
				return view('backend.categories.view', compact('categories'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('categories');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('categories');
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
			$url=	route('updateCategories',['id'=>$id ?? '']);

			$categories = $this->categories::where('id', $id)->first();

			$categorieLists = Category::where("status", "active")->where('parent_id','0')->get(['id',"name"]);

			if (isset($categories->id)) {
				$type = 'Edit';
				return view('backend.categories.create', compact('categories', 'type','url', 'categorieLists'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('categories');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('categories');
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

			$categories = $this->categories::where('id', $id)->first();
			// dd($categories);

				if (isset($categories->id)) {
				try {
					$categories->name = $request->name;
					$categories->parent_id = $request->parent_id;
				 	if ($request->hasfile('category_image')) {
                        $file = $request->file('category_image');
                        $filename = time() . $file->getClientOriginalName();
                        $filename = str_replace(' ', '', $filename);
                        $filename = str_replace('.jpeg', '.jpg', $filename);
                        $file->move(public_path('img/categories'), $filename);
                        if ($categories->image != null && file_exists(public_path('img/categories/' . $user->image))) {
                            if ($categories->image != 'noimage.jpg') {
                                unlink(public_path('img/categories/' . $categories->image));
                            }
                        }
                        $categories->image = $filename;
                    }
					$categories->save();
					$request->session()->flash('success', 'Category updated successfully');
					return redirect()->route('categories');

				} catch (Exception $e) {
					$request->session()->flash('error', 'Something went wrong. Please try again later.');
					return redirect()->route('categories');
				}

			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('categories');
			}
			
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('categories');
		}

	}

	

	// public function checkCategoryName(Request  $request ,$id = null){
		 	
    //   	 if (isset($request->name)) {
	// 	    $check = $this->categories::where('name', $request->name)->where("status","!=","delete");
	// 		// dd($check);
    //         if (isset($id) && $id != null) {
    //             $check = $check->where('id', '!=', $id);
    //         }
    //         $check = $check->count();
    //         if ($check > 0) {
    //             return "false";
    //         } else {
    //             return "true";
    //         }
    //     } else {
    //         return "true";
    //     }
	// }
	
	/**
	 * Remove the specified FQA from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */

	 public function destroy(Request $request) {
        if (isset($request->deleteid) && $request->deleteid != null) {
            $categories = Category::find($request->deleteid);

            if (isset($categories->id)) {
                $categories->status = 'delete';
                if ($categories->save()) {
                 
                    Gift::where("gift_category_id",$categories->id)->update(['status'=>'delete']);
                 

                    echo json_encode(["status" => 1, 'ids' => json_encode($request->deleteid), 'message' => 'Category deleted successfully.']);
                } else {
                    echo json_encode(["status" => 0, 'message' => 'Not all categories were deleted. Please try again later.']);
                }
            } else {
                echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
            }
        } else {
            echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
        }
    }


	  public function bulkdelete(Request $request) {

        if (isset($request->deleteid) && $request->deleteid != null) {
            $deleteid = explode(',', $request->deleteid);
            $ids = count($deleteid);
            $count = 0;
            foreach ($deleteid as $id) {
                $categories = Category::find($id);

                if (isset($categories->id)) {
                    $categories->status = 'delete';
                    if ($categories->save()) {
						 Gift::where("gift_category_id",$categories->id)->update(['status'=>'delete']);
                        // DB::table('model_has_roles')->where('model_id',$user->id)->delete();
                        // Category::where("gift_category_id",$gift->id)->update(['status'=>'delete']);
                        $count++;
                    }
                }
            }
            if ($count == $ids) {
                echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Category deleted successfully.']);
            } else {
                echo json_encode(["status" => 0, 'message' => 'Not all Category were deleted. Please try again later.']);
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
                $categories = Category::find($id);

                if (isset($categories->id)) {
                    if ($categories->status == 'active') {
                        $categories->status = 'inactive';
                    } elseif ($categories->status == 'inactive') {
                        $categories->status = 'active';
                    }

                    if ($categories->save()) {
                    	 Gift::where("gift_category_id",$categories->id)->update(['status'=>'delete']);
                		//  $getStateId = State::where("country_id",$countries->id)->pluck("id")->toArray();
            			//  City::whereIn("state_id",$getStateId)->update(['status'=>$countries->status]);
        			  	// $getCityId = City::whereIn("state_id",$getStateId)->pluck("id")->toArray();
                       	//  Region::whereIn("city_id",$getCityId)->update(['status'=>$countries->status]);
                        $count++;
                    }
                }
            }
            if ($count == $ids) {
                echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Category status updated successfully.']);
            } else {
                echo json_encode(["status" => 0, 'message' => 'Not all Category were updated. Please try again later.']);
            }
        } else {
            echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
        }
	}

 	// activate/deactivate Country
    public function updateStatusAjax(Request $request) {

        if (isset($request->statusid) && $request->statusid != null) {
            $categories = Category::find($request->statusid);

            if (isset($categories->id)) {
                $categories->status = $request->status;
                if ($categories->save()) {
					//   $request->session()->flash('success', 'Category updated successfully.');
                    // return redirect()->back();
                	// State::where("country_id",$categories->id)->update(['status'=>$countries->status]);

                	// $getStateId = State::where("country_id",$countries->id)->pluck("id")->toArray();
                	
                	// City::whereIn("state_id",$getStateId)->update(['status'=>$countries->status]);
            	 	
            	 	// $getCityId = City::whereIn("state_id",$getStateId)->pluck("id")->toArray();

                	// Region::where("country_id",$countries->id)
                	// ->whereIn("state_id",$getStateId)
                	// ->whereIn('city_id',$getCityId)
                	// ->update(['status'=>$countries->status]);
                    echo json_encode(['status' => 1, 'message' => 'Category status updated successfully.']);
                } else {
                    echo json_encode(['status' => 0, 'message' => 'Unable to update Category. Please try again later.']);
                }
            } else {
                echo json_encode(['status' => 0, 'message' => 'Invalid data']);
            }
        } else {
            echo json_encode(['status' => 0, 'message' => 'Invalid data']);
        }
    }
}
