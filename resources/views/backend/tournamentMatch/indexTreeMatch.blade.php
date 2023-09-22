@extends('backend.layout')

@section('title')
    Tournament Match
@endsection

@push('plugin_css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet"
        href="{{ asset('adminlte/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
@endpush

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Tournament Tree Match</h1>
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
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="inputTournament">Tournament</label>
                                        <select class="form-control select2" style="width: 100%;" name="newsCategory"
                                            id="inputTournament">
                                            <option value="">Select a Tournament</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="inputStartDate">Start Date</label>
                                        <input type="text" class="form-control" id="inputStartDate"
                                            placeholder="Tournament Start Date" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="inputEndDate">End Date</label>
                                        <input type="text" class="form-control" id="inputEndDate"
                                            placeholder="Tournament End Date" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="inputPrize">Prize</label>
                                        <input type="text" class="form-control" id="inputPrize"
                                            placeholder="Tournament End Date" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="inputNumberOfParticipants">Number of Participants</label>
                                        <input type="text" class="form-control" id="inputNumberOfParticipants"
                                            placeholder="Tournament Number of Participants" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="inputTeamTournament">Teams Participate</label>
                                        <textarea type="text" class="form-control" id="inputTeamTournament" placeholder="Tournament Teams Participate"
                                            readonly></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="inputTermsCondition">Terms Condition</label>
                                        <textarea type="text" class="form-control" id="inputTermsCondition" placeholder="Tournament Terms & Condition"
                                            readonly></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <button type="button" class="btn btn-primary" id="btnRollMatch" disabled>Roll Match</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->
            <div class="row" id="formTournamentMatch">

            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </div>
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
    <!-- Select2 -->
    <script src="{{ asset('adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
    <!-- InputMask -->
    <script src="{{ asset('adminlte/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/inputmask/jquery.inputmask.min.js') }}"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="{{ asset('adminlte/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
@endpush

@push('script_js')
    <script type="text/javascript">
        $(document).ready(function() {
            //Initialize Select2 Elements
            $('#inputTournament').select2({
                ajax: {
                    url: "{{ url('be/tournament/getDropdown') }}",
                    dataType: 'json',
                    // placeholder: "Select a Tournament",
                    // allowClear: true,
                    data: function(params) {
                        var query = {
                            typeTournament: 2, //tournament tree
                            search: params.term,
                            type: 'public'
                        }
                        return query;
                    },
                    processResults: function(response) {
                        if (response.code == 0) {
                            var results = [];
                            $.each(response.data, function(index, data) {
                                results.push({
                                    id: data.id,
                                    text: data.name
                                });
                            });
                            return {
                                "results": results
                            };
                        } else {
                            alert(response.message);
                        }
                    }
                }
            });

            $('#inputTournament').change(function(e) {
                e.preventDefault();
                $('#btnRollMatch').prop("disabled", false);
                let tournamentId = $(this).val();
                $.ajax({
                    type: "POST",
                    url: "{{ url('be/tournament/getInfo') }}",
                    data: {
                        "tournament_id": tournamentId
                    },
                    dataType: "json",
                    encode: true,
                    success: function(response) {
                        if (response.code == "00") {
                            let titleGame = response.data.title_game;
                            let teamInTournament = response.data.team_in_tournament;
                            let registerStartDate = response.data.register_date_start;
                            let registerEndDate = response.data.register_date_end;
                            let startDate = response.data.start_date;
                            let endDate = response.data.end_date;
                            let prize = response.data.prize;
                            let termsCondition = response.data.terms_condition;
                            let numberOfParticipants = response.data.number_of_participants;

                            let teamInTournamentText = "";
                            $.each(teamInTournament, function(index, data) {
                                teamInTournamentText += data.team_name + ", ";
                            });
                            $('#inputStartDate').val(startDate);
                            $('#inputEndDate').val(endDate);
                            $('#inputPrize').val(prize);
                            $('#inputTermsCondition').text(termsCondition);
                            $('#inputTeamTournament').text(teamInTournamentText);
                            $('#inputNumberOfParticipants').val(numberOfParticipants);
                        }
                    },
                    error: function(error) {
                        alert(error.desc);
                    }
                });
            });

            $("#btnRollMatch").click(function(e) {
                e.preventDefault();
                let tournamentId = $("#inputTournament").val();
                $.ajax({
                    type: "POST",
                    url: "{{ url('be/tournament/rollRandomMatch') }}",
                    data: {
                        "tournament_id": tournamentId
                    },
                    success: function(response) {
                        $('#formTournamentMatch').empty();
                        $('#formTournamentMatch').html(response);
                    },
                    error: function(error) {
                        alert(error.code);
                    }
                });
            });
        });
    </script>
@endpush
