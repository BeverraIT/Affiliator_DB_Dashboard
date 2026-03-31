<aside class="text-white p-3 d-flex flex-column"
       style="width:260px; height:100vh; position: sticky; top: 0; overflow-y: auto; background: radial-gradient(circle at 20% 0%, #0b1b3a 0%, #050b16 40%, #020617 100%);">

    <div class="p-3 mb-4 rounded-3 bg-white bg-opacity-10 shadow">
        <div class="d-flex align-items-center justify-content-between">
            <div class="overflow-hidden">
                <h6 class="mb-0 text-white fw-semibold">Database Affiliator</h6>
                <small class="text-white-50">{{ auth()->user()->name }}</small>
            </div>

            <div class="flex-shrink-0 ms-2">
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" 
                            class="btn btn-sm border-0 rounded-circle d-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger" 
                            style="width: 38px; height: 38px;"
                            title="Logout">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 0h-8A1.5 1.5 0 0 0 0 1.5v9A1.5 1.5 0 0 0 1.5 12h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z"/>
                            <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <nav class="nav flex-column gap-2">
        @php
            $isDashboard = request()->routeIs('admin.dashboard.*');
            $isKreator  = request()->routeIs('admin.kreator.*');
            $isSampel   = request()->routeIs('admin.kirim-sampel.*');
            $isFUShip   = request()->routeIs('admin.followup.pengiriman.*');
            $isFUChat   = request()->routeIs('admin.followup.chat.*');
            $isFUKonten = request()->routeIs('admin.followup_konten.*');
            $isToko     = request()->routeIs('admin.toko.*');
            $isProduk   = request()->routeIs('admin.product.*');
            $isKategori = request()->routeIs('admin.category.*');
            $isStyle    = request()->routeIs('admin.content-styles.*');
            $isUsers    = request()->routeIs('admin.users.*');

            $linkBase = 'nav-link d-flex align-items-center gap-3 px-1 py-1 rounded-pill';
            $active   = 'text-white';
            $inactive = 'text-white-50';
        @endphp
        {{-- Dashboard --}}
        <a href="{{ route('admin.dashboard.index') }}"
           class="{{ $linkBase }} {{ $isDashboard ? $active : $inactive }}"
           style="{{ $isDashboard ? 'background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); box-shadow: 0 12px 26px rgba(37,99,235,.35);' : 'background: transparent;' }}">
            <span class="rounded-circle d-inline-flex align-items-center justify-content-center"
                  style="width:28px;height:28px; background: rgba(255,255,255,.06);">
                {{-- home --}}
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 2 7.5V14a1 1 0 0 0 1 1h4a.5.5 0 0 0 .5-.5v-4h2v4a.5.5 0 0 0 .5.5h4a1 1 0 0 0 1-1V7.5a.5.5 0 0 0-.146-.354l-6-6z"/>
                </svg>
            </span>
            <span class="{{ $isDashboard ? 'fw-medium' : '' }}">Dashboard</span>
        </a>

        {{-- Kreator --}}
        <a href="{{ route('admin.kreator.index') }}"
           class="{{ $linkBase }} {{ $isKreator ? $active : $inactive }}"
           style="{{ $isKreator ? 'background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); box-shadow: 0 12px 26px rgba(37,99,235,.35);' : 'background: transparent;' }}">
            <span class="rounded-circle d-inline-flex align-items-center justify-content-center"
                  style="width:28px;height:28px; background: rgba(255,255,255,.06);">
                {{-- person --}}
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                    <path d="M14 14s-1-4-6-4-6 4-6 4 1 1 6 1 6-1 6-1z"/>
                </svg>
            </span>
            <span class="{{ $isKreator ? 'fw-medium' : '' }}">Kreator</span>
        </a>

        {{-- Kirim Sampel --}}
        <a href="{{ route('admin.kirim-sampel.index') }}"
           class="{{ $linkBase }} {{ $isSampel ? $active : $inactive }}"
           style="{{ $isSampel ? 'background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); box-shadow: 0 12px 26px rgba(37,99,235,.35);' : 'background: transparent;' }}">
            <span class="rounded-circle d-inline-flex align-items-center justify-content-center"
                  style="width:28px;height:28px; background: rgba(255,255,255,.06);">
                {{-- box/parcel --}}
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8.186 1.113a1 1 0 0 0-.372 0l-5 1.25A1 1 0 0 0 2 3.333V13a1 1 0 0 0 .757.97l5 1.25a1 1 0 0 0 .486 0l5-1.25A1 1 0 0 0 14 13V3.333a1 1 0 0 0-.814-.97l-5-1.25zM8 2.12 12.5 3.25 8 4.38 3.5 3.25 8 2.12z"/>
                    <path d="M3 4.06v8.57l4.5 1.12V5.18L3 4.06zm10 0L8.5 5.18v8.57L13 12.63V4.06z"/>
                </svg>
            </span>
            <span class="{{ $isSampel ? 'fw-medium' : '' }}">Kirim Sampel</span>
        </a>

        {{-- Follow Up - Pengiriman --}}
        <a href="{{ route('admin.followup.pengiriman.index') }}"
           class="{{ $linkBase }} {{ $isFUShip ? $active : $inactive }}"
           style="{{ $isFUShip ? 'background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); box-shadow: 0 12px 26px rgba(37,99,235,.35);' : 'background: transparent;' }}">
            <span class="rounded-circle d-inline-flex align-items-center justify-content-center"
                  style="width:28px;height:28px; background: rgba(255,255,255,.06);">
                {{-- truck --}}
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M0 3a1 1 0 0 1 1-1h9a1 1 0 0 1 1 1v6h1.5a1 1 0 0 1 .8.4l1.5 2A1 1 0 0 1 15.5 13H14a2 2 0 1 1-4 0H6a2 2 0 1 1-4 0H1a1 1 0 0 1-1-1V3z"/>
                </svg>
            </span>
            <span class="{{ $isFUShip ? 'fw-medium' : '' }}">Follow Up - Pengiriman</span>
        </a>

        {{-- Follow Up - Chat --}}
        <a href="{{ route('admin.followup.chat.index') }}"
           class="{{ $linkBase }} {{ $isFUChat ? $active : $inactive }}"
           style="{{ $isFUChat ? 'background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); box-shadow: 0 12px 26px rgba(37,99,235,.35);' : 'background: transparent;' }}">
            <span class="rounded-circle d-inline-flex align-items-center justify-content-center"
                  style="width:28px;height:28px; background: rgba(255,255,255,.06);">
                {{-- Icon: Chat Dots --}}
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M5 8a1 1 0 1 1-0-2 1 1 0 0 1 0 2zm4 0a1 1 0 1 1-0-2 1 1 0 0 1 0 2zm3 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                    <path d="m2.165 15.803.02-.004c1.83-.363 2.948-.842 3.468-1.105A9.06 9.06 0 0 0 8 15c4.418 0 8-3.134 8-7s-3.582-7-8-7-8 3.134-8 7c0 1.76.743 3.37 1.97 4.6-.097 1.016-.417 2.13-.771 2.966-.079.186.074.394.273.362 2.256-.37 3.597-.938 4.18-1.234A9.06 9.06 0 0 0 8 15z"/>
                </svg>
            </span>
            <span class="{{ $isFUChat ? 'fw-medium' : '' }}">Follow Up - Chat</span>
        </a>

        {{-- Follow Up - Konten --}}
        <a href="{{ route('admin.followup_konten.index') }}"
           class="{{ $linkBase }} {{ $isFUKonten ? $active : $inactive }}"
           style="{{ $isFUKonten ? 'background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); box-shadow: 0 12px 26px rgba(37,99,235,.35);' : 'background: transparent;' }}">
            <span class="rounded-circle d-inline-flex align-items-center justify-content-center"
                  style="width:28px;height:28px; background: rgba(255,255,255,.06);">
                {{-- play --}}
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M6.79 5.093A.5.5 0 0 1 7.5 5.5v5a.5.5 0 0 1-.71.457l-3-1.5A.5.5 0 0 1 3.5 9V7a.5.5 0 0 1 .29-.457l3-1.5z"/>
                    <path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8 7A7 7 0 1 0 8 1a7 7 0 0 0 0 14z"/>
                </svg>
            </span>
            <span class="{{ $isFUKonten ? 'fw-medium' : '' }}">Follow Up - Konten</span>
        </a>

        @if(auth()->user()->role === 'super admin')
            {{-- Toko --}}
            <a href="{{ route('admin.toko.index') }}"
            class="{{ $linkBase }} {{ $isToko ? $active : $inactive }}"
            style="{{ $isToko ? 'background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); box-shadow: 0 12px 26px rgba(37,99,235,.35);' : 'background: transparent;' }}">
                <span class="rounded-circle d-inline-flex align-items-center justify-content-center"
                    style="width:28px;height:28px; background: rgba(255,255,255,.06);">
                    {{-- shop --}}
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M2.97 1.35A1 1 0 0 1 3.73 1h8.54a1 1 0 0 1 .76.35l2 2.5A1 1 0 0 1 15 4.5V6a2 2 0 0 1-2 2H3A2 2 0 0 1 1 6V4.5a1 1 0 0 1 .24-.65l1.73-2.5z"/>
                        <path d="M2 9h12v5a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V9z"/>
                    </svg>
                </span>
                <span class="{{ $isToko ? 'fw-medium' : '' }}">Toko</span>
            </a>

            {{-- Produk --}}
            <a href="{{ route('admin.product.index') }}"
            class="{{ $linkBase }} {{ $isProduk ? $active : $inactive }}"
            style="{{ $isProduk ? 'background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); box-shadow: 0 12px 26px rgba(37,99,235,.35);' : 'background: transparent;' }}">
                <span class="rounded-circle d-inline-flex align-items-center justify-content-center"
                    style="width:28px;height:28px; background: rgba(255,255,255,.06);">
                    {{-- cube --}}
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8.186 1.113a1 1 0 0 0-.372 0l-5 1.25A1 1 0 0 0 2 3.333V13a1 1 0 0 0 .757.97l5 1.25a1 1 0 0 0 .486 0l5-1.25A1 1 0 0 0 14 13V3.333a1 1 0 0 0-.814-.97l-5-1.25z"/>
                    </svg>
                </span>
                <span class="{{ $isProduk ? 'fw-medium' : '' }}">Produk</span>
            </a>

            {{-- Kategori --}}
            <a href="{{ route('admin.category.index') }}"
            class="{{ $linkBase }} {{ $isKategori ? $active : $inactive }}"
            style="{{ $isKategori ? 'background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); box-shadow: 0 12px 26px rgba(37,99,235,.35);' : 'background: transparent;' }}">
                <span class="rounded-circle d-inline-flex align-items-center justify-content-center"
                    style="width:28px;height:28px; background: rgba(255,255,255,.06);">
                    {{-- grid --}}
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M2 2h5v5H2V2zm7 0h5v5H9V2zM2 9h5v5H2V9zm7 0h5v5H9V9z"/>
                    </svg>
                </span>
                <span class="{{ $isKategori ? 'fw-medium' : '' }}">Kategori</span>
            </a>

            {{-- Konten Style --}}
            <a href="{{ route('admin.content-styles.index') }}"
            class="{{ $linkBase }} {{ $isStyle ? $active : $inactive }}"
            style="{{ $isStyle ? 'background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); box-shadow: 0 12px 26px rgba(37,99,235,.35);' : 'background: transparent;' }}">
                <span class="rounded-circle d-inline-flex align-items-center justify-content-center"
                    style="width:28px;height:28px; background: rgba(255,255,255,.06);">
                    {{-- chat --}}
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 1c3.866 0 7 2.239 7 5s-3.134 5-7 5a9.9 9.9 0 0 1-1.667-.14L3 13l.86-2.15C2.15 9.84 1 8.53 1 6c0-2.761 3.134-5 7-5z"/>
                    </svg>
                </span>
                <span class="{{ $isStyle ? 'fw-medium' : '' }}">Konten Style</span>
            <a href="{{ route('admin.users.index') }}"
               class="{{ $linkBase }} {{ $isUsers ? $active : $inactive }}"
               style="{{ $isUsers ? 'background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); box-shadow: 0 12px 26px rgba(37,99,235,.35);' : 'background: transparent;' }}">
                <span class="rounded-circle d-inline-flex align-items-center justify-content-center"
                      style="width:28px;height:28px; background: rgba(255,255,255,.06);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7Zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm-5.784 6A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216ZM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/>
                    </svg>
                </span>
                <span class="{{ $isUsers ? 'fw-medium' : '' }}">User</span>
            </a>
        @endif
    </nav>
</aside>