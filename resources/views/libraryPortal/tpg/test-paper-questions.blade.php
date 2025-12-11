@extends('libraryPortal.layouts.master')
@section('content')
    @include('admin.layouts.flash-messages')

 
            @livewire('sp-add-questions', ['testPaperId' => $id])
      
@endsection
