@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-4 mb-2">
            @guest
                {{-- About/Sign Up--}}
                <div class="card">
                    <div class="card-header">About</div>
                    <div class="card-body">description about signing up</div>
                </div>
            @else 
                {{-- Player Profile --}}
                <div id="profile" 
                    data-first-name="nath"
                ></div> 
            @endguest
        </div>

        {{-- Battle Finder/Battle Scene --}}
        <div class="col-md-8">
            <div id="battle"></div>
        </div>
    </div>

@endsection
