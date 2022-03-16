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
                <li class="breadcrumb-item active">Table Profiles</li>
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
                <div class="col-md-2">
                </div>
                <div class="col-md-8">
                    <!-- jquery validation -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Table Profiles - <small></small>Add</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <!-- form start -->
                        <form id="table_add" action="{{route('table-insert')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                            <div class="card-body">
                            <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Text</label><span class="form_required">*</span>
                                            <input type="text" name="table_data" id="table_data" class="form-control" placeholder="Enter Message that you want to save in qrcode" autocomplete="off">
                                            @if ($errors->has('table_data'))
                                            <span class="help-block">
                                                <strong class="error-text">{{ $errors->first('table_data') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Table No.</label><span class="form_required">*</span>
                                            <input type="text" name="table_no" id="table_no" class="form-control" placeholder="Enter Table No." autocomplete="off">
                                            @if ($errors->has('table_no'))
                                            <span class="help-block">
                                                <strong class="error-text">{{ $errors->first('table_no') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                            <button type="submit" class="btn btn-primary" id="table_add_form_submit">Submit</button>
                            <button type="button" class="btn btn-secondary" id="table_add_form_cancel">Cancel</button>
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
        $('#table_add').validate({
            ignore: ":hidden", 
            ignore: "",
            rules: {
                table_data: {
                    required: true,
                    maxlength: 255,
                },
                table_no: {
                    required: true,
                    maxlength: 50,
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
    $('#table_add_form_cancel').click(function(){
        window.location.href="{{ route('table-list') }}";
    })
    
</script>

@endsection










