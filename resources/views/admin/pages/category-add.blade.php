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
                <li class="breadcrumb-item active">Add Category</li>
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
                            <h3 class="card-title">Add Category</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form id="new_category_add" action="{{route('category-insert')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Category Name</label><span class="form_required">*</span>
                                            <input type="text" name="category_name" id="name" class="form-control" placeholder="Enter Category Name" autocomplete="off" maxlength="50">
                                            @if ($errors->has('category_name'))
                                            <span class="help-block">
                                                <strong class="error-text">{{ $errors->first('category_name') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Image</label>
                                            <input type="file" name="category_file_name" id="category_file_name" class="form-control" placeholder="Choose Image" accept="image/png, image/jpeg, image/jpg" autocomplete="off">
                                            <p class='category_no_image'>No image found</p>
                                            <button type="button" class="close file_clear" style="display:none;" id="category_image_clear">
                                            <span>&times;</span>
                                            </button>
                                            <img width="150" height="100" class='category_selected_image' style='display:none;' src="#"  />
                                            @if ($errors->has('category_file_name'))
                                            <span class="help-block">
                                                <strong class="error-text">{{ $errors->first('category_file_name') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                            <button type="submit" class="btn btn-primary" id="new_category_add_form_submit">Submit</button>
                            <button type="button" class="btn btn-secondary" id="new_category_add_form_cancel">Cancel</button>
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
        $('#new_category_add').validate({
            ignore: ":hidden",
            ignore: "",
            rules: {
                category_name: {
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
    $("#category_file_name").change(function() {
        displayPlainLogoSelectedImage(this);
    });
    function displayPlainLogoSelectedImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
            $('.category_selected_image').show();
            $('#category_image_clear').show();
            $('.category_no_image').hide();
            $('.category_selected_image').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#category_image_clear").on("click", function () {
        $("#category_file_name").val(null);
        $('.category_selected_image').hide();
        $('#category_image_clear').hide();
        $('.category_no_image').show();
        $(".category_selected_image").val(null);
    });
    $('#new_category_add_form_cancel').click(function(){
        window.location.href="{{ route('category-list') }}";
    })

</script>

@endsection
