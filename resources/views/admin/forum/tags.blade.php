@extends('layouts.admin')

@section('content')

<main class="content">
    <!-- ========== section start ========== -->
    <section class="section">
      <div class="container-fluid">
      <div class="row mt-50">

        <div class="col-lg-12">
            @if(Route::is('admin.tags.list'))
                <div class="card">
                    <div class="card-header">

                        <div class="d-md-flex justify-content-between align-items-center mb-10">
                            <h5 class="h4 mb-0">{{ trans('tags') }}</h5>
                        </div>
                    </div>
                    <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable_cms" class="table table-bordered table-reload">

                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans('name') }}</th>
                                    <th>{{ trans('slug') }}</th>
                                    <th>{{ trans('posts') }}</th>
                                    <th class="text-right">{{ trans('options') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tags as $key => $tag)
                                    <tr>
                                        <td>{{ ($key+1) }}</td>
                                        <td>{{ $tag->name }}</td>
                                        <td>{{ $tag->slug }}</td>
                                        <td>{{ $tag->count }}</td>
                                        <td class="text-right">

                                            <a href="{{ route('admin.tags.edit', ['id' => $tag->id]) }}" class="btn btn-soft-success btn-icon btn-circle btn-sm btn icon" title="Edit">
                                                <i class="align-middle" data-feather="edit-2"></i>
                                            </a>
                                            <a href="javascript:void(0)" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" title="Delete"
                                            onclick="delete_item('{{ route('admin.tags.destroy') }}','{{ $tag->id }}','{{ trans('delete_this_tag') }}?');">
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
            @elseif (Route::is('admin.tags.edit'))

                <div class="card-style settings-card-2 mb-30">
                    <h4 class="mb-3">{{ trans('edit_tag') }}</h4>
                    <form id="editpost_form" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-12">
                                <div class="input-style-1">
                                    <label class="form-label">{{ trans('name') }}</label>
                                    <input type="text" name="name" id="name" value="{{ $tag->name }}">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>

                            <div class="col-12">
                            <button type="submit" id="editpost_btn" class="main-btn primary-btn btn-hover">{{ trans('submit') }}</button>
                            </div>
                        </div>

                    </form>
                </div>
            @endif
        </div>


      </div><!-- row -->
     </div><!-- container -->
    </section>

</main>
@endsection

@section('scripts')

    @if(Route::is('admin.tags.edit'))
        <script>
            $(function() {
                // update user ajax request
                $(document).on('submit', '#editpost_form', function(e) {
                    e.preventDefault();
                    start_load();
                    const fd = new FormData(this);
                    $("#editpost_btn").text('{{ trans('submitting') }}...');

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        method: 'POST',
                        url: '{{ route('admin.tags.edit', ['id' => $tag->id]) }}',
                        data: fd,
                        cache: false,
                        contentType: false,
                        processData: false,
                        dataType: 'json',
                        success: function(response) {
                            end_load();

                            if (response.status == 400) {

                                showError('name', response.messages.name);
                                $("#editpost_btn").text('{{ trans('submit') }}');

                            }else if (response.status == 200) {

                                tata.success("Success", response.messages, {
                                position: 'tr',
                                duration: 3000,
                                animate: 'slide'
                                });

                                removeValidationClasses("#editpost_form");
                                $("#editpost_form")[0].reset();
                                window.location.reload();

                            }else if(response.status == 401){

                                tata.error("Error", response.messages, {
                                position: 'tr',
                                duration: 3000,
                                animate: 'slide'
                                });

                                $("#editpost_form")[0].reset();
                                window.location.reload();

                            }

                        }
                    });

                });
            });
        </script>
    @endif

@endsection
