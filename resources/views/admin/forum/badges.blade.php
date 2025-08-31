@extends('layouts.admin')

@section('content')

<main class="content">
    <!-- ========== section start ========== -->
    <section class="section">
      <div class="container-fluid">
      <div class="row mt-50">


        <div class="col-12">
            <div class="card">
                <div class="card-header">

                    <div class="d-md-flex justify-content-between align-items-center mb-10">
                        <h5 class="h4 mb-0">{{ trans('badges') }}</h5>
                        <div>
                            <button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addBadgeModal">+ {{ trans('add_badge') }}</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                <div class="table-responsive">
                    <table id="datatable_cms" class="table table-bordered table-reload">

                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ trans('image') }}</th>
                                <th>{{ trans('name') }}</th>
                                <th>{{ trans('score') }}</th>
                                <th>{{ trans('description') }}</th>
                                <th class="text-right">{{ trans('options') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($badges as $key => $badge)
                                <tr>
                                    <td>{{ ($key+1) }}</td>
                                    <td><img src="{{ my_asset('uploads/badges/'.$badge->image) }}" class="img-fluid" width="70px" height="60px" alt="Image"></td>
                                    <td>{{ $badge->name }}</td>
                                    <td>{{ $badge->score }}</td>
                                    <td>{{ $badge->description }}</td>
                                    <td class="text-right">

                                        <a href="#" id="{{ $badge->id }}'" class="btn btn-soft-success btn-icon btn-circle btn-sm btn icon editIcon" title="Edit">
                                            <i class="align-middle" data-feather="edit-2"></i>
                                        </a>
                                        <a href="javascript:void(0)" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                        title="Delete"
                                        onclick="delete_item('{{ route('admin.badges.destroy') }}','{{ $badge->id }}','{{ trans('delete_this_badge') }}')">
                                            <i class="align-middle" data-feather="trash"></i>
                                        </a>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                </div>
            </div>
        </div>

        {{-- add Badge modal start --}}
        <div class="modal fade" id="addBadgeModal" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ trans('add_badge') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="model-body">

                    <form id="add_badge_form" action="" method="POST">
                        @csrf

                        <div class="row px-3 py-3">
                            <div class="col-12">
                                <div class="input-style-1">
                                    <label for="name">{{ trans('name') }}</label>
                                    <input type="text" name="name" id="name" placeholder="Eg. King" class="form-control my-2">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input-style-1">
                                    <label for="description">{{ trans('description') }}</label>
                                    <textarea name="description" id="description" rows="4" placeholder="{{ trans('description') }}"></textarea>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input-style-1">
                                    <label for="score">{{ trans('score') }}</label>
                                    <input type="number" name="score" id="score" placeholder="Eg. 400" class="form-control my-2">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input-style-1">
                                    <label for="symbol">{{ trans('image') }}</label>
                                    <input type="file" name="image" id="image">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>

                        </div>

                        <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('close') }}</button>
                        <button type="submit" id="add_badge_btn" class="btn btn-success">{{ trans('add_badge') }}</button>
                        </div>
                    </form>

                </div>
                </div>
            </div>
        </div>

        {{-- Edit Badge modal start --}}
        <div class="modal fade" id="editBadgeModal" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ trans('edit_badge') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="model-body">

                    <form id="edit_badge_form" action="" method="POST">
                        @csrf

                        <input type="hidden" name="badge_id" id="badge_id">
                        <input type="hidden" name="old_image" id="old_image">
                        <div class="row px-3 py-3">
                            <div class="col-12">
                                <div class="input-style-1">
                                    <label for="name">{{ trans('name') }}</label>
                                    <input type="text" name="edit_name" id="edit_name" placeholder="Eg. Music" class="form-control my-2">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input-style-1">
                                    <label for="description">{{ trans('description') }}</label>
                                    <textarea name="edit_description" id="edit_description" rows="4" placeholder="Description"></textarea>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input-style-1">
                                    <label for="score">{{ trans('score') }}</label>
                                    <input type="number" name="edit_score" id="edit_score" placeholder="Eg. 400" class="form-control my-2">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-12" id="current_image">
                                <div class="input-style-1">
                                    <label for="symbol">{{ trans('image') }}</label>
                                    <input type="file" name="edit_image" id="edit_image">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>

                        </div>

                        <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('close') }}</button>
                        <button type="submit" id="edit_badge_btn" class="btn btn-success">{{ trans('edit_badge') }}</button>
                        </div>
                    </form>

                </div>
                </div>
            </div>
        </div>

    </div><!-- row -->
   </div><!-- container -->
  </section>

</main>
@endsection



@section('scripts')

<script>
    $(function() {

        // add badge ajax request
        $(document).on('submit', '#add_badge_form', function(e) {
            e.preventDefault();
            start_load();
            const fd = new FormData(this);
            $("#add_badge_btn").text('{{ trans('adding') }}...');
            $.ajax({
            url: '{{ route('admin.badges.add') }}',
            method: 'post',
            data: fd,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(response) {

                end_load();

                if (response.status == 400) {

                    showError('name', response.messages.name);
                    showError('description', response.messages.description);
                    showError('score', response.messages.score);
                    showError('image', response.messages.image);
                    $("#add_badge_btn").text('{{ trans('add_badge') }}');

                }else if (response.status == 200) {

                    tata.success("Success", response.messages, {
                    position: 'tr',
                    duration: 3000,
                    animate: 'slide'
                    });

                    removeValidationClasses("#add_badge_form");
                    $("#add_badge_form")[0].reset();
                    $("#addBadgeModal").modal('hide');
                    window.location.reload();

                }else if(response.status == 401){

                    tata.error("Error", response.messages, {
                    position: 'tr',
                    duration: 3000,
                    animate: 'slide'
                    });

                    $("#add_badge_form")[0].reset();
                    $("#addBadgeModal").modal('hide');
                    window.location.reload();

                }

            }
            });
        });

        // edit badge ajax request
        $(document).on('click', '.editIcon', function(e) {
            e.preventDefault();
            start_load();
            let id = $(this).attr('id');
            $.ajax({
                url: '{{ route('admin.badges.edit') }}',
                method: 'get',
                data: {
                id: id,
                },
                success: function(response) {
                    end_load();

                    $('#editBadgeModal').modal('show');

                    $('#edit_badge_form #edit_name').val(response.name);
                    $('#edit_badge_form #edit_description').val(response.description);
                    $('#edit_badge_form #edit_score').val(response.score);

                    $('#edit_badge_form #current_image').prepend($('<img>',{id:'theImg',src:'../../public/uploads/badges/'+response.image,class:'img-fluid mb-3',width:'100px',height:'100px'}));
                    $('#edit_badge_form #old_image').val(response.image);
                    $('#edit_badge_form #badge_id').val(response.id);

                }
            });
        });

        // update badge ajax request
        $(document).on('submit', '#edit_badge_form', function(e) {
            e.preventDefault();
            start_load();
            const fd = new FormData(this);
            $("#edit_badge_btn").text('{{ trans('updating') }}...');
            $.ajax({
                method: 'POST',
                url: '{{ route('admin.badges.update') }}',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    end_load();

                    if (response.status == 400) {

                        showError('edit_name', response.messages.edit_name);
                        showError('edit_description', response.messages.edit_description);
                        showError('edit_score', response.messages.edit_score);
                        showError('edit_image', response.messages.edit_image);
                        $("#edit_badge_btn").text('{{ trans('edit_badge') }}');

                    }else if (response.status == 200) {

                        tata.success("Success", response.messages, {
                        position: 'tr',
                        duration: 3000,
                        animate: 'slide'
                        });

                        removeValidationClasses("#edit_badge_form");
                        $("#edit_badge_form")[0].reset();
                        $("#editBadgeModal").modal('hide');
                        window.location.reload();

                    }else if(response.status == 401){

                        tata.error("Error", response.messages, {
                        position: 'tr',
                        duration: 3000,
                        animate: 'slide'
                        });

                        $("#edit_badge_form")[0].reset();
                        $("#editBadgeModal").modal('hide');
                        window.location.reload();

                    }

                }
            });
        });

});
</script>

@endsection
