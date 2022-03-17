@extends('admin.layouts.app')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Table Availability</li>
                </ol>
            </div>
        </div>
        @if(Session::has('message'))
        <div class="pad margin no-print">
            <div class="callout {{ Session::get('callout-class', 'callout-success') }}" style="margin-bottom: 0!important;">
                {{ Session::get('message') }}  
            </div>
        </div>
        @endif  
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- jquery validation -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Table Availability - <small>Edit</small></h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form id="table_edit" action="{{route('tableAvailability-update',$table_details->id)}}" method="POST" enctype="multipart/form-data">
                        @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Name</label><span class="form_required">*</span>
                                            <input type="text" name="menu_name" id="menu_name" value="{{ $menu_details->menu_name }}" class="form-control" placeholder="Enter Item Name"  reqiured="" autocomplete="off">
                                            @if ($errors->has('menu_name'))
                                            <span class="help-block">
                                                <strong class="error-text">{{ $errors->first('menu_name') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Name</label><span class="form_required">*</span>
                                            <input type="text" name="menu_name" id="menu_name" value="{{ $menu_details->menu_name }}" class="form-control" placeholder="Enter Item Name"  reqiured="" autocomplete="off">
                                            @if ($errors->has('menu_name'))
                                            <span class="help-block">
                                                <strong class="error-text">{{ $errors->first('menu_name') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                            <button type="submit" class="btn btn-primary" id="table_edit_form_submit">Submit</button>
                            <button type="button" class="btn btn-secondary" id="table_edit_form_cancel">Cancel</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.card -->
                    </div>
                <!--/.col (left) -->
                <!-- right column -->
                <div class="col-md-6">

                </div>
                <!--/.col (right) -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
  <!-- /.content-wrapper -->

@endsection

@section('script')
<script>

    $(function () {
       $('#table_edit').validate({
            rules: {
                menu_name: {
                    required: true,
                    maxlength: 255,
                },
                menu_description: {
                    required: true,
                    maxlength: 255,
                },
                menu_category: {
                    required: true,
                    maxlength: 255,
                },
                sub_category: {
                    required: true,
                    maxlength: 255,
                },
                menu_cuisine: {
                    required: true,
                    maxlength: 255,
                },
                menu_portion: {
                    required: true,
                    maxlength: 255,
                },
                menu_price: {
                    required: true,
                    maxlength: 255,
                },
                menu_file_name: {
                    required: false
                },
                
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
        });
        
    });
</script>

@endsection
