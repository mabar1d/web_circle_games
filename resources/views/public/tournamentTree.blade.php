<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="{{ asset('css/jquery.bracket.min.css') }}">
</head>

<body>
    <input type="hidden" value="{{ $tournament_id }}" id="tournament_id" readonly>
    <div class="tournament"></div>
    <script src="{{ asset('js/jquery.js') }}"></script>
    <script src="{{ asset('js/jquery.bracket.min.js') }}"></script>
    <script>
        function createBracketTournament() {
            jQuery.ajax({
                url: "{{ url('get_tournament_tree_match') }}",
                type: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "tournament_id": $("#tournament_id").val()
                },
                success: function(response) {
                    response = $.parseJSON(response);
                    // console.log(response.code);
                    if (response.code == 00) {
                        var minData = response.data;
                        // console.log(minData);
                        // // contoh double elimination
                        // var minData = {
                        //     "teams": [
                        //         ["Team 1", "Team 2"],
                        //         ["Team 3", "Team 4"]
                        //     ],
                        //     "results": [ // List of brackets (three since this is double elimination)
                        //         [ // Winner bracket
                        //             [
                        //                 [1, 2],
                        //                 [3, 4]
                        //             ], // First round and results
                        //             [
                        //                 [5, 6]
                        //             ] // Second round
                        //         ],
                        //         [ // Loser bracket
                        //             [
                        //                 [7, 8]
                        //             ], // First round
                        //             [
                        //                 [9, 10]
                        //             ] // Second round
                        //         ],
                        //         [ // Final "bracket"
                        //             [ // First round
                        //                 [11, 12], // Match to determine 1st and 2nd
                        //                 [13, 14] // Match to determine 3rd and 4th
                        //             ],
                        //             [ // Second round
                        //                 [15, 16] // LB winner won first round (11-12) so need a final decisive round
                        //             ]
                        //         ]
                        //     ]
                        // }

                        // //contoh Single Elimination
                        // var minData = {
                        //     teams: [
                        //         ["Team 1", "Team 17"],
                        //         ["Team 2", "Team 18"],
                        //         ["Team 3", "Team 19"],
                        //         ["Team 4", "Team 20"],
                        //         ["Team 5", "Team 21"],
                        //         ["Team 6", "Team 22"],
                        //         ["Team 7", "Team 23"],
                        //         ["Team 8", "Team 24"],
                        //         ["Team 9", "Team 25"],
                        //         ["Team 10", "Team 26"],
                        //         ["Team 11", "Team 27"],
                        //         ["Team 12", "Team 28"],
                        //         ["Team 13", "Team 29"],
                        //         ["Team 14", "Team 30"],
                        //         ["Team 15", "Team 31"],
                        //         ["Team 16", "Team 32"]
                        //     ],
                        //     results: [
                        //         [
                        //             [ //first leg
                        //                 [1, 0],
                        //                 [1, 0],
                        //                 [1, 0],
                        //                 [1, 0],
                        //                 [1, 0],
                        //                 [1, 0],
                        //                 [1, 0],
                        //                 [1, 0],
                        //                 [1, 0],
                        //                 [1, 0],
                        //                 [1, 0],
                        //                 [1, 0],
                        //                 [1, 0],
                        //                 [1, 0],
                        //                 [1, 0],
                        //                 [1, 0],
                        //                 [1, 0],
                        //                 [1, 0],
                        //             ],
                        //             [ //second leg
                        //                 [1, 3],
                        //                 [1, 2],
                        //                 [4, 1],
                        //                 [1, 3],
                        //                 [1, 4],
                        //                 [1, 2],
                        //                 [1, 2],
                        //                 [1, 4]
                        //             ],
                        //             [ //quarter Final
                        //                 [2, 3],
                        //                 [1, 2],
                        //                 [2, 1],
                        //                 [4, 3]
                        //             ],
                        //             [ //semi Final
                        //                 [2, 3],
                        //                 [1, 2]
                        //             ],
                        //             [ //Final
                        //                 [2, 1], //juara 1 dan 2
                        //                 [2, 1] //juara 3 dan 4
                        //             ]
                        //         ]
                        //     ]
                        // }

                        // var minData = {
                        //     teams: data.teams,
                        //     results: data.score
                        // }

                        $(".tournament").bracket({
                            init: minData,
                            centerConnectors: true,
                            teamWidth: 100,
                            scoreWidth: 50,
                            matchMargin: 80,
                            roundMargin: 100
                        });
                    }
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
$test = [];
?>
