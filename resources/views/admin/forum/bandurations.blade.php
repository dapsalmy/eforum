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
                        <h5 class="h4 mb-0">{{ trans('ban_durations') }}</h5>
                        <div>
                            <button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#add_ban_modal">+ {{ trans('add_ban_duration') }}</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                <div class="table-responsive">
                    <table id="datatable_cms" class="table table-bordered table-reload">

                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ trans('title') }}</th>
                                <th>{{ trans('time_in_minutes') }}</th>
                                <th class="text-right">{{ trans('options') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bandurations as $key => $banduration)
                                <tr>
                                    <td>{{ ($key+1) }}</td>
                                    <td>{{ $banduration->title }}</td>
                                    <td>{{ $banduration->time }}</td>
                                    <td class="text-right">

                                        <a  href="#" id="{{ $banduration->id }}'" class="btn btn-soft-success btn-icon btn-circle btn-sm btn icon editIcon" title="Edit">
                                            <i class="align-middle" data-feather="edit-2"></i>
                                        </a>
                                        <a href="javascript:void(0)" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                        title="Delete"
                                        onclick="delete_item('{{ route('admin.bandurations.destroy') }}','{{ $banduration->id }}','{{ trans('delete_ban_duration') }}?')">
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

        {{-- add modal start --}}
        <div class="modal fade" id="add_ban_modal" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ trans('add_ban_duration') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="model-body">

                    <form id="add_ban_form" action="" method="POST">
                        @csrf

                        <div class="row px-3 py-3">
                            <div class="col-12">
                                <div class="input-style-1">
                                    <label for="title">{{ trans('title') }}</label>
                                    <input type="text" name="title" id="title" placeholder="Eg. 6 Hours" class="form-control my-2">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input-style-1">
                                    <label for="time">{{ trans('time') }}</label>
                                    <input type="number" name="time" id="time" placeholder="{{ trans('time_in_minutes') }}">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>

                        </div>

                        <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('close') }}</button>
                        <button type="submit" id="add_ban_btn" class="btn btn-success">{{ trans('add_ban_duration') }}</button>
                        </div>
                    </form>

                </div>
                </div>
            </div>
        </div>

        {{-- Edit Badge modal start --}}
        <div class="modal fade" id="edit_ban_modal" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ trans('edit_ban_duration') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="model-body">

                    <form id="edit_ban_form" action="" method="POST">
                        @csrf

                        <input type="hidden" name="ban_id" id="ban_id">
                        <div class="row px-3 py-3">
                            <div class="col-12">
                                <div class="input-style-1">
                                    <label for="title">{{ trans('title') }}</label>
                                    <input type="text" name="edit_title" id="edit_title" placeholder="Eg. 6 Hours" class="form-control my-2">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input-style-1">
                                    <label for="time">{{ trans('time') }}</label>
                                    <input type="number" name="edit_time" id="edit_time" placeholder="{{ trans('time_in_minutes') }}">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>

                        </div>

                        <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('close') }}</button>
                        <button type="submit" id="edit_ban_btn" class="btn btn-success">{{ trans('edit_ban_duration') }}</button>
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

        // add ban ajax request
        $(document).on('submit', '#add_ban_form', function(e) {
            e.preventDefault();
            start_load();
            const fd = new FormData(this);
            $("#add_ban_btn").text('{{ trans('adding') }}...');
            $.ajax({
            url: '{{ route('admin.bandurations.add') }}',
            method: 'post',
            data: fd,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(response) {

                end_load();

                if (response.status == 400) {

                    showError('title', response.messages.title);
                    showError('time', response.messages.time);
                    $("#add_ban_btn").text('{{ trans('add_ban_duration') }}');

                }else if (response.status == 200) {

                    tata.success("Success", response.messages, {
                    position: 'tr',
                    duration: 3000,
                    animate: 'slide'
                    });

                    removeValidationClasses("#add_ban_form");
                    $("#add_ban_form")[0].reset();
                    $("#add_ban_modal").modal('hide');
                    window.location.reload();

                }else if(response.status == 401){

                    tata.error("Error", response.messages, {
                    position: 'tr',
                    duration: 3000,
                    animate: 'slide'
                    });

                    $("#add_ban_form")[0].reset();
                    $("#add_ban_modal").modal('hide');
                    window.location.reload();

                }

            }
            });
        });

        // edit ban ajax request
        $(document).on('click', '.editIcon', function(e) {
            e.preventDefault();
            start_load();
            let id = $(this).attr('id');
            $.ajax({
                url: '{{ route('admin.bandurations.edit') }}',
                method: 'get',
                data: {
                id: id,
                },
                success: function(response) {
                    end_load();

                    $('#edit_ban_modal').modal('show');

                    $('#edit_ban_form #edit_title').val(response.title);
                    $('#edit_ban_form #edit_time').val(response.time);
                    $('#edit_ban_form #ban_id').val(response.id);

                }
            });
        });

        // update ban ajax request
        $(document).on('submit', '#edit_ban_form', function(e) {
            e.preventDefault();
            start_load();
            const fd = new FormData(this);
            $("#edit_ban_btn").text('{{ trans('updating') }}...');
            $.ajax({
                method: 'POST',
                url: '{{ route('admin.bandurations.update') }}',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    end_load();

                    if (response.status == 400) {

                        showError('edit_title', response.messages.edit_title);
                        showError('edit_time', response.messages.edit_time);
                        $("#edit_ban_btn").text('{{ trans('edit_ban_duration') }}');

                    }else if (response.status == 200) {

                        tata.success("Success", response.messages, {
                        position: 'tr',
                        duration: 3000,
                        animate: 'slide'
                        });

                        removeValidationClasses("#edit_ban_form");
                        $("#edit_ban_form")[0].reset();
                        $("#edit_ban_modal").modal('hide');
                        window.location.reload();

                    }else if(response.status == 401){

                        tata.error("Error", response.messages, {
                        position: 'tr',
                        duration: 3000,
                        animate: 'slide'
                        });

                        $("#edit_ban_form")[0].reset();
                        $("#edit_ban_modal").modal('hide');
                        window.location.reload();

                    }

                }
            });
        });

});
</script>

@endsection
