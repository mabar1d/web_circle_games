<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Circle Game News | {{$data->title}} </title>
    <meta name="description" content="{{$data->title}}">
    <meta property="og:title" content="Circle Games News!" />
    <meta property="og:url" content="{{$data->linkShare}}" />
    <meta property="og:description" content="{{$data->title}}">
    <meta property="og:image" content="{{ $data->image }}">
    <meta property="og:type" content="article" />
    <meta property="og:locale" content="id_ID" />

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{asset('adminlte/plugins/fontawesome-free/css/all.min.css')}}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{asset('adminlte/dist/css/adminlte.min.css')}}">
</head>

<body class="hold-transition layout-top-nav">
    <div class="wrapper">
        <div class="content-wrapper">
            <div class="container">
                <div class="row">
                    <div class="text-center">
                        <h1>{{$data->title}}</h1>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="content">
                    <div class="container">
                        <div class="text-center">
                            <img src="{{ $data->image }}" alt="image-news" style="max-width: 100%; height: auto;">
                        </div>
                        <div class="row text-justify">
                            <p>{{$data->content}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="main-footer">
        <div class="float-right d-none d-sm-inline">
            Anything you want
        </div>
        <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
    </footer>
    </div>

    <!-- jQuery -->
    <script src="{{asset('adminlte/plugins/jquery/jquery.min.js')}}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <!-- AdminLTE App -->
    <script src="{{asset('adminlte/dist/js/adminlte.min.js')}}"></script>
</body>


</html>