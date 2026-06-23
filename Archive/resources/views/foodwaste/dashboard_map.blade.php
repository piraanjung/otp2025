@extends('layouts.foodwaste')

@section('title_page', 'แผนที่แสดงถังขยะ')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>แผนที่แสดงตำแหน่งถังขยะทั้งหมด</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div id="map" style="height: 600px; width: 100%; border-radius: 8px;"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        const binIconUrl = "{{ asset('imgs/bin_empty.png') }}";

        function initMap() {

            const defaultLocation = { lat: 16.0591353, lng: 105.1723741 }; // Default to บ้านนาสนาม เขมราฐ
            // const defaultLocation = { lat: 17.3756670, lng: 103.7108740 }; // Default to Bangkok
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 16,
                center: defaultLocation,
            });

            // Fetch waste bin data from the backend
            fetch("{{ route('foodwaste.waste_bins.map') }}")
                .then(response => response.json())
                .then(bins => {
                    bins.forEach(bin => {
                        console.log(bin)
                        if (bin.latitude && bin.longitude) {
                            const latLng = new google.maps.LatLng(bin.latitude, bin.longitude);

                            // Set marker icon
                            const marker = new google.maps.Marker({
                                position: latLng,
                                map: map,
                                title: bin.bin_code,
                                icon: { // Custom icon object
                                    url: binIconUrl, // Your trash bin icon URL
                                    scaledSize: new google.maps.Size(32, 32) // Adjust size as needed
                                },
                            });

                            // Create info window content
                            const infoContent = `
                                <h6>${bin.bin_code}</h6>
                                <p><strong>ชื่อ-สกุล:</strong> ${bin.user.firstname}  ${bin.user.lastname}</p>
                                <p><strong>ที่อยู่:</strong> ${bin.user.address} บ้าน ${bin.user.user_subzone.subzone_name} หมู่ ${bin.user.user_zone.zone_name}</p>
                                <p><strong>ประเภท:</strong> ${bin.bin_type}</p>
                                <p><strong>สถานะ:</strong> ${bin.status}</p>
                                <p><strong>ละติจูด:</strong> ${bin.latitude}</p>
                                <p><strong>ลองจิจูด:</strong> ${bin.longitude}</p>
                            `;

                            const infowindow = new google.maps.InfoWindow({
                                content: infoContent
                            });

                            marker.addListener("click", () => {
                                infowindow.open(map, marker);
                            });
                        }
                    });
                })
                .catch(error => console.error('Error fetching bin data:', error));
        }
    </script>
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA-5AlIGzLhFXErl2STRT6GacX0616iW2o&callback=initMap"></script>
@endsection