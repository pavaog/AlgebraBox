@extends('layouts.index')

@section('title', 'AlgebraBox | The greatest cloud storage')

@section('content')
<div class="row">
    <ol class="breadcrumb">
        <li class="active"><a href="{{ route('home.dir', '' ) }}">Home</a></li>
        @foreach ($breadcrumbs as $link)
            <li><a href="{{ route('home.dir', $link['path'] ) }}">{{ $link['name'] }}</a></li>
        @endforeach
    </ol>
</div>
<div class="row">
    <div class="col-md-3">
        <div class="list-group">
            <a href="#" class="list-group-item active" data-toggle="modal" data-target="#myModal">
                Create New Directory
            </a>
            <a href="#" class="list-group-item list-group-item-info" data-toggle="modal" data-target="#upFiles">
                Upload files</a>
        </div>
    </div>
    <div class="col-md-9">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th></th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Size</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @if($curr_dir)
                    <tr href="{{ route('home.dir', $level_up ) }}">
                        <td><a href="{{ route('home.dir', $level_up ) }}"><span class="glyphicon glyphicon-option-horizontal"></span></a></td>
                    </tr>
                @endif
                @foreach ($directories as $dirName => $dirPath)
                    <tr>
                        <td><span class="glyphicon glyphicon-folder-open"></span></td>
                        <td><a href="{{ route('home.dir', $dirPath) }}">{{ $dirName }}</a></td>
                        <td>Directory</td>
                        <td></td>
                        <td>
                            <a href="{{ route('dir.delete', $dirName ) }}" class="btn btn-delete action_confirm" data-method="delete" data-token="{{ csrf_token() }}" title="Delete">
                            <span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
                        </td>
                    </tr>
                @endforeach

                @forelse ($files as $file)
                    <tr>
                        <td><span class="glyphicon glyphicon-file"></span></td>
                            <td>{{ $file[1] }}</td>
                            <td>{{ $file[2] }}</td>
                            <td>{{ $file[3] }}</td>
                            <td>
                            <a href="{{ route('delete.file', $file[1]) }}" class="btn btn-delete action_confirm" data-method="delete" data-token="{{ csrf_token() }}" title="Delete">
                            <span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
                            <a href="{{ route('down.file', $file[1]) }}" class="btn btn-download action_confirm" title="Download">
                            <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span></a>
                        </td>
                    </tr>
                @empty
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal New Directory -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('dir.create', $curr_dir) }}" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">Create new directory</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="new-dir">Directory Name</label>
                        <input type="text" class="form-control" id="new-dir" name="new_dir" placeholder="Enter directory name" value="" autofocus>
                        <input type="hidden" name="curr_dir" value="{{ $curr_dir }}">
                    </div>
                </div>
                <div class="modal-footer">
                    {{ csrf_field() }}
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal - upload -->
<div class="modal fade" id="upFiles" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('file.upload', $curr_dir) }}" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h5 class="modal-title" id="myModalLabel">Upload files (maximum upload size 2MB per file and 8MB all together)</h5>
                </div>
                <div class="modal-body">
                  <div class="form-group">
                    <label for="up_files">Select files</label>
                    <input type="file" name="file[]" multiple>
                </div>
                </div>
                <div class="modal-footer">
                    {{ csrf_field() }}
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

@stop