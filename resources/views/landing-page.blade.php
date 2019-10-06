@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <a href="{{ route("github-login") }}" class="h4">Login with GitHub</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
