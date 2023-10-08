<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Form Master Game</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form id="formModalAdd">
            <input type="hidden" name="gameId" value="{{ isset($data['id']) && $data['id'] ? $data['id'] : null }}">
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group">
                        <label for="inputTitle">Title</label>
                        <input type="text" name="gameTitle" class="form-control" id="inputTitle"
                            placeholder="Enter Game Title"
                            value="{{ isset($data['title']) && $data['title'] ? $data['title'] : null }}">
                    </div>
                    <div class="form-group">
                        <label for="inputDesc">Description</label>
                        <textarea name="gameDesc" class="form-control" id="inputDesc" cols="10" rows="5" placeholder="Description">{{ isset($data['desc']) && $data['desc'] ? $data['desc'] : null }}</textarea>
                    </div>
                    @if (isset($data['game_image_url']))
                        <img src="{{ $data['game_image_url'] }}" alt="{{ $data['image'] }}" width="350"
                            height="250">
                    @endif
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
                            value="1" {{ isset($data['status']) && $data['status'] ? 'checked' : '' }}>
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
<script>
    $(function() {
        bsCustomFileInput.init();
    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $("#formModalAdd").submit(function(e) {
            e.preventDefault();
            var form = new FormData(this);
            $.ajax({
                type: "POST",
                url: "{{ url('be/master/game/store') }}",
                data: form,
                dataType: "json",
                encode: true,
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    alert(response.message);
                    if (response.code == 0) {
                        // Hide Modal
                        $('#modalFormAdd').modal('hide');
                        $('#tbl_list').DataTable().ajax.reload();
                    }
                },
                error: function(error) {
                    alert(response.message);
                }
            });
        });
    });
</script>
