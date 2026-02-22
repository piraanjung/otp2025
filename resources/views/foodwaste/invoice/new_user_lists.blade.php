@extends('layouts.adminlte')
@section('style')
<style>
    .checked_all{
        margin-left: -18rem;
        position: absolute;
    }
    </style>
@endsection
@section('content')

<form action="{{ url('invoice/zone_create_for_new_users') }}" method="post">
    @csrf
    
    <div class="card card-widget widget-user col-md-8">
        <!-- Add the bg color to the header using any of the bg-* classes -->
        <div class="widget-user-header bg-info">
            <h3 class="widget-user-username">รายชื่อผู้ใช้น้ำที่เพิ่มระหว่างรอบบิล</h3>
            <h5 class="widget-user-desc">เส้นทาง {{$newUsers[0]->subzone->subzone_name}}
                <input type="hidden" name="undertake_subzone_id" value="{{$newUsers[0]->undertake_subzone_id}}">
            </h5>
            <input type="checkbox" class="form-control checked_all" checked>

        </div>
        <div class="widget-user-image">
            <img class="img-circle elevation-2" src="{{asset('/adminlte/dist/img/users2.png')}}" alt="User Avatar">
        </div>
        <div class="card-footer">
            <?php $i =0;?>
            @foreach ($newUsers as $item)
            <div class="row">
                <div class="col-sm-2 border-right">
                    <div class="description-block">
                        <span class="description-text">
                            <input type="checkbox" class="form-control checkbox" name="new_users[{{$i++}}][user_id]" value="{{$item->user_id}}" checked>
                        </span>
                    </div>
                    <!-- /.description-block -->
                </div>
                <!-- /.col -->
                <div class="col-sm-2 border-right">
                    <div class="description-block">
                        <span class="description-text h6">
                            {{$item->meternumber }}
                        </span>
                    </div>
                    <!-- /.description-block -->
                </div>
                <div class="col-sm-4 border-right">
                    <div class="description-block">
                        <span class="description-text h6">
                            {{$item->user_profile->name }}
                        </span>
                    </div>
                    <!-- /.description-block -->
                </div>
                <div class="col-sm-4 border-right">
                    <div class="description-block">
                        <span class="description-text h6">
                            {{'บ้านเลขที่ '.$item->user_profile->address }}
                        </span>
                    </div>
                    <!-- /.description-block -->
                </div>
                <!-- /.col -->

                <!-- /.col -->
            </div>
            <!-- /.row -->
            @endforeach

        </div>
    </div>
    <div class="row">
        <div class=" col-md-8 ">
            <input type="submit" class="btn btn-success float-right" value="เพิ่มข้อมูล">

        </div>
    </div>
</form>
@endsection

@section('script')
    <script>
        var checked = true
        $('.checked_all').click(function(){
            
            if(checked === true){
                $('.checkbox').prop('checked', false)
                checked = false
            }else{
                $('.checkbox').prop('checked', true)
                checked = true
            }

        })
    </script>
@endsection