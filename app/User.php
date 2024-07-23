<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Passport\HasApiTokens;
use DB;
use Auth;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

      /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static $withoutAppends = false;

    public function scopeWithoutAppends($query)
    {
        self::$withoutAppends = true;

        return $query;
    } 
    public function family()
    {
        return $this->hasOne(Family::class, 'id', 'family_id');
    } 

    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'store_category');
    } 
    public function restaurantcategory() 
    {
        return $this->belongsTo(RestaurantCategory::class, 'id','store_category');
    }
    public function devices() {
        return $this->hasMany(UserDevice::class, 'user_id', 'id'); 
    }
    public function voucher()
    {
        return $this->hasOne(Vouchar::class, 'vendor_id', 'id');
    }
    public function fetchAdmins($request, $columns) {
        
        $query = User::where('status', '!=', 'delete')->where('id', '!=', 1)->where('is_admin', 'Yes');

        if (isset($request->from_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
        }
        if (isset($request->end_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
        }
        if (isset($request->search) && !empty($request->search)) {
            /*$query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
                $q->orWhere('last_name', 'like', '%' . $request->search . '%');
                $q->orWhere('email', 'like', '%' . $request->search . '%');
                $q->orWhere('mobile',  $request->search);
            });*/

            $string = $request->search;

            $query->where(function ($q) use ($string) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower(trim($string)) . '%'])
                    ->orWhere('email', 'ILIKE', '%' . trim($string) . '%') // ILIKE for case-insensitive search
                    ->orWhere('mobile', 'LIKE', '%' . trim($string) . '%');
            });
        }
        if (isset($request->status)) {
            $query->where('status', $request->status);
        }
          if (isset($request->order_column)) {
            $users = $query->orderBy($columns[$request->order_column], $request->order_dir);
        } else {
            $users = $query->orderBy('created_at', 'desc');
        }
        
        return $users;
    }
    public function fetchUsers($request, $columns) {
        $query = User::where('status', '!=', 'delete')->where('id', '!=', 1)->where('is_admin', 'No')->where('role','Customer')->orderBy('id', 'desc');

        if (isset($request->from_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
        }
        if (isset($request->end_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
        }
        // if (isset($request->search)) {
        //     $query->where(function ($q) use ($request) {
        //         $q->where('name', 'like', '%' . $request->search . '%');
        //         $q->orWhere('last_name','like', '%' . $request->search . '%');
        //         $q->orWhere('email', 'like', '%' . $request->search . '%');
        //          $q->orWhere('mobile', 'like', '%' . $request->search . '%');
        //     });
        // }
       
        if (isset($request->status)) {
            $query->where('status', $request->status);
        }
        
        if (isset($request->order_column)) {
            if($request->order_column == 8){
                 $users = $query->orderBy('created_at', $request->order_dir);
            }else{
                $users = $query->orderBy($columns[$request->order_column], $request->order_dir);
            }
        }else {
            $users = $query->orderBy('created_at', 'desc');
        }
        return $users;
    }

    public function fetchVendors($request, $columns) {
       $query = User::where('status', '!=', 'delete')->where('id', '!=', 1)->where('is_admin', 'No')->where('role', 'Vendor')->orderBy('id', 'desc');

        if (isset($request->from_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
        }
        if (isset($request->end_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
        }
        if (isset($request->search) && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
                $q->orWhere('store_name','like', '%' . $request->search . '%');
                $q->orWhere('email', 'like', '%' . $request->search . '%');
                 $q->orWhere('mobile', 'like', '%' . $request->search . '%');
            });
        }
       
        if (isset($request->status)) {
            $query->where('status', $request->status);
        }
        
        if (isset($request->order_column)) {
            if($request->order_column == 7){
                 $users = $query->orderBy('created_at', $request->order_dir);
            }else{
                $users = $query->orderBy($columns[$request->order_column], $request->order_dir);
            }
        }else {
            $users = $query->orderBy('created_at', 'desc');
        }
        return $users;
    }

    public function fetchKitchens($request, $columns) {
       $query = User::where('status', '!=', 'delete')->where('id', '!=', 1)->where('is_admin', 'No')->where('parent_id', Auth::user()->id)->where('role', 'Kitchen')->orderBy('id', 'desc');

        if (isset($request->from_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
        }
        if (isset($request->end_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
        }
        if (isset($request->status)) {
            $query->where('status', $request->status);
        }

        if (isset($request->search) && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
                $q->orWhere('email', 'like', '%' . $request->search . '%');
                $q->orWhere('mobile', 'like', '%' . $request->search . '%');
            });
        }

        if (isset($request->order_column)) {
            if($request->order_column == 8){
                 $users = $query->orderBy('users.created_at', $request->order_dir);
            }else{
                $users = $query->orderBy($columns[$request->order_column], $request->order_dir);
            }
        }else {
            $users = $query->orderBy('created_at', 'desc');
        }

        // Now, let's add a count of related restaurant_order_items for each user
        $users = $query->withCount(['restaurantOrderItems' => function ($q) {
            $q->whereColumn('vendor_id', 'users.parent_id');
        }]);
        // dd($users);

        return $users;
    }

    public function fetchTablets($request, $columns) {
       $query = User::where('status', '!=', 'delete')->where('id', '!=', 1)->where('is_admin', 'No')->where('parent_id', Auth::user()->id)->where('role', 'Tablet')->orderBy('id', 'desc');

        if (isset($request->from_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
        }
        if (isset($request->end_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
        }
        if (isset($request->status)) {
            $query->where('status', $request->status);
        }
        
        if (isset($request->order_column)) {
            if($request->order_column == 7){
                 $users = $query->orderBy('created_at', $request->order_dir);
            }else{
                $users = $query->orderBy($columns[$request->order_column], $request->order_dir);
            }
        }else {
            $users = $query->orderBy('created_at', 'desc');
        }
        return $users;
    }

    public function fetchVendorProducts($request, $columnitem) {
        $query = VendorProduct::where('status', '!=', 'delete');
        if (isset($request->from_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
        }
        if (isset($request->end_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
        }
        if (isset($request->gst)) {
            $query->where(function ($q) use ($request) {
                $q->where('gst', 'like', '%' . $request->gst . '%');
            });
        }
        // if (isset($request->name)) {
        //     $query->where(function ($q) use ($request) {
        //         $q->where('name', 'like', '%' . $request->name . '%');
        //         $q->orWhere('description', 'like', '%' . $request->name . '%');
        //     });
        // }
        if (isset($request->status) && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        if (isset($request->vendor_id) && !empty($request->vendor_id)) {
            $query->where('vendor_id', $request->vendor_id);
        }
       if (isset($request->order_column)) {
            if($request->order_column == 7){
                 $Brands = $query->orderBy('created_at', $request->order_dir);
            }else{
                $Brands = $query->orderBy($columnitem[$request->order_column], $request->order_dir);
            } 
        } else {
          $Brands = $query->orderBy('created_at', 'desc');
        }

        return $Brands;
    } 

    public function fetchVendorUsers($request, $columns) {
        $query = User::where('status', '!=', 'delete')->where('id', '!=', 1)->where('is_admin', 'No')->where('role','Customer')->orderBy('id', 'desc');

        if (isset($request->from_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
        }
        if (isset($request->end_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
        }
        // if (isset($request->search)) {
        //     $query->where(function ($q) use ($request) {
        //         $q->where('name', 'like', '%' . $request->search . '%');
        //         $q->orWhere('last_name','like', '%' . $request->search . '%');
        //         $q->orWhere('email', 'like', '%' . $request->search . '%');
        //          $q->orWhere('mobile', 'like', '%' . $request->search . '%');
        //     });
        // }
       
        if (isset($request->status)) {
            $query->where('status', $request->status);
        }
        
        if (isset($request->order_column)) {
            if($request->order_column == 9){
                 $users = $query->orderBy('created_at', $request->order_dir);
            }else{
                $users = $query->orderBy($columns[$request->order_column], $request->order_dir);
            }
        }else {
            $users = $query->orderBy('created_at', 'desc');
        }
        return $users;
    }

    
    public function fetchVendorSubscriptions($request, $columnsub) {
        $query = UserSubscription::where('status', '!=', 'delete');
         if (isset($request->from_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
        }
        if (isset($request->end_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
        }

        if (isset($request->name)) {
            $query->where(function ($q) use ($request) {   
                $q->orWhere('name', 'like', '%' . $request->name . '%');
                $q->orWhere('price', 'like', '%' . $request->search . '%');
                $q->orWhere('days', 'like', '%' . $request->search . '%');
                $q->orWhere('type', 'like', '%' . $request->search . '%');
                $q->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }
        if(isset($request->status)) {
            $query->where('status', $request->status);
        }
         if (isset($request->user_id) && !empty($request->user_id)) {
            $query->where('user_id', $request->user_id);
        }
        if(isset($request->order_column)) {
            if($request->order_column == 5){
                 $cms = $query->orderBy('created_at', $request->order_dir);
            }else{
                $cms = $query->orderBy($columnsub[$request->order_column], $request->order_dir);
            } 
        }else {
            $cms = $query->orderBy('name', 'asc');
        }
   
        return $cms;
    }

    public function fetchTransactions($request, $columndata) {
        $query = Transaction::where('status', '!=', 'delete');
        if (isset($request->from_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
        }
        if (isset($request->end_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
        }
        if (isset($request->payment_type)) {
            $query->where(function ($q) use ($request) {
                $q->where('order_id', 'like', '%' . $request->search . '%');
		        $q->orWhere('transaction_no', 'like', '%' . $request->search . '%');
	            $q->orWhere('payment_type', 'like', '%' . $request->search . '%');
	            $q->orWhere('title', 'like', '%' . $request->search . '%');
	            $q->orWhere('amount', 'like', '%' . $request->search . '%');
            });
        }
    
        if (isset($request->status)) {
            $query->where('status', $request->status);
        }
         if (isset($request->user_id) && !empty($request->user_id)) {
            $query->where('user_id', $request->user_id);
        }
         if (isset($request->order_column)) {
            if($request->order_column == 8){
                 $Brands = $query->orderBy('created_at', $request->order_dir);
            }else{
                $Brands = $query->orderBy($columndata[$request->order_column], $request->order_dir);
            } 
        } else {
          $Brands = $query;
        }
      
        return $Brands;
    } 


   public function fetchCustomers($request, $columns) {
    $users = Auth::id();
    $query = VendorCustomer::select('id', 'vendor_id', 'customer_id', 'type', 'category_id', 'feedback', DB::raw("DATE_FORMAT(created_at,'%b %d, %Y') as created_at"))
        ->where('vendor_id', $users)
        ->with(['customer' => function ($q) {
            $q->select('id', 'name', 'phone_code', 'mobile');
        }])
        ->whereHas('customer', function ($q) use ($request) {
            if (isset($request->search) && !empty($request->search)) {
                    $q1->where('users.name', 'like', '%' . $request->search . '%')
                    ->orWhere('users.mobile', 'like', '%' . $request->search . '%'); 
            }
        });
    
    if (isset($request->type)) {
        $query->where('type', $request->type);
    }

    if (isset($request->from_date) && !empty($request->from_date)) {
        $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
    }

    if (isset($request->end_date) && !empty($request->end_date)) {
        $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
    }

    if (isset($request->order_column) && $request->order_column != null) {
        if ($request->order_column == 7) {
            $query->orderBy('created_at', $request->order_dir);
        } else {
            $query->orderBy($columns[$request->order_column], $request->order_dir);
        }
    } else {
        $query->orderBy('created_at', 'desc'); // Order by created_at in descending order
    }

    return $query; 
}
 
    public function getNameAttribute($details)
    {
        $res = '';
        if (!empty($details)) {
            $res = $details;
        }
        return $res;
    }
    public function getPhoneCodeAttribute($details)
    {
        $res = '';
        if (!empty($details)) {
            $res = $details;
        }
        return $res;
    }
    public function getMobileAttribute($details)
    {
        $res = '';
        if (!empty($details)) {
            $res = $details;
        }
        return $res;
    }
   
    public function getEmailAttribute($details)
    {
        $res = '';
        if (!empty($details)) {
            $res = $details;
        }
        return $res;
    } 
    public function getPasswordAttribute($details)
    {
        $res = '';
        if (!empty($details)) {
            $res = $details;
        }
        return $res;
    }
    public function getAddressAttribute($details)
    {
        $res = '';
        if (!empty($details)) {
            $res = $details;
        }
        return $res;
    }
    public function getLatitudeAttribute($details)
    {
        $res = '';
        if (!empty($details)) {
            $res = $details;
        }
        return $res;
    }
    public function getLongitudeAttribute($details)
    {
        $res = '';
        if (!empty($details)) {
            $res = $details;
        }
        return $res;
    }
    public function getGstNumberAttribute($details)
    {
        $res = '';
        if (!empty($details)) {
            $res = $details;
        }
        return $res;
    }
    public function getDobAttribute($details)
    {
        $res = '';
        if (!empty($details)) {
            $res = $details;
        }
        return $res;
    }
    public function getAvatarAttribute($details)
    {
        if ($details != '') {
            return asset('img/avatars').'/'.$details;
        }
        return asset('images/no_avatar.jpg');
    } 
    public function getImageAttribute($details)
    {
         if ($details != '') {
            return asset('img/avatars').'/'.$details;
        }
        return asset('images/no_avatar.jpg');
    }

    public function getStoreLogoAttribute($details)
    {
         if ($details != '') {
            return asset('img/storelogos').'/'.$details;
        }
        return asset('images/no_logo.png');
    }
    public function getCountry() {
        return $this->belongsTo(Country::class, "country_id", "id");
    }
    public function getState() {
        return $this->belongsTo(State::class, "state_id", "id");
    }
    public function getCities() {
        return $this->belongsTo(City::class, "city_id", "id");
    }
    public function getRegion() {
        return $this->belongsTo(Region::class, "region_id", "id");
    }
    public function getOtp() {
        return $this->belongsTo(OTP::class, "user_id", "id");
    } 
    
    public function boxpurchase()
    {
        return $this->belongsToMany(BoxPurchase::class);
    }

    public function contest()
    {
        return $this->belongsToMany(ContestParticipant::class);
    }

    public function boxprice()
    {
        return $this->belongsToMany(BoxPrice::class);
    }

    public function Purchases()
    {
        return $this->hasMany(BoxPurchase::class);
    }

    // public function notifications()
    // {
    //     return $this->hasMany(Notificationuser::class, 'receiver_id');
    // }

    public function participatedUsers()
    {
        return $this->hasMany(ContestJoin::class, 'user_id');
    }


    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class, 'id','user_id');
    }   

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'store_category');
    }

    public function restaurantOrderItems()
    {
        return $this->hasMany(RestaurantOrderItem::class, 'vendor_id','parent_id');
    }
    
    
}

