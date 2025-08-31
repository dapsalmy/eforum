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
                        <h5 class="h4 mb-0">{{ trans('countries') }}</h5>
                        <div>
                            <button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addCountryModal">+ {{ trans('add') }}</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                <div class="table-responsive">
                    <table id="datatable_cms" class="table table-bordered table-reload">

                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ trans('name') }}</th>
                                <th>{{ trans('code') }}</th>
                                <th>{{ trans('status') }}</th>
                                <th class="text-right">{{ trans('options') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($country as $key => $co)
                                <tr>
                                    <td>{{ ($key+1) }}</td>
                                    <td>{{ $co->name }}</td>
                                    <td>{{ $co->code }}</td>
                                    @if ($co->status == 1)
                                     <td> <span class="badge bg-success">{{ trans('active') }}</span> </td>
                                    @else
                                    <td> <span class="badge bg-danger">{{ trans('not_active') }}</span> </td>
                                    @endif
                                    <td class="text-right">

                                        <a  href="#" id="{{ $co->id }}" class="btn btn-soft-success btn-icon btn-circle btn-sm btn icon editIcon" title="Edit">
                                            <i class="align-middle" data-feather="edit-2"></i>
                                        </a>
                                        <a href="javascript:void(0)" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" title="Delete"
                                        onclick="delete_item('{{ route('admin.country.destroy') }}','{{ $co->id }}','{{ trans('delete_this_country') }}');">
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




        {{-- add Category modal start --}}
        <div class="modal fade" id="addCountryModal" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ trans('add_country') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="model-body">

                    <form id="add_country_form" action="" method="POST">
                        @csrf

                        <div class="row px-3 py-3">
                            <div class="col-12">
                                <div class="input-style-1">
                                    <label for="name">{{ trans('name') }}</label>
                                    <input type="text" name="name" id="name" placeholder="Eg. United States of America" class="form-control my-2">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input-style-1">
                                    <label for="code">{{ trans('code') }}</label>
                                    <input type="text" name="code" id="code" placeholder="Eg. US" class="form-control my-2">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input-style-1">
                                    <label for="status">{{ trans('status') }}</label>
                                    <select name="status" id="status" class="form-select form-control">
                                        <option value="1">{{ trans('active') }}</option>
                                        <option value="0">{{ trans('not_active') }}</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>

                        </div>

                        <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('close') }}</button>
                        <button type="submit" id="add_country_btn" class="btn btn-success">{{ trans('add') }}</button>
                        </div>
                    </form>

                </div>
                </div>
            </div>
        </div>
        {{-- add Category modal end --}}

        {{-- Edit Category modal start --}}
        <div class="modal fade" id="editCountryModal" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ trans('edit_country') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="model-body">

                    <form id="edit_country_form" action="" method="POST">
                        @csrf

                        <input type="hidden" name="country_id" id="country_id">
                        <div class="row px-3 py-3">
                            <div class="col-12">
                                <div class="input-style-1">
                                    <label for="name">{{ trans('name') }}</label>
                                    <input type="text" name="name" id="name" placeholder="Eg. United States of America" class="form-control my-2">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input-style-1">
                                    <label for="code">{{ trans('code') }}</label>
                                    <input type="text" name="code" id="code" placeholder="Eg. US" class="form-control my-2">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input-style-1">
                                    <label for="status">{{ trans('status') }}</label>
                                    <select name="status" id="status" class="form-select form-control">
                                        <option value="1">{{ trans('active') }}</option>
                                        <option value="0">{{ trans('not_active') }}</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>

                        </div>

                        <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('close') }}</button>
                        <button type="submit" id="edit_country_btn" class="btn btn-success">{{ trans('update') }}</button>
                        </div>
                    </form>

                </div>
                </div>
            </div>
        </div>
        {{-- Edit Category modal end --}}

    </div><!-- row -->
   </div><!-- container -->
  </section>

</main>
@endsection



@section('scripts')

<script>
    $(function() {

        // add category ajax request
        $(document).on('submit', '#add_country_form', function(e) {
            e.preventDefault();
            start_load();
            const fd = new FormData(this);
            $("#add_country_btn").text('{{ trans('adding') }}...');
            $.ajax({
            url: '{{ route('admin.country.add') }}',
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
                    showError('code', response.messages.code);
                    showError('status', response.messages.status);
                    $("#add_country_btn").text('{{ trans('add') }}');

                }else if (response.status == 200) {

                    tata.success("Success", response.messages, {
                    position: 'tr',
                    duration: 3000,
                    animate: 'slide'
                    });

                    removeValidationClasses("#add_country_form");
                    $("#add_country_form")[0].reset();
                    $("#addCountryModal").modal('hide');
                    window.location.reload();

                }else if(response.status == 401){

                    tata.error("Error", response.messages, {
                    position: 'tr',
                    duration: 3000,
                    animate: 'slide'
                    });

                    $("#add_country_form")[0].reset();
                    $("#addCountryModal").modal('hide');
                    window.location.reload();

                }

            }
            });
        });

        // edit category ajax request
        $(document).on('click', '.editIcon', function(e) {
            e.preventDefault();
            start_load();
            let id = $(this).attr('id');
            $.ajax({
                url: '{{ route('admin.country.edit') }}',
                method: 'get',
                data: {
                id: id,
                },
                success: function(response) {
                    end_load();

                    $('#editCountryModal').modal('show');

                    $('#edit_country_form #name').val(response.name);
                    $('#edit_country_form #code').val(response.code);
                    $('#edit_country_form #country_id').val(response.id);
                    if (response.status == 1) {
                        $("#edit_country_form #status option[value=1]").attr('selected', 'selected');
                    } else {
                        $("#edit_country_form #status option[value=0]").attr('selected', 'selected');
                    }

                }
            });
        });

        // update category ajax request
        $(document).on('submit', '#edit_country_form', function(e) {
            e.preventDefault();
            start_load();
            const fd = new FormData(this);
            $("#edit_country_btn").text('{{ trans('updating') }}...');
            $.ajax({
                method: 'POST',
                url: '{{ route('admin.country.update') }}',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    end_load();

                    if (response.status == 400) {

                        showError('name', response.messages.name);
                        showError('code', response.messages.code);
                        showError('status', response.messages.status);
                        $("#edit_country_btn").text('{{ trans('submit') }}');

                    }else if (response.status == 200) {

                        tata.success("Success", response.messages, {
                        position: 'tr',
                        duration: 3000,
                        animate: 'slide'
                        });

                        removeValidationClasses("#edit_country_form");
                        $("#edit_country_form")[0].reset();
                        $("#editCountryModal").modal('hide');
                        window.location.reload();

                    }else if(response.status == 401){

                        tata.error("Error", response.messages, {
                        position: 'tr',
                        duration: 3000,
                        animate: 'slide'
                        });

                        $("#edit_country_form")[0].reset();
                        $("#editCountryModal").modal('hide');
                        window.location.reload();

                    }

                }
            });
        });

    });
</script>

@endsection
