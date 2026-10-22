@extends('layouts.auth')

@section('title', 'Forgot password')
@section('meta_description', 'Reset your '.($siteName ?? config('app.name')).' password.')

@section('content')
    <x-auth-card mode="forgot" />
@endsection
