@extends('layouts.auth')

@section('title', 'Create account')
@section('meta_description', 'Create your '.($siteName ?? config('app.name')).' account.')

@section('content')
    <x-auth-card mode="register" />
@endsection
