@extends('layouts.adminlte')

@section('content')
set_items_pricepoint
@foreach ($items as $item)
<div class="row">
    <div class="col-6">{{$item->kp_itemscode}}</div> 
    <div class="col-6">{{$item->kp_itemsname}}</div>
</div>
   
    
@endforeach
@endsection