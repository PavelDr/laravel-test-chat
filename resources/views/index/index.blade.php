@extends('layouts.master')

{{-- Web site Title --}}
@section('title')
    @parent
    :: Account Signup or Registration
@stop

@section('content')

Main Page
<br/>
@if (!Auth::check())
@if(count($errors) > 0)
    Errors:
    @foreach($errors->all() as $error)
        {{ $error }}
        <br/>
    @endforeach
@endif

Registration form
<form method="POST" action="/auth/register">
    {!! csrf_field() !!}

    <div class="col-md-6">
        {{ trans('Name') }}
        <input type="text" name="username" value="{{ old('username') }}">
    </div>

    <div>
        {{ trans('Email') }}
        <input type="email" name="email" value="{{ old('email') }}">
    </div>

    <div>
        {{ trans('Password') }}
        <input type="password" name="password">
    </div>

    <div class="col-md-6">
        {{ trans('Confirm Password') }}
        <input type="password" name="password_confirmation">
    </div>

    <div>
        <button type="submit">{{ trans('Register') }}</button>
    </div>
</form>

Login form
<!-- resources/views/auth/login.blade.php -->
<form method="POST" action="/auth/login">
    {!! csrf_field() !!}

    <div>
        {{ trans('Email or UserName') }}
        <input type="text" name="email" value="{{ old('email') }}">
    </div>

    <div>
        {{ trans('Password') }}
        <input type="password" name="password" id="password">
    </div>

        <div>
            <input type="checkbox" name="remember"> {{ trans('Remember Me') }}
        </div>

    <div>
        <button type="submit">{{ trans('Login') }}</button>
    </div>
</form>
@endif

@stop
