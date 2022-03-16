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
              <li class="breadcrumb-item active">Orders</li>
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
              <div class="col-12">
                    <!-- card -->
                    <div class="card scrollmenu">
                        <!-- card-header -->
                        <div class="card-header">
                            <h3 class="card-title">Reports</h3>
                        </div>
                        <div class="loader" style="display:none;"></div>
                        <div class="data-section">
                        <a href='/export_report'><button type="button" class="btn btn-xs btn-primary">Export</button></a>
                          <!-- /.card-header -->
                          <!-- card-body -->
                          <div class="card-body">
                              <table class="table table-bordered table-striped report_view" style="width:100%">
                                  <thead>
                                      <tr>
                                          <th>S. No.</th>
                                          <th>Order No.</th>
                                          <th>Table No.</th>
                                          <th>Order On</th>
                                          <th>Order By</th>
                                          <th>Items Ordered</th>
                                          <th>Total Amount</th>
                                          <th>Status</th>
                                        </tr>
                                  </thead>
                              </table>
                          </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
  <!-- /.content-wrapper -->

@endsection
<style>
    .report_view{
        word-wrap:break-word;
    }
</style>
@section('script')

<script type="text/javascript">
  $(function () {
    $(".loader").hide();
    $(".data-section").show();
    var table = $('.report_view').DataTable({
        bStateSave: true,
        responsive: true,
        bDestroy: true,
        processing: true,
        serverSide: true,
        deferRender: true,
        "pageLength": 10,
        "scrollX": true,
        dom: 'Bfrtip',
        lengthMenu: [
            [ 10, 25, 50, 500 ],
            [ '10 rows', '25 rows', '50 rows', '500 rows' ]
        ],
        buttons: [
            'pageLength','colvis','copy', 'csv', 'excel', 'print'
        ],
        ajax: "{{ route('reports') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'order_no', name: 'order_no'},
            {data: 'table_no', name: 'table_no'},
            {data: 'ordered_on', name: 'ordered_on'},
            {data: 'ordered_by', name: 'ordered_by'},
            {data: 'food_ordered', name: 'food_ordered'},
            {data: 'total_amount', name: 'total_amount'},
            {data: 'status', name: 'status'},
        ]
    });
  });
</script>
@endsection
