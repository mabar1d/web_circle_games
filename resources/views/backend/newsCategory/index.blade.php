@extends('backend.layout')

@section('title')
    News Category
@endsection

@push('plugin_css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endpush

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">News Category</h1>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <button type="button" id="btnFormAdd" class="btn btn-sm btn-success mb-2"
                                data-toggle="modal">Add New</button>
                            <table id="tbl_list" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
    <div class="modal fade" id="modalFormAdd">

    </div>
    <!-- /.modal -->
@endsection

@push('plugin_js')
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
@endpush

@push('script_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#tbl_list').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ url('be/master/news_category/getDatatable') }}",
                order: [],
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'desc',
                        name: 'desc'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                drawCallback: function(settings) {
                    $(".btnView").click(function() {
                        let id = $(this).data('id');
                        let urlBtnView = "{{ url('be/master/news_category/getFormAdd') }}";
                        $.ajax({
                            url: urlBtnView,
                            type: "POST",
                            data: {
                                "id": id
                            },
                            success: function(response) {
                                $('#modalFormAdd').empty();
                                $('#modalFormAdd').html(response);
                                // Display Modal
                                $('#modalFormAdd').modal('show');
                            },
                            error: function(error) {
                                alert(error);
                            }
                        })
                    });

                    $(".btnDelete").click(function() {
                        if (confirm('Are You Sure?')) {
                            let id = $(this).data('id');
                            let urlBtnDelete = "{{ url('be/master/news_category/delete') }}";
                            $.ajax({
                                url: urlBtnDelete,
                                type: "POST",
                                dataType: "json",
                                data: {
                                    "id": id
                                },
                                success: function(response) {
                                    alert(response.message);
                                    if (response.code == 0) {
                                        $('#tbl_list').DataTable().ajax.reload();
                                    }
                                },
                                error: function(error) {
                                    alert(error);
                                }
                            })
                        }
                    });
                }
            });

            $('#btnFormAdd').click(function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    url: "{{ url('be/master/news_category/getFormAdd') }}",
                    success: function(response) {
                        $('#modalFormAdd').empty();
                        $('#modalFormAdd').html(response);
                        // Display Modal
                        $('#modalFormAdd').modal('show');
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });
        });
    </script>
@endpush
