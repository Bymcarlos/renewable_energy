@extends('layouts.intranet')
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('home')}}"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('management') }}">Management</a></li>
    <li class="breadcrumb-item active">Users</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
    <tr class="border">
    	<td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
    	<td width="85%"><small>Users are the ... You can create new Users, edit or delete.</small></td>
    	<td width="14%" class="text-right"><a href="#" class="btn btn-primary btn-sm fa fa-file-o m-1" title="Add new user" data-toggle="modal" data-target="#add-user"></a></td>
    </tr>
</table>

<!-- List Users -->
<table class="table-bordered table-sm" id="dt_list" width="100%" cellspacing="0">
    <thead class="bg-light">
        <tr>
            <th>USER</th>
            <th>EMAIL</th>
            <th>ROLE</th>
            <th class="text-right"></th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->roles()->first()->name }}</td>
            <td class="text-right"><a href="#" class="btn btn-primary btn-sm fa fa-key" data-toggle="modal" data-target="#change-pass" onclick="changePass({{ $user->id }})" title="Change password"></a><a href="#" class="btn btn-primary btn-sm fa fa-pencil ml-1" id="user_{{$user->id}}" data-name="{{ $user->name }}" data-email="{{ $user->email }}" data-role="{{ $user->roles()->first()->id }}" data-toggle="modal" data-target="#edit-user" onclick="editUser({{ $user->id }})"></a><a href="#" class="btn btn-primary btn-sm fa fa-trash ml-1" data-toggle="modal" data-target="#delete-user" onclick="deleteUser({{ $user->id}} )"></a></td>
        </tr>
        @endforeach
    </tbody>
</table>
<!-- Create User Modal -->
<div class="modal fade" id="add-user" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Create User</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" action="{{ route('users.store') }}" method="POST">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label" for="name">User name:</label>
                        <input type="text" id="name" name="name" class="form-control" data-error="Please enter valid title." required />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="email">User email:</label>
                        <input type="text" id="email" name="email" class="form-control" data-error="Please enter valid email." required />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="password">User password:</label>
                        <input type="text" id="password" name="password" class="form-control" data-error="Please enter valid password." required />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="role">User role:</label>
                        <select class="form-control" id="role_id" name="role_id"">
                            <option disabled selected value="0">Select role</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn crud-submit btn-success">Add</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Edit User Modal -->
<div class="modal fade" id="edit-user" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Edit User</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" id="form_edit" method="POST">
                    <input type="hidden" name="_method" value="PUT" />
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label" for="name">User name:</label>
                        <input type="text" id="name" name="name" class="form-control" data-error="Please enter valid name." required />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="email">User email:</label>
                        <input type="text" id="email" name="email" class="form-control" data-error="Please enter valid email." required />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="role">User role:</label>
                        <select class="form-control" id="role_id" name="role_id"">
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn crud-submit btn-success">Update</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Change Password Modal -->
<div class="modal fade" id="change-pass" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Change pasword</h4>
            </div>
            <div class="modal-body">
                <h5 class="bg-light" id="name"></h5>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" id="form_password" method="POST">
                    <input type="hidden" name="_method" value="PUT" />
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label" for="password">New password:</label>
                        <input type="text" id="password" name="password" class="form-control" data-error="Please enter valid password." required />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn crud-submit btn-success">Update</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Delete Item Modal -->
<div class="modal fade" id="delete-user" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Confirm delete User:</h4>
            </div>
            <div class="modal-body">
                <h5 class="bg-light" id="name"></h5>
            </div>
            <div class="modal-body">
                <h5 class="bg-light" id="email"></h5>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" id="form_delete" method="POST">
                    <input type="hidden" name="_method" value="DELETE" />
                    <input type="hidden" id="id" name="id"/>
                    {{ csrf_field() }}
                    <div class="form-group">
                        <button type="submit" class="btn crud-submit btn-success">Delete</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js_custom')
<script type="text/javascript">
function editUser(item_id){
    var route = "{{ route('users.update',['user'=>':id']) }}".replace(':id', item_id);
    $("#edit-user #form_edit").attr("action",route);
    $("#edit-user #name").val($("#user_"+item_id).data("name"));
    $("#edit-user #email").val($("#user_"+item_id).data("email"));
    $("#edit-user #role_id").val($("#user_"+item_id).data("role"));
}
function deleteUser(item_id){
    var route = "{{ route('users.destroy',['user'=>':id']) }}".replace(':id', item_id);
    $("#delete-user #form_delete").attr("action",route);
    $("#delete-user #name").text($("#user_"+item_id).data("name"));
    $("#delete-user #email").text($("#user_"+item_id).data("email"));
}
function changePass(item_id){
    var route = "{{ route('users.password.change',['user'=>':id']) }}".replace(':id', item_id);
    $("#change-pass #form_password").attr("action",route);
    $("#change-pass #name").text($("#user_"+item_id).data("name"));
}

$('#dt_list').DataTable({
    "columnDefs": [ {
        "searchable": false,
        "orderable": false,
        "targets": [3]
    },
    {
        "width": "25%", 
        "targets": 0
    },
    {
        "width": "35%", 
        "targets": 1
    },
    {
        "width": "25%", 
        "targets": 2
    },
    {
        "width": "15%", 
        "targets": 3
    }],
    "dom": 'fprt<"bottom"p>',
    "order": [[ 0, 'asc' ]],
    "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]]
});
</script>
@endsection