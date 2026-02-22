@extends('layouts.login')
@section('title')
    Super Admin
@endsection
@section('content')

    <form method="POST" action="{{ route('superadmin.login.post') }}">
        @csrf
        <label for="username">องค์กร:</label>
        <select name="org_id" id="" class="form-control">
            <option value="">เลือก</option>
            @foreach ($orgs as $org)
                <option value="{{$org->id}}">{{$org->org_name}} | {{$org->org_code}}</option>
            @endforeach
        </select>
        <div>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" class="form-control" value="superadmin1" required
                autofocus>
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" value="s12345" class="form-control" required>
        </div>
        <div>
            <button type="submit" class="btn btn-info mt-2">Login</button>
        </div>
    </form>
@endsection