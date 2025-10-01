@extends('layouts.app')

@section('content')
  <section class="card" style="max-width:680px;margin:2rem auto;">
    <h1 style="margin-top:0;">Mi perfil</h1>

    <div style="display:grid;grid-template-columns:1fr 2fr;gap:.75rem;">
      <div style="color:#9aa1ab;">Nombre</div>
      <div>{{ auth()->user()->name }}</div>

      <div style="color:#9aa1ab;">Email</div>
      <div>{{ auth()->user()->email }}</div>

      <div style="color:#9aa1ab;">Cuenta creada</div>
      <div>{{ auth()->user()->created_at->format('Y-m-d H:i') }}</div>
    </div>

    <form action="{{ route('logout') }}" method="POST" style="margin-top:1.25rem;">
      @csrf
      <button class="btn" type="submit">Cerrar sesión</button>
    </form>
  </section>
@endsection
