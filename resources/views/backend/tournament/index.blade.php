@extends('layout/dashboardLayout')

@section('plugin_css')
@endsection

@section('script_css')
@endsection

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>List News</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">List News</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"></h3>
                        <div class="card-tools">
                            <div class="input-group input-group-sm" style="width: 150px;">
                                <input type="text" name="table_search" class="form-control float-right" placeholder="Search">
                                <div class="input-group-append">
                                    <button id="btnSearchTableListNews" type="button" class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="tableListNews"></div>
                    <div class="card-footer clearfix">
                        <ul class="pagination pagination-sm m-0 float-right">
                            <li class="page-item"><button type="button" id="btnPrevTableListNews" data-page="1" class="page-link">&laquo;</button></li>
                            <li class="page-item"><button type="button" id="btnNextTableListNews" data-page="2" class="page-link">&raquo;</button></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
</section>
@endsection

@section('plugin_js')
@endsection

@section('script_js')
<script>
    function renderTableListNews(search, page) {
        $.ajax({
            url: "{{ url('tournament/getTable') }}",
            type: "GET",
            dataType: 'json',
            data: {
                "search": search,
                "page": page
            },
            beforeSend: function() {
                // App.blockUI({
                //     boxed: true
                // });
            },
            success: function(response) {
                if (response.code == '00') {
                    $("#tableListNews").empty();
                    $("#tableListNews").html(response.html);
                    $("#btnPrevTableListNews").data("page", response.prevPage);
                    $("#btnNextTableListNews").data("page", response.nextPage);
                } else {
                    alert(response.desc);
                }
            },
            error: function(error) {
                alert(error);
            }
        });
    }

    $(document).ready(function() {
        renderTableListNews();
        $("#btnSearchTableListNews").click(function(e) {
            e.preventDefault();
            let search = $("input[name=table_search]").val();
            let page = $(this).data("page");
            console.log(page);
            console.log(search);
            renderTableListNews(search, page);
        });
        $("#btnNextTableListNews").click(function(e) {
            e.preventDefault();
            let search = $("input[name=table_search]").val();
            let page = $(this).data("page");
            renderTableListNews(search, page);
        });
        $("#btnPrevTableListNews").click(function(e) {
            e.preventDefault();
            let search = $("input[name=table_search]").val();
            let page = $(this).data("page");
            renderTableListNews(search, page);
        });
    });

    function getDetailNews(slug) {
        console.log(slug);
        window.location.replace('listNews/detailNews/' + slug);
    }
</script>
@endsection