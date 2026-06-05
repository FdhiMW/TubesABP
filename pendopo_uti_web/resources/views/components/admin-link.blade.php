@props(['variant' => 'inline'])

@auth
    @if(auth()->user()->isAdmin())
        @if($variant === 'hero')
            <li>
                <a href="{{ route('admin.dashboard') }}" class="transition-colors hover:text-gold-soft">
                    🛡️ Admin Panel
                </a>
            </li>
        @else
            <a href="{{ route('admin.dashboard') }}"
               >
                🛡️ Admin Panel
            </a>
        @endif
    @endif
@endauth