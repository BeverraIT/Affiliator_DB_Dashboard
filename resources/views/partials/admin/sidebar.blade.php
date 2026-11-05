@php
    $isDashboard = request()->routeIs('admin.dashboard.*');
    $isKreator   = request()->routeIs('admin.kreator.*');
    $isSampel    = request()->routeIs('admin.kirim-sampel.*');
    $isKora      = request()->routeIs('admin.kora.*');
    $isFUShip    = request()->routeIs('admin.followup.pengiriman.*');
    $isFUChat    = request()->routeIs('admin.followup.chat.*');
    $isFUKonten  = request()->routeIs('admin.followup_konten.*');
    $isToko      = request()->routeIs('admin.toko.*');
    $isProduk    = request()->routeIs('admin.product.*');
    $isKategori  = request()->routeIs('admin.category.*');
    $isStyle     = request()->routeIs('admin.content-styles.*');
    $isUsers     = request()->routeIs('admin.users.*');

    $isSuperAdmin = auth()->user()->role === 'super admin';
    $userName     = auth()->user()->name;
    $userInitial  = strtoupper(mb_substr($userName, 0, 1));
@endphp

<nav class="md-nav-drawer" role="navigation" aria-label="Navigasi utama">

    {{-- ── Header ──────────────────────────────────────────── --}}
    <div class="md-nav-header">
        <div class="md-nav-header__avatar" aria-hidden="true">
            {{ $userInitial }}
        </div>
        <div class="md-nav-header__text">
            <span class="md-nav-header__title">Database Affiliator</span>
            <span class="md-nav-header__subtitle">{{ $userName }}</span>
        </div>
    </div>

    <hr class="md-nav-divider" aria-hidden="true">

    {{-- ── Main nav ─────────────────────────────────────────── --}}
    <ul class="md-nav-list" role="list">

        <li role="listitem">
            <a href="{{ route('admin.dashboard.index') }}"
               class="md-nav-item{{ $isDashboard ? ' md-nav-item--active' : '' }}"
               @if($isDashboard) aria-current="page" @endif>
                <span class="md-nav-item__icon" aria-hidden="true">
                    @if($isDashboard)
                        <i class="bi bi-house-fill"></i>
                    @else
                        <i class="bi bi-house"></i>
                    @endif
                </span>
                <span class="md-nav-item__label">Dashboard</span>
            </a>
        </li>

        <li role="listitem">
            <a href="{{ route('admin.kreator.index') }}"
               class="md-nav-item{{ $isKreator ? ' md-nav-item--active' : '' }}"
               @if($isKreator) aria-current="page" @endif>
                <span class="md-nav-item__icon" aria-hidden="true">
                    @if($isKreator)
                        <i class="bi bi-person-fill"></i>
                    @else
                        <i class="bi bi-person"></i>
                    @endif
                </span>
                <span class="md-nav-item__label">Kreator</span>
            </a>
        </li>

        <li role="listitem">
            <a href="{{ route('admin.kirim-sampel.index') }}"
               class="md-nav-item{{ $isSampel ? ' md-nav-item--active' : '' }}"
               @if($isSampel) aria-current="page" @endif>
                <span class="md-nav-item__icon" aria-hidden="true">
                    @if($isSampel)
                        <i class="bi bi-box-seam-fill"></i>
                    @else
                        <i class="bi bi-box-seam"></i>
                    @endif
                </span>
                <span class="md-nav-item__label">Kirim Sampel</span>
            </a>
        </li>

        <li role="listitem">
            <a href="{{ route('admin.kora.index') }}"
               class="md-nav-item{{ $isKora ? ' md-nav-item--active' : '' }}"
               @if($isKora) aria-current="page" @endif>
                <span class="md-nav-item__icon" aria-hidden="true">
                    <i class="bi bi-robot"></i>{{-- bootstrap-icons tidak punya varian -fill untuk robot --}}
                </span>
                <span class="md-nav-item__label">KORA</span>
            </a>
        </li>

    </ul>

    {{-- ── Follow Up section ───────────────────────────────── --}}
    <p class="md-nav-section-label" aria-hidden="true">Follow Up</p>

    <ul class="md-nav-list" role="list">

        <li role="listitem">
            <a href="{{ route('admin.followup.pengiriman.index') }}"
               class="md-nav-item{{ $isFUShip ? ' md-nav-item--active' : '' }}"
               @if($isFUShip) aria-current="page" @endif>
                <span class="md-nav-item__icon" aria-hidden="true">
                    @if($isFUShip)
                        <i class="bi bi-truck"></i>
                    @else
                        <i class="bi bi-truck"></i>
                    @endif
                </span>
                <span class="md-nav-item__label">Pengiriman</span>
            </a>
        </li>

        <li role="listitem">
            <a href="{{ route('admin.followup.chat.index') }}"
               class="md-nav-item{{ $isFUChat ? ' md-nav-item--active' : '' }}"
               @if($isFUChat) aria-current="page" @endif>
                <span class="md-nav-item__icon" aria-hidden="true">
                    @if($isFUChat)
                        <i class="bi bi-chat-dots-fill"></i>
                    @else
                        <i class="bi bi-chat-dots"></i>
                    @endif
                </span>
                <span class="md-nav-item__label">Chat</span>
            </a>
        </li>

        <li role="listitem">
            <a href="{{ route('admin.followup_konten.index') }}"
               class="md-nav-item{{ $isFUKonten ? ' md-nav-item--active' : '' }}"
               @if($isFUKonten) aria-current="page" @endif>
                <span class="md-nav-item__icon" aria-hidden="true">
                    @if($isFUKonten)
                        <i class="bi bi-play-circle-fill"></i>
                    @else
                        <i class="bi bi-play-circle"></i>
                    @endif
                </span>
                <span class="md-nav-item__label">Konten</span>
            </a>
        </li>

    </ul>

    {{-- ── Manajemen section (super admin only) ────────────── --}}
    @if($isSuperAdmin)
        <hr class="md-nav-divider" aria-hidden="true">

        <p class="md-nav-section-label" aria-hidden="true">Manajemen</p>

        <ul class="md-nav-list" role="list">

            <li role="listitem">
                <a href="{{ route('admin.toko.index') }}"
                   class="md-nav-item{{ $isToko ? ' md-nav-item--active' : '' }}"
                   @if($isToko) aria-current="page" @endif>
                    <span class="md-nav-item__icon" aria-hidden="true">
                        @if($isToko)
                            <i class="bi bi-shop-window"></i>
                        @else
                            <i class="bi bi-shop"></i>
                        @endif
                    </span>
                    <span class="md-nav-item__label">Toko</span>
                </a>
            </li>

            <li role="listitem">
                <a href="{{ route('admin.product.index') }}"
                   class="md-nav-item{{ $isProduk ? ' md-nav-item--active' : '' }}"
                   @if($isProduk) aria-current="page" @endif>
                    <span class="md-nav-item__icon" aria-hidden="true">
                        @if($isProduk)
                            <i class="bi bi-boxes"></i>
                        @else
                            <i class="bi bi-box2"></i>
                        @endif
                    </span>
                    <span class="md-nav-item__label">Produk</span>
                </a>
            </li>

            <li role="listitem">
                <a href="{{ route('admin.category.index') }}"
                   class="md-nav-item{{ $isKategori ? ' md-nav-item--active' : '' }}"
                   @if($isKategori) aria-current="page" @endif>
                    <span class="md-nav-item__icon" aria-hidden="true">
                        @if($isKategori)
                            <i class="bi bi-grid-fill"></i>
                        @else
                            <i class="bi bi-grid"></i>
                        @endif
                    </span>
                    <span class="md-nav-item__label">Kategori</span>
                </a>
            </li>

            <li role="listitem">
                <a href="{{ route('admin.content-styles.index') }}"
                   class="md-nav-item{{ $isStyle ? ' md-nav-item--active' : '' }}"
                   @if($isStyle) aria-current="page" @endif>
                    <span class="md-nav-item__icon" aria-hidden="true">
                        @if($isStyle)
                            <i class="bi bi-palette-fill"></i>
                        @else
                            <i class="bi bi-palette"></i>
                        @endif
                    </span>
                    <span class="md-nav-item__label">Konten Style</span>
                </a>
            </li>

            <li role="listitem">
                <a href="{{ route('admin.users.index') }}"
                   class="md-nav-item{{ $isUsers ? ' md-nav-item--active' : '' }}"
                   @if($isUsers) aria-current="page" @endif>
                    <span class="md-nav-item__icon" aria-hidden="true">
                        @if($isUsers)
                            <i class="bi bi-people-fill"></i>
                        @else
                            <i class="bi bi-people"></i>
                        @endif
                    </span>
                    <span class="md-nav-item__label">User</span>
                </a>
            </li>

        </ul>
    @endif

    {{-- ── Footer: Logout ───────────────────────────────────── --}}
    <div class="md-nav-footer">
        <hr class="md-nav-divider" aria-hidden="true">
        <ul class="md-nav-list" role="list">
            <li role="listitem">
                <form action="{{ route('logout') }}" method="POST" class="md-nav-logout-form">
                    @csrf
                    <button type="submit" class="md-nav-item md-nav-item--logout">
                        <span class="md-nav-item__icon" aria-hidden="true">
                            <i class="bi bi-box-arrow-right"></i>
                        </span>
                        <span class="md-nav-item__label">Keluar</span>
                    </button>
                </form>
            </li>
        </ul>
    </div>

</nav>
