<div class="col-md-12">
    <div class="card">
        <form id="formInputTournamentMatch">
            <div class="card-body">
                <input type="hidden" class="form-control" name="tournament_id" value="{{ $tournamentId }}" readonly>
                @foreach ($data as $keyData => $rowData)
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="inputHomeTeam">Home Team</label>
                                <input type="text" class="form-control" value="{{ $rowData['home_team_name'] }}"
                                    readonly>
                                <input type="hidden" class="form-control"
                                    name="match_array[{{ $keyData }}][home_team_id]"
                                    value="{{ $rowData['home_team_id'] }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="inputAwayTeam">Away Team</label>
                                <input type="text" class="form-control" value="{{ $rowData['away_team_name'] }}"
                                    readonly>
                                <input type="hidden" class="form-control"
                                    name="match_array[{{ $keyData }}][opponent_team_id]"
                                    value="{{ $rowData['away_team_id'] }}" readonly>
                            </div>
                        </div>
                        <!-- Date and time -->
                        <div class="form-group">
                            <label>Match Date and time</label>
                            <div class="input-group date reservationdatetime"
                                id="reservationdatetime{{ $keyData }}" data-target-input="nearest">
                                <input type="text" class="form-control datetimepicker-input"
                                    data-target="#reservationdatetime{{ $keyData }}"
                                    name="match_array[{{ $keyData }}][date]" />
                                <div class="input-group-append" data-target="#reservationdatetime{{ $keyData }}"
                                    data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                        </div>
                        <!-- /.form group -->
                    </div>
                @endforeach
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <button type="submit" class="btn btn-primary" id="btnSubmit">Submit</button>
                <a class="btn btn-primary" id="btnLook" style="display: none" target="_blank">Look Tournament Tree</a>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        //Date and time picker
        $('.reservationdatetime').datetimepicker({
            format: 'DD-MM-YYYY hh:mm A',
            icons: {
                time: 'far fa-clock'
            }
        });

        $("#formInputTournamentMatch").submit(function(e) {
            e.preventDefault();
            var formSerialize = $("#formInputTournamentMatch").serialize();
            let tournamentId = $("[name='tournament_id']").val();
            $.ajax({
                type: "POST",
                url: "{{ url('be/tournament/tree/match/store') }}",
                data: formSerialize,
                dataType: "json",
                encode: true,
                success: function(response) {
                    if (response.code == '00') {
                        alert(response.desc);
                        $("#btnLook").show();
                        $("#btnLook").attr('href',
                            "{{ url('look_tournament?tournament_id=') }}" +
                            tournamentId
                        );
                    }
                },
                error: function(error) {
                    alert(error.message);
                }
            });
        });
    });
</script>
