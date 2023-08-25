<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Form Master Game</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form id="formModalAdd">
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group">
                        <label for="inputTitle">Title</label>
                        <input type="text" name="gameTitle" class="form-control" id="inputTitle"
                            placeholder="Enter Game Title">
                    </div>
                    <div class="form-group">
                        <label for="inputDesc">Description</label>
                        <textarea name="gameDesc" class="form-control" id="inputDesc" cols="10" rows="5"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="inputImage">File input</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="inputImage" name="gameImage">
                                <label class="custom-file-label" for="inputImage">Choose file</label>
                            </div>
                            <div class="input-group-append">
                                <span class="input-group-text">Upload</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="inputStatus" name="gameStatus"
                            value="1" checked>
                        <label class="form-check-label" for="inputStatus">Active</label>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <div class="modal-footer justify-content-between">
                <button type="submit" class="btn btn-primary" id="submitFormAdd">Submit</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>
    <!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->

<script type="text/javascript">
    $(document).ready(function() {
        $("#formModalAdd").submit(function(e) {
            e.preventDefault();
            var form = $("#formModalAdd");
            $.ajax({
                type: "POST",
                url: "{{ url('be/master/game/store') }}",
                data: form.serialize(),
                dataType: "json",
                encode: true,
                success: function(response) {
                    alert(response.message);
                    if (response.code == 0) {
                        // Hide Modal
                        $('#modalFormAdd').modal('hide');
                    }
                },
                error: function(error) {
                    alert(response.message);
                }
            });
        });
    });
</script>
