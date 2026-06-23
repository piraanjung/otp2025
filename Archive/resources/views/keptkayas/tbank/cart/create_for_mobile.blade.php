<table class="table" id="search_member">
    <thead>
        <tr>
            <td></td>
        </tr>
    </thead>
    <tbody>
        @foreach ($members as $item)
        {{ $item->user->firstname . ' ' . $item->user->lastname }}

            <tr>
                <td>
                    <div class="card ">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold"></p>
                                        <h5 class="font-weight-bolder mb-0">
                                            {{ $item->user->firstname . ' ' . $item->user->lastname }}
                                            <div class="text-success text-sm font-weight-bolder">
                                                {{ $item->user->address }}
                                                {{$item->user->user_zone->zone_name}}
                                                {{$item->user->user_subzone->subzone_name}}
                                                {{$item->user->user_tambon->tambon_name}}
                                                {{$item->user->user_district->district_name}}
                                                {{$item->user->user_province->province_name}}

                                                {{$item->trash_zone->zone_name}}
                                                {{$item->trash_subzone->subzone_name}}

                                            </div>
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <a href="{{route('items.buy_items',$item->user_id)}}" class="btn btn-info">รับซื้อขยะ</a>

                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>

</table>
