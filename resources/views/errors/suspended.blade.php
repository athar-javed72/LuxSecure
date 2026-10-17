@extends('include.master')

@section('title', 'Account suspended')

@section('content')
<x-container class="py-10 max-w-lg mx-auto">
    <x-empty-state
        title="Account suspended"
        description="This account has been deactivated. You can no longer access LuxSecure with these credentials."
        action-label="Return to sign in"
        :action-href="route('login.form')"
    />
</x-container>
@endsection
