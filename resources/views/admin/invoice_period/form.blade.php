<main class="main-content col">
    <div class="main-content-container container-fluid px-4 my-auto h-100">
        <div class="row no-gutters h-100">
            <div class="col-lg-3 col-md-5 auth-form mx-auto my-auto">
                <div class="card">
                    <div class="card-body">
                       
                        <h5 class="auth-form__title text-center mb-4">
                            {{$mode == "create" ? "สร้างรอบบิล" : "แก้ไขรอบบิล"}}</h5>

                            <div id="app">
                                <invoice-period :mode="'{{$mode}}'" :id="'{{$id}}'"></invoice-period>
                            </div>
                    </div>

                </div>
            </div>
        </div>
</main>
