<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Form News</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form id="formModalAdd">
            <input type="hidden" name="newsId" value="{{ isset($data['id']) && $data['id'] ? $data['id'] : null }}">
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group">
                        <label for="inputImage">Image</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="inputImage" name="newsImage">
                                <label class="custom-file-label" for="inputImage">Choose file</label>
                            </div>
                            <div class="input-group-append">
                                <span class="input-group-text">Upload</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputCategory">Category</label>
                        <select class="form-control select2" style="width: 100%;" name="newsCategory"
                            id="inputCategory">
                            <option value="">Select a Category</option>
                            @if (isset($data['news_category_id']) && $data['news_category_id'])
                                <option value="{{ $data['news_category_id'] }}" selected>
                                    {{ $data['news_category_name'] }}</option>
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="inputTitle">Title</label>
                        <input type="text" name="newsTitle" class="form-control" id="inputTitle"
                            placeholder="Enter Game Title"
                            value="{{ isset($data['title']) && $data['title'] ? $data['title'] : null }}">
                    </div>
                    <div class="form-group">
                        <label for="inputDesc">Content</label>
                        <textarea name="newsContent" class="form-control" id="inputDesc" cols="10" rows="5">{{ isset($data['content']) && $data['content'] ? $data['content'] : null }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="inputTags">Tags</label>
                        <select class="form-control select2" style="width: 100%;" name="newsTags[]" id="inputTags"
                            data-tags="true" data-placeholder="TAGS" multiple="multiple">
                            @if (isset($data['array_tags']) && $data['array_tags'])
                                @foreach ($data['array_tags'] as $valueTags)
                                    <option value="{{ $valueTags }}" selected>
                                        {{ $valueTags }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="inputStatus" name="newsStatus"
                            value="1" {{ isset($data['status']) && $data['status'] ? 'checked' : null }}>
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
        //Initialize Select2 Elements
        $('#inputCategory').select2({
            ajax: {
                url: "{{ url('be/master/news_category/getDropdown') }}",
                dataType: 'json',
                // placeholder: "Select a Category",
                // allowClear: true,
                data: function(params) {
                    var query = {
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

        $('#inputTags').select2({
            tags: true,
            tokenSeparators: [","],
            multiple: true,
            ajax: {
                url: "{{ url('be/master/tags/getDropdown') }}",
                dataType: "json",
                data: function(params) {
                    var query = {
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
                                id: data.name,
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

        $("#formModalAdd").submit(function(e) {
            e.preventDefault();
            var form = $("#formModalAdd");
            $.ajax({
                type: "POST",
                url: "{{ url('be/news/store') }}",
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
                    alert(error.message);
                }
            });
        });
    });
</script>
