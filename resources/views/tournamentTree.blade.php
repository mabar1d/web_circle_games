<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="{{asset('css/jquery.bracket.min.css')}}">
</head>

<body>
    <input type="hidden" value="{{$tournament_id}}" id="tournament_id" readonly>
    <div class="tournament"></div>
    <script src="{{asset('js/jquery.js')}}"></script>
    <script src="{{asset('js/jquery.bracket.min.js')}}"></script>
    <script>
        function createBracketTournament() {
            jQuery.ajax({
                url: "{{ url('get_tournament_match')}}",
                type: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "tournament_id": $("#tournament_id").val()
                },
                success: function(data) {
                    console.log(data)
                    data = $.parseJSON(data);
                    var minData = {
                        teams: data.teams,
                        results: data.score
                    }
                    $(".tournament").bracket({
                        init: minData
                    });
                },
                error: function(xhr, b, c) {
                    console.log("xhr=" + xhr + " b=" + b + " c=" + c);
                }
            });
        }
        $(document).ready(function() {
            createBracketTournament();
        });
    </script>
</body>

</html>


<?php
$test = array();
?>