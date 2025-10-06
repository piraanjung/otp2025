@extends('layouts.keptkaya')

@section('content')
    
    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if(session('success'))
        <div style="color: green;">{{ session('success') }}</div>
    @endif
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div>
                        <label for="organization_id">Organization</label>
                        <select class="form-control" name="organization_id" id="organization_id">
                            <option value="">Select Organization</option>
                            @foreach ($organizations as $organization)
                                <option value="{{ $organization->id }}">{{ $organization->org_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="username">Lind ID</label>
                        <input id="line_id" type="text" class="form-control" name="line_id" value="{{ old('line_id') }}" required autofocus>
                    </div>
                    <div>
                        <label for="username">Username</label>
                        <input id="username" type="text" class="form-control" name="username" value="{{ old('username') }}" required autofocus>
                    </div>
                    <div>
                        <label for="firstname">First Name</label>
                        <input id="firstname" type="text" class="form-control" name="firstname" value="{{ old('firstname') }}" required>
                    </div>
                    <div>
                        <label for="lastname">Last Name</label>
                        <input id="lastname" type="text" class="form-control" name="lastname" value="{{ old('lastname') }}" required>
                    </div>
                    <div>
                        <label for="email">Email</label>
                        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                    </div>
                    <div>
                        <label for="password">Password</label>
                        <input id="password" type="password" class="form-control" name="password" required>
                    </div>
                    <div>
                        <label for="password_confirmation">Confirm Password</label>
                        <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required>
                    </div>
                    <div>
                        <label for="id_card">ID Card</label>
                        <input id="id_card" type="text" class="form-control" name="id_card" value="{{ old('id_card') }}">
                    </div>
                    <div>
                        <label for="phone">Phone</label>
                        <input id="phone" type="text" class="form-control" name="phone" value="{{ old('phone') }}">
                    </div>
                    <!-- Add more fields as needed -->
                    <button type="submit">Register</button>
                </form>
            </div>
        </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script charset="utf-8" src="https://static.line-scdn.net/liff/edge/versions/2.22.3/sdk.js"></script>

            <script>
        const LINE_BOT_API = "https://api.line.me/v2/bot";
        const LINE_CHANNAL_ACCESS_TOKEN = "hKQpGAefzUb3nfDPG+kNim34f3uUhEm0RW8h9E2NtyAYZNtRrDTnP8J6qPyPSPvRNU3XV786SyrBZH649FugjcCrHZ4nOKWLtp/yHTdm/ZXQASL72zVoRIS/UFmTKNddkTrWTIci91qA1JinsUbxMAdB04t89/1O/w1cDnyilFU="
        let profile;
        const headers = {
            'Access-Control-Allow-Origin': "*",
            "Access-Control-Allow-Headers": "Content-Type",
            "Access-Control-Allow-Methods": "GET, POST, PUT, DELETE, OPTIONS",
            'Content-Type': 'application/json',
            'Authorization': 'Bearer hKQpGAefzUb3nfDPG+kNim34f3uUhEm0RW8h9E2NtyAYZNtRrDTnP8J6qPyPSPvRNU3XV786SyrBZH649FugjcCrHZ4nOKWLtp/yHTdm/ZXQASL72zVoRIS/UFmTKNddkTrWTIci91qA1JinsUbxMAdB04t89/1O/w1cDnyilFU=',
        }
        const main = async () => {
            await liff.init({
                liffId: '1656703539-Rzmb63NE',
            });
            if (!liff.isLoggedIn()) {
                liff.login()
                return false
            }

            profile = await liff.getProfile();
            console.log('profile', displayName)
            $('#loading').addClass('hidden');
            // let url = (profile.pictureUrl).replace("https://profile.line-scdn.net/", "");
            // $.post(`/line/fine_line_id`,{
            //       userId: profile.userId,
            //       displayName: profile.displayName,
            //       url: url,
            //       phone: profile.phoneNumber
            //   }).then(function (data)  {
            //     console.log('dta', data)
            //     if (data.res === 1) {
            //        window.location.href ='/line/dashboard/'+data.user_waste_pref_id;
            //     }
            // })

           // header__profile_img.src = profile.pictureUrl;
            // lineName.textContent = `Hello ${profile.displayName}`
            // lineUID.textContent = `UID ${profile.userId}`

        }

        const sendMessage = async () => {
            const body = {
                to: profile.userId,
                messages: [
                    {
                        type: 'text',
                        text: "Hello, world1"
                    },

                ]
            }
            try {
                const response = await axios.post(
                    `${LINE_BOT_API}/message/push`,
                    body,
                    { headers }
                )
                console.log('response', response.data)
            } catch (error) {
                console.log('err', error)
            }

        }

        const logOut = async () => {
            liff.logout()
            window.location.href = '/lineliff'
        }

        main()
    </script>
@endsection
