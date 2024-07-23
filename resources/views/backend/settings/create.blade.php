@extends('layouts.backend.app')
@section('title', 'Setting')

@section('content')
    <div class="main-body">
        <!-- Start Page Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include('layouts.backend.message')
                    <div class="card-body p-3">
                        <h4>Fill In Setting Details</h4>
                        <hr>
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form class="form-material row form-valide" method="post" action="{{ route('updateSetting') }}" enctype="multipart/form-data">
                            {{csrf_field()}}
                            <input type="hidden" class="form-control form-control-line" name="total_val" value="{{count($settings)}}" required>
                            @foreach($settings as $key=>$value)
                                <div class="form-group col-md-6">
                                    <label>{{ $value->name }}</label><sup class="text-reddit"> *</sup>
                                    <input type="hidden" class="form-control form-control-line" name="id_{{$key}}" value="{{$value->id}}" min="1" required>
                                    @if($value->type == 'number')
                                        <input type="number" class="form-control form-control-line" name="value_{{$key}}" value="{{$value->rule_value}}" min="{{$value->rule_min}}" required >

                                    @elseif($value->type == 'text')
                                        <input type="text" class="form-control form-control-line" name="value_{{$key}}" value="{{$value->rule_value}}" required >
                                    @elseif($value->type == 'email')
                                        <input type="email" class="form-control form-control-line" name="value_{{$key}}" value="{{$value->rule_value}}" required >
                                    @elseif($value->type == 'dropdown')    
                                        <select class="form-control form-control-line" name="value_{{$key}}" id="{{$value->rule_value}}">
                                            <option value="Yes" @if('Yes'==$value->rule_value) selected @endif>Yes</option>
                                            <option value="No" @if('No'==$value->rule_value) selected @endif>No</option>
                                        </select>
                                    @else
                                        <textarea class="form-control form-control-line" name="value_{{$key}}" rows="5">{{ $value->rule_value }}</textarea>
                                    @endif
                                </div>
                            @endforeach
                            <div class="col-12">
                                <button type="submit" class="btn btn-success submitBtn m-r-10">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End PAge Content -->
    </div>

@endsection

@push('scripts')

@endpush