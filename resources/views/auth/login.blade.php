@extends('layouts.app')
@section('title', 'Login — Book Depot')

@section('content')
    <section style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:6rem 1.25rem 3rem">
        <div style="width:100%;max-width:440px">

            <div style="text-align:center;margin-bottom:2.5rem">
                <a href="{{ route('home') }}"
                    style="font-family:var(--font-serif);font-size:1.8rem;letter-spacing:.25em">Book Depot</a>
                <p style="margin-top:.75rem;color:var(--text-muted);font-size:.9rem">Welcome back. Sign in to your account.
                </p>
            </div>

            @if ($errors->any())
                <div
                    style="background:#8b1a1a22;border:1px solid #8b1a1a;padding:1rem 1.25rem;margin-bottom:1.5rem;font-size:.85rem;color:#8b1a1a">
                    @foreach ($errors->all() as $e)
                        <p>{{ $e }}</p>
                    @endforeach
                </div>
            @endif

            @if (session('success'))
                <div
                    style="background:#1a5c2c22;border:1px solid #1a5c2c;padding:1rem 1.25rem;margin-bottom:1.5rem;font-size:.85rem;color:#1a5c2c">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" style="display:flex;flex-direction:column;gap:1.25rem">
                @csrf
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="your@email.com"
                        autofocus />
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <div style="position:relative">
                        <input type="password" name="password" id="pwField" required placeholder="••••••••"
                            style="width:100%;padding-right:3rem" />
                        <button type="button"
                            onclick="const f=document.getElementById('pwField');f.type=f.type==='password'?'text':'password'"
                            style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:.75rem;letter-spacing:.05em;text-transform:uppercase">
                            Show
                        </button>
                    </div>
                </div>
                <label class="checkbox-label">
                    <input type="checkbox" name="remember" />
                    Remember me for 30 days
                </label>
                <button type="submit" class="btn-primary btn-full">Sign In</button>
            </form>

            <p style="text-align:center;margin-top:1.75rem;font-size:.85rem;color:var(--text-muted)">
                Don't have an account?
                <a href="{{ route('register') }}" style="color:var(--text);border-bottom:1px solid var(--text)">Create one
                    →</a>
            </p>
        </div>
    </section>
@endsection
