@extends('layouts.app')

@section('content')
<div class="container">

        <div class="row">
            <div class="col-md-5">
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
            <div class="col-md-7">
                @if($current_battle) 
                    <div id="battle"
                        data-battle-id="{{ $current_battle->id }}"
                    ></div>
                @else 
                    <div id="finder"></div>
                @endif
            </div>
        </div>

</div>
@endsection
