@extends('layouts.admin1')
@section('nav-main')
    Manage Period
@endsection
@section('nav-main-url')
    {{route('superadmin.meter_types.index')}}
@endsection
@section('nav-current')
   Create Period
@endsection
@section('nav-current-title')
    Create New Period
@endsection
@section('content')
   
    <div class="card card-body p-2">
        <div class="row">
            <div class="col-12 col-lg-8 m-auto">
               <form action="{{ route('superadmin.tw_periods.store') }}" method="POST">
                
                    @csrf
                    @include('superadmin.tw_periods.form', ['period' => new App\Models\Tabwater\TwPeriod()])
                <button type="submit" class="btn btn-info mt-2 w-30">Create Period</button>

                </form>
            </div>
        
        </div>
        

    </div>


    

@endsection