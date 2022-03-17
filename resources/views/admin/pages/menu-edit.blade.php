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
                <li class="breadcrumb-item active">Menu Items</li>
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
                            <h3 class="card-title">Menu Items - <small>Edit</small></h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form id="menu_edit" action="{{route('menu-update',$menu_details->id)}}" method="POST" enctype="multipart/form-data">
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
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Description</label><span class="form_required">*</span>
                                            <textarea name="menu_description" id="menu_description" value="{{ $menu_details->menu_description }}" class="form-control" reqiured autocomplete="off">
                                            {{ $menu_details->menu_description }}</textarea>
                                            @if ($errors->has('menu_description'))
                                            <span class="help-block">
                                                <strong class="error-text">{{ $errors->first('menu_description') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Category</label><span class="form_required">*</span>
                                            <select class="form-control select2"  name="menu_category" id="menu_category">
                                                <option selected disabled>Select Category</option>
                                                @foreach($category as $each_category)
                                                    <option value="{{$each_category->category_name}}" @if($each_category->category_name==$menu_details->menu_category){{"selected"}} @endif>{{$each_category->category_name}}</option>  
                                                @endforeach
                                            </select>
                                            @if ($errors->has('menu_category'))
                                            <span class="help-block">
                                                <strong class="error-text">{{ $errors->first('menu_category') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Sub Category</label><span class="form_required">*</span>
                                            <select class="form-control select2"  name="sub_category" id="sub_category">
                                                <option selected disabled>Select Subcategory</option>
                                                
                                                <option {{ ($menu_details->sub_category) == 'Veg' ? 'selected' : '' }}  value="Veg">Veg</option>
                                                <option {{ ($menu_details->sub_category) == 'Non-Veg' ? 'selected' : '' }}  value="Non-Veg">Non-Veg</option>
                                            </select>
                                            @if ($errors->has('sub_category'))
                                            <span class="help-block">
                                                <strong class="error-text">{{ $errors->first('sub_category') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Cuisine</label><span class="form_required">*</span>
                                            <select class="form-control select2"  name="menu_cuisine" id="menu_cuisine">
                                                <option selected disabled>Select Cuisine</option>
                                                @foreach($cuisine as $each_cuisine)
                                                    <option value="{{$each_cuisine->cuisine_name}}" @if($each_cuisine->cuisine_name==$menu_details->menu_cuisine){{"selected"}} @endif>{{$each_cuisine->cuisine_name}}</option>  
                                                @endforeach
                                            </select>
                                            @if ($errors->has('menu_cuisine'))
                                            <span class="help-block">
                                                <strong class="error-text">{{ $errors->first('menu_cuisine') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Portion</label><span class="form_required">*</span>
                                            <select class="form-control select2"  name="menu_portion" id="menu_portion">
                                                <option selected disabled>Select Portion</option>
                                                @foreach($portion as $each_portion)
                                                    <option value="{{$each_portion->portion_name}}" @if($each_portion->portion_name==$menu_details->menu_portion){{"selected"}} @endif>{{$each_portion->portion_name}}</option>  
                                                @endforeach
                                            </select>
                                            @if ($errors->has('menu_portion'))
                                            <span class="help-block">
                                                <strong class="error-text">{{ $errors->first('menu_portion') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Price</label><span class="form_required">*</span>
                                            <input type="text" name="menu_price" id="menu_price" value="{{ $menu_details->menu_price }}" class="form-control" placeholder="Enter Food Price" autocomplete="off"  reqiured="">
                                            @if ($errors->has('menu_price'))
                                            <span class="help-block">
                                                <strong class="error-text">{{ $errors->first('menu_price') }}</strong>
                                            </span>
                                            @endif
                                        </div>  
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Image</label>
                                            <input type="file" name="menu_file_name" id="menu_file_name" class="form-control" placeholder="Choose Image" accept="image/png, image/jpeg, image/jpg" autocomplete="off">
                                            
                                            @if($menu_details->menu_image)
                                                    <img width="150" height="100" class='image_uploaded_image' src="{{ url('storage/menu_item_images/' . $menu_details->menu_image) }}" />
                                                    <img width="150" height="100" class='image_selected_image' style='display:none;' src="#"  />
                                                    @else
                                                    <p class='image_no_image'>No image found</p>
                                                    <img width="150" height="100" class='image_selected_image' style='display:none;' src="#"  />
                                                    @endif
                                            <img width="150" height="100" class='menu_selected_image' style='display:none;' src="#"  />
                                            @if ($errors->has('menu_file_name'))
                                            <span class="help-block">
                                                <strong class="error-text">{{ $errors->first('menu_file_name') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                            <button type="submit" class="btn btn-primary" id="menu_edit_form_submit">Submit</button>
                            <button type="button" class="btn btn-secondary" id="menu_edit_form_cancel">Cancel</button>
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
        $("#menu_file_name").change(function() {
            displayPlainLogoSelectedImage(this);
        });
        function displayPlainLogoSelectedImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                $('.menu_selected_image').show();
                $('#menu_image_clear').show();
                $('.menu_no_image').hide();
                $('.menu_selected_image').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        $('#menu_edit').validate({
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
    $("#menu_image_clear").on("click", function () {
        $("#menu_file_name").val(null);
        $('.menu_selected_image').hide();
        $('#menu_image_clear').hide();
        $('.menu_no_image').show();
        $(".menu_selected_image").val(null);
    });
    $('#menu_edit_form_cancel').click(function(){
        window.location.href="{{ route('menu-list') }}";
    })
    
</script>

@endsection
