<?php
    
namespace App\Http\Controllers\Backend;
    
use App\Library\Helper;
use App\Library\Notify;    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Illuminate\Support\Arr;
    
class PermissionController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        
        $this->middleware('permission:app-permission-list|app-permission-create', ['only' => ['index','store']]);
        $this->middleware('permission:app-permission-create', ['only' => ['create','store']]);
    }

    public function index(Request $request) {
        
    }


    public function create() {

    }
    public function store(Request $request) {
        
    }
    public function show(Request $request, $id = null) {

    }
    public function edit(Request $request, $id = null) {
        
    }
    public function update(Request $request, $id = null) {
        

    }
}