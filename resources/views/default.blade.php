@extends('ItinerisPageAsPostTypeArchive::layouts.app')

@section('content')
    @php
        echo apply_filters('the_content', $content)
    @endphp
@endsection
