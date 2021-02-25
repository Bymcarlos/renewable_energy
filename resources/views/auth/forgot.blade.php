@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">FORGOT PASSWORD</div>

                <div class="card-body"><strong>Please, contact with an administrator and will send your a new password as soon as possible:</strong>
                    <div class="row mt-3 p-2">
                        <div class="col-1"></div>
                        <div class="col-4">
                            <strong>NAME:</strong>
                        </div>
                        <div class="col-7">
                            <strong>EMAIL:</strong>
                        </div>
                    </div>
                    <div class="row p-2">
                        @foreach ($users as $user)
                        <div class="col-1"></div>
                        <div class="col-4">
                            {{ $user->name}}
                        </div>
                        <div class="col-7">
                            {{ $user->email}}
                        </div>
                        @endforeach
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
