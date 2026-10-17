@extends('include.master')

@section('title', 'Access denied')

@section('content')
<x-container class="py-10 max-w-lg mx-auto">
    <x-empty-state
        title="Access denied"
        description="You don’t have permission to view this page. If you believe this is a mistake, contact support or sign in with a different account."
        action-label="Back to home"
        :action-href="route('home')"
    />
</x-container>
@endsection
