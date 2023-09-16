<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Form Master News Category</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form id="formModalAdd">
            <input type="hidden" name="newsCategoryId"
                value="{{ isset($data['id']) && $data['id'] ? $data['id'] : null }}">
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group">
                        <label for="inputTitle">Title</label>
                        <input type="text" name="newsCategoryTitle" class="form-control" id="inputTitle"
                            placeholder="Enter News Category Title"
                            value="{{ isset($data['name']) && $data['name'] ? $data['name'] : null }}">
                    </div>
                    <div class="form-group">
                        <label for="inputDesc">Description</label>
                        <textarea name="newsCategoryDesc" class="form-control" id="inputDesc" cols="10" rows="5"
                            placeholder="Description">{{ isset($data['desc']) && $data['desc'] ? $data['desc'] : null }}</textarea>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="inputStatus" name="newsCategoryStatus"
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

<script type="text/javascript">
    $(document).ready(function() {
        $("#formModalAdd").submit(function(e) {
            e.preventDefault();
            var form = $("#formModalAdd");
            $.ajax({
                type: "POST",
                url: "{{ url('be/master/news_category/store') }}",
                data: form.serialize(),
                dataType: "json",
                encode: true,
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
