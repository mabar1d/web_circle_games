<div class="card-body table-responsive p-0">
    <table class="table table-hover text-nowrap">
        <thead>
            <tr>
                <th>Title</th>
                <th>Register Start</th>
                <th>Register End</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Number Of Participants</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($listData as $rowData)
            <tr>
                <td>{{$rowData["name"]}}</td>
                <td>{{$rowData["register_date_start"]}}</td>
                <td>{{$rowData["register_date_end"]}}</td>
                <td>{{$rowData["start_date"]}}</td>
                <td>{{$rowData["end_date"]}}</td>
                <td>{{$rowData["number_of_participants"]}}</td>
                <td>
                    <button type="button" class="btn btn-warning" id="btn_edit" value="{{ $rowData['id'] }}"><i class="far fa-edit"></i></button>
                    <button type="button" class="btn btn-danger" id="btn_delete" value="{{ $rowData['id'] }}"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>