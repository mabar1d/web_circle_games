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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="inputMatchDate">Match Date</label>
                                <input type="text" class="form-control" id="inputMatchDate"
                                    name="match_array[{{ $keyData }}][date]" placeholder="Tournament End Date">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <button type="submit" class="btn btn-primary" id="btnSubmit">Submit</button>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $("#formInputTournamentMatch").submit(function(e) {
            e.preventDefault();
            var formSerialize = $("#formInputTournamentMatch").serialize();
            $.ajax({
                type: "POST",
                url: "{{ url('be/tournament/match/store') }}",
                data: formSerialize,
                dataType: "json",
                encode: true,
                success: function(response) {
                    if (response.code == '00') {
                        alert(response.desc);
                    }
                },
                error: function(error) {
                    alert(error.message);
                }
            });
        });
    });
</script>
