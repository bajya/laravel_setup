@extends('layouts.backend.app')
@section('title', 'Transaction - ' . ucfirst($transaction->name))

@section('content')
    <div class="content-wrapper">

        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">{{ucfirst($type)}} Transaction</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('transactions')}}">Transaction</a></li>
                    <li class="breadcrumb-item active">View Transaction</li>
                </ol>
            </div>
        </div>
        <!-- Start Page Content -->
        <div class="row">
            @include('layouts.backend.message')
        
            <div class="col-lg-12 col-xlg-9 col-md-12">
                <div class="card">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs profile-tab" role="tablist">
                        <!-- <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#profile" role="tab">Details</a> </li>
                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#settings" role="tab">Settings</a> </li> -->
                        
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane active" id="profile" role="tabpanel">
                            <div class="card-body p-3">
                                <div>
                                    <div class="view_inner_cls">
                                        <h5 class="p-t-20 db">Vendor Store Name</h5><small class="text-success db">{{ $transaction->getUser->store_name }}</small>
                                    </div>
                                    <div class="view_inner_cls">
                                        <h5 class="p-t-20 db">Transaction Id</h5><small class="text-success db">{!! $transaction->transaction_no !!}</small>
                                    </div>
                                     <div class="view_inner_cls">
                                        <h5 class="p-t-20 db">Payment Type</h5><small class="text-success db">{!! $transaction->payment_type !!}</small>
                                    </div>
                                    <div class="view_inner_cls">
                                        <h5 class="p-t-20 db">Amount</h5><small class="text-success db">INR {!! $transaction->amount !!}</small>
                                    </div>
                                    <div class="view_inner_cls">
                                        <h5 class="p-t-20 db">Created On</h5><small class="text-success db">{{date('Y, M d', strtotime($transaction->created_at))}}</small>
                                    </div>
                                    <!-- <div class="view_inner_cls">
                                        <h5 class="p-t-20 db">Status</h5><small class="text-success db">{{ucfirst(config('constants.STATUS.'.$transaction->status))}}</small>
                                    </div> -->
                                   
                                    
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="settings" role="tabpanel">
                            <div class="card-body p-3">
                                <form class="form-horizontal form-material" method="post" action="">
                                    {{csrf_field()}}
                                    <div class="form-group bt-switch">
                                        <div class="col-md-6">
                                            <label class="col-md-6" for="val-block">Status</label>
                                            <input type="hidden" name="statusid" value="{{$transaction->id}}">
                                            <input type="hidden" name="status" value="{{$transaction->status}}">
                                           
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <button type="submit" class="btn btn-success">Update</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>


        <!-- End PAge Content -->
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function(){
            
            $('#statusBoxPrice').on('switchChange.bootstrapSwitch', function (event, state) {

                var x = $(this).data('on-text');
                var y = $(this).data('off-text');
                if($("#statusBoxPrice").is(':checked'))
                    $('input[name=status]').val('active');
                else
                    $('input[name=status]').val('inactive');
            });
        });
    </script>
@endpush
