@extends('layouts.auth')

@section('title', 'Login')
@section('meta_description', 'Sign in to your '.($siteName ?? config('app.name')).' account.')

@section('content')
    <x-auth-card mode="login" />
@endsection
