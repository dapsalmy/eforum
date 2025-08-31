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
                        <h5 class="h4 mb-0">{{ trans('points') }}</h5>
                        <div>
                            <button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#add_point">+ {{ trans('add_buy_point') }}</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                <div class="table-responsive">
                    <table id="datatable_cms" class="table table-bordered table-reload">

                        <thead>
                            <tr>
                                <th>{{ trans('value') }}</th>
                                <th>{{ trans('price') }}</th>
                                <th>{{ trans('order') }}</th>
                                <th class="text-right">{{ trans('options') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($points as $point)
                                <tr>
                                    <td>{{ $point->value }} {{ trans('points') }}</td>
                                    <td>{{ get_setting('currency_symbol') }}{{ $point->price }}</td>
                                    <td>{{ $point->order }}</td>
                                    <td class="text-right">

                                        <a  href="#" id="{{ $point->id }}" class="btn btn-soft-success btn-icon btn-circle btn-sm btn icon editIcon" title="Edit">
                                            <i class="align-middle" data-feather="edit-2"></i>
                                        </a>
                                        <a href="javascript:void(0)" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" title="Delete"
                                        onclick="delete_item('{{ route('admin.buypoints.destroy') }}','{{ $point->id }}','{{ trans('delete_this_point') }}?');">
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

        <!-- Add -->
        <div class="modal fade" id="add_point" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ trans('add_point') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="model-body">

                    <form id="add_point_form" method="POST">
                        @csrf

                        <div class="row px-3 py-3">
                            <div class="col-12">
                                <div class="input-style-1">
                                    <label for="value">{{ trans('value') }}</label>
                                    <input type="number" name="value" id="value" placeholder="Eg. 20" class="form-control my-2">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input-style-1">
                                    <label for="price">{{ trans('price') }}</label>
                                    <input type="number" name="price" id="price" placeholder="Eg. 10" class="form-control my-2">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input-style-1">
                                    <label for="order">{{ trans('order') }}</label>
                                    <input type="number" name="order" id="order" placeholder="Eg. 1" class="form-control my-2">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>

                        </div>

                        <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('close') }}</button>
                        <button type="submit" id="add_point_btn" class="btn btn-success">{{ trans('submit') }}</button>
                        </div>
                    </form>

                </div>
                </div>
            </div>
        </div>

        <!-- Edit -->
        <div class="modal fade" id="edit_point" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ trans('edit_point') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="model-body">

                    <form id="edit_point_form" method="POST">
                        @csrf

                        <input type="hidden" name="buypoint_id" id="buypoint_id">

                        <div class="row px-3 py-3">
                            <div class="col-12">
                                <div class="input-style-1">
                                    <label for="value">{{ trans('value') }}</label>
                                    <input type="number" name="value" id="value" placeholder="Eg. 20" class="form-control my-2">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input-style-1">
                                    <label for="price">{{ trans('price') }}</label>
                                    <input type="number" name="price" id="price" placeholder="Eg. 10" class="form-control my-2">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input-style-1">
                                    <label for="order">{{ trans('order') }}</label>
                                    <input type="number" name="order" id="order" placeholder="Eg. 1" class="form-control my-2">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>

                        </div>

                        <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('close') }}</button>
                        <button type="submit" id="edit_point_btn" class="btn btn-success">{{ trans('edit') }}</button>
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

        // add
        $(document).on('submit', '#add_point_form', function(e) {
            e.preventDefault();
            start_load();
            const fd = new FormData(this);
            $("#add_point_btn").text('{{ trans('adding') }}...');
            $.ajax({
            url: '{{ route('admin.buypoints.add') }}',
            method: 'post',
            data: fd,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(response) {

                end_load();

                if (response.status == 400) {

                    showError('value', response.messages.value);
                    showError('price', response.messages.price);
                    showError('order', response.messages.order);
                    $("#add_point_btn").text('{{ trans('submit') }}');

                }else if (response.status == 200) {

                    tata.success("Success", response.messages, {
                    position: 'tr',
                    duration: 3000,
                    animate: 'slide'
                    });

                    removeValidationClasses("#add_point_form");
                    $("#add_point_form")[0].reset();
                    $("#add_point").modal('hide');
                    window.location.reload();

                }else if(response.status == 401){

                    tata.error("Error", response.messages, {
                    position: 'tr',
                    duration: 3000,
                    animate: 'slide'
                    });

                    $("#add_point_form")[0].reset();
                    $("#add_point").modal('hide');
                    window.location.reload();

                }

            }
            });
        });

        // edit
        $(document).on('click', '.editIcon', function(e) {
            e.preventDefault();
            start_load();
            let id = $(this).attr('id');
            $.ajax({
                url: '{{ route('admin.buypoints.edit') }}',
                method: 'get',
                data: {
                id: id,
                },
                success: function(response) {
                    end_load();

                    $('#edit_point').modal('show');

                    $('#edit_point_form #value').val(response.value);
                    $('#edit_point_form #price').val(response.price);
                    $('#edit_point_form #order').val(response.order);
                    $('#edit_point_form #buypoint_id').val(response.id);

                }
            });
        });

        // update
        $(document).on('submit', '#edit_point_form', function(e) {
            e.preventDefault();
            start_load();
            const fd = new FormData(this);
            $("#edit_point_btn").text('{{ trans('updating') }}...');
            $.ajax({
                method: 'POST',
                url: '{{ route('admin.buypoints.update') }}',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    end_load();

                    if (response.status == 400) {

                        showError('value', response.messages.value);
                        showError('price', response.messages.price);
                        showError('order', response.messages.order);
                        $("#edit_point_btn").text('{{ trans('edit') }}');

                    }else if (response.status == 200) {

                        tata.success("Success", response.messages, {
                        position: 'tr',
                        duration: 3000,
                        animate: 'slide'
                        });

                        removeValidationClasses("#edit_point_form");
                        $("#edit_point_form")[0].reset();
                        $("#edit_point").modal('hide');
                        window.location.reload();

                    }else if(response.status == 401){

                        tata.error("Error", response.messages, {
                        position: 'tr',
                        duration: 3000,
                        animate: 'slide'
                        });

                        $("#edit_point_form")[0].reset();
                        $("#edit_point").modal('hide');
                        window.location.reload();

                    }

                }
            });
        });

    });
</script>

@endsection
