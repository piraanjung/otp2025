@extends('layouts.keptkaya_mobile2')

@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome สำหรับไอคอน -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   <link rel="stylesheet" href="{{ asset('css/staff_accessmenu.css') }}">
    <script>
        window.ASSET_URL = "{{ asset('') }}";
    </script>

    
@endsection

@section('content')

    <div class="container-fluid vh-100 d-flex flex-column justify-content-between p-0 position-relative">
        {{-- @include('staff.includes.login_screen') --}}
        @include('staff.includes.main_screen')
        @include('staff.includes.button_menu')
   </div>
@endsection

@section('script')
<script src="{{ asset('/js/jquery-3.7.1.slim.js') }}"></script>
<script>
    
    {!! file_get_contents(resource_path('views/staff/includes/login_screen.js')) !!}
    {!! file_get_contents(resource_path('views/staff/includes/navigate_to.js')) !!}

</script>
@endsection