@extends('errors::minimal')

@section('title', __('Please refresh page'))
@section('code', '100')
@section('message', __('<a href="{{ url('/login') }}">Click Here to Refresh</a>'))
