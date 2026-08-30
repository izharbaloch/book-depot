@extends('layouts.app')
@section('title', 'Create Account — Book Depot')

@section('content')
    <section style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:6rem 1.25rem 3rem">
        <div style="width:100%;max-width:480px">

            <div style="text-align:center;margin-bottom:2.5rem">
                <a href="{{ route('home') }}"
                    style="font-family:var(--font-serif);font-size:1.8rem;letter-spacing:.25em">Book Depot</a>
                <p style="margin-top:.75rem;color:var(--text-muted);font-size:.9rem">Create your account and join the inner
                    circle.</p>
            </div>

            @if ($errors->any())
                <div
                    style="background:#8b1a1a22;border:1px solid #8b1a1a;padding:1rem 1.25rem;margin-bottom:1.5rem;font-size:.85rem;color:#8b1a1a">
                    @foreach ($errors->all() as $e)
                        <p>{{ $e }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" style="display:flex;flex-direction:column;gap:1.25rem">
                @csrf
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="John Doe" />
                </div>
                <div class="form-group">
                    <label>Email Address *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        placeholder="your@email.com" />
                </div>
                <div class="form-group">
                    <label>Password *</label>
                    <input type="password" name="password" required placeholder="Min. 8 characters" />
                </div>
                <div class="form-group">
                    <label>Confirm Password *</label>
                    <input type="password" name="password_confirmation" required placeholder="Repeat password" />
                </div>
                <label class="checkbox-label">
                    <input type="checkbox" required />
                    I agree to the <a href="#" style="border-bottom:1px solid var(--text)">Terms of Service</a> and <a
                        href="#" style="border-bottom:1px solid var(--text)">Privacy Policy</a>
                </label>
                <button type="submit" class="btn-primary btn-full">Create Account</button>
            </form>

            <p style="text-align:center;margin-top:1.75rem;font-size:.85rem;color:var(--text-muted)">
                Already have an account?
                <a href="{{ route('login') }}" style="color:var(--text);border-bottom:1px solid var(--text)">Sign in →</a>
            </p>
        </div>
    </section>
@endsection
