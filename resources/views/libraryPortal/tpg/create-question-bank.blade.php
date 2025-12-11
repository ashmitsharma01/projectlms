@extends('libraryPortal.layouts.master')
@section('content')
    @include('admin.layouts.flash-messages')
  
    @livewire('sp-question-bank', ['question' => isset($question) ? $question : null])

    
@endsection
