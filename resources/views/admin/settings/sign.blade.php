<div class="card">
    <div class="card-body">

        <a href="javascript:void(0)" class="btn btn-success mb-3 append_sign_form_btn">เพิ่มข้อมูล</a>

        <div id="append_sign_form">
            <?php $i = 0; ?>
            <input type="hidden" value="{{ $i }}" name="preview_count" id="preview_count"><!--a -->
            @if (isset($signs))
                @if (collect($signs)->isNotEmpty())
                    @foreach ($signs as $item)
                        <div class="card bg-gradient-secondary mb-2" id="form{{ ++$i }}">
                            <div class="card-body">
                                <div class="form-group row">
                                    <div class="col-sm-1  trash_div" >
                                        <a href="javascript:;" onclick="del('{{ $i }}')">
                                            <label for="organize_address" class="col-form-label ">&nbsp;</label>
                                            <i class="fas fa-trash-alt text-danger form-control"></i>
                                        </a>
                                    </div>
                                    <div class="col-sm-6 row">
                                        <div class="col-md-12">
                                            <label for="organize_address" class=" col-form-label">ชื่อ-สกุล</label>
                                            <input type="text" class="form-control"
                                                name="old_sign[{{ $i }}][name]" value="{{ $item->name }}"
                                                id="sign${{ $i }}">
                                        </div>
                                        <div class="col-md-12">
                                            <label for="organize_address" class=" col-form-label">ตำแหน่ง</label>
                                            <input type="text" class="form-control" value="{{ $item->position }}"
                                                name="old_sign[{{ $i }}][position]">
                                        </div>
                                    </div>
                                    <div class="col-sm-5">
                                        <label for="organize_address" class="col-sm-2 col-form-label">&nbsp;</label>
                                        <img id="frame{{ $i }}" src="{{ asset('sign/' . $item->image) }}"
                                            class="mt-4 mb-3" width="200px" />

                                        <input type="file" onchange="preview('{{ $i }}')"
                                            class="form-control filenames"
                                            name="old_sign_file[{{ $i }}]" data-id="{{ $i }}">

                                        <input type="hidden" name="old_sign[{{ $i }}][change_image]"
                                            id="img_name{{ $i }}" value="0">
                                        <input type="hidden" name="old_sign[{{ $i }}][old_name]"
                                            id="img_name_old_name{{ $i }}" value="{{ $item->image }}">

                                    </div>

                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            @else
            @endif
        </div>
    </div>
</div>


<!--row-->
