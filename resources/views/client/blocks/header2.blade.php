<header>
    <div class="container">
        <div>
            <a href="{{ route('client.home') }}">
                <img id="image-header" src="{{ asset('assets/client/img/logo-dh-thuyloi.png') }}" alt="bg-head.png" class="img-fluid pt-2 pb-2">
            </a>
        </div>

        <div id="language-switcher" class="text-end">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" onclick="toggleLanguage()" {{ app()->getLocale() == 'en' ? 'checked' : '' }}>
            </div>
        
            {{-- <a href="{{ url('/en') }}" class="language-link">
                <img src="https://cdn.jsdelivr.net/gh/lipis/flag-icons/flags/4x3/us.svg" alt="English" style="width: 24px; height: 24px;">
            </a>
            <a href="{{ url('/vi') }}" class="language-link">
                <img src="https://cdn.jsdelivr.net/gh/lipis/flag-icons/flags/4x3/vn.svg" alt="Tiếng Việt" style="width: 24px; height: 24px;">
            </a> --}}
        </div>
    </div>
</header>


<style>
    header .container {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    #image-header {
        max-width: 350px;
        /* Adjust based on your logo size */
    }

    #language-switcher {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    #language-switcher .form-switch {
        display: flex;
        align-items: center;
    }

    #language-switcher .form-switch input {
        width: 60px !important;
        height: 30px;
        position: relative;
        background-color: transparent;
        border: 1px solid #ddd;
        border-radius: 15px!important;
        cursor: pointer;
        appearance: none;
        -webkit-appearance: none;
        outline: none;
    }

    #language-switcher .form-switch input::before {
        content: '';
        position: absolute;
        top: 3px;
        left: 3px;
        width: 24px;
        height: 24px;
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
        border-radius: 50%;
        transition: 0.3s;
    }

    #language-switcher .form-switch input:checked::before {
    left: 33px;
    background-image: url('https://cdn.jsdelivr.net/gh/lipis/flag-icons/flags/4x3/us.svg');
    /* US flag */
    }

    #language-switcher .form-switch input:not(:checked)::before {
        left: 3px;
        background-image: url('https://cdn.jsdelivr.net/gh/lipis/flag-icons/flags/4x3/vn.svg');
        /* Vietnam flag */
    }


    #nav-menu {
        display: flex;
        align-items: center;
    }

    .search-wrapper {
        position: relative;
        margin-left: auto;
    }

    .search-icon {
        cursor: pointer;
        margin-right: 10px;
    }

    .search-form {
        position: absolute;
        top: 100%;
        right: 0;
        /* background-color: #fff;
        border: 1px solid #ccc; */
        border-top: none;
        padding: 10px;
        display: none;
        z-index: 1000;
        display: flex;
        
    }

    .search-form input {
        width: 250px;
        padding: 5px;
        border: 1px solid #ddd;
        border-radius: 5px;
        flex: 1;
    }

    .search-form button {
        padding: 5px 10px;
        right: 5px;
        background-color: #024da1;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    @media (max-width: 576px) {
        .navbar-toggler {
            order: 2;
        }

        .search-wrapper {
            order: 1;
            margin-left: auto;
        }
    }
</style>
    <nav id="nav-menu" class="navbar navbar-expand-sm navbar-dark" style="background-color: rgb(2, 77, 161);">
            <div class="container">
                <a class="navbar-brand" href="{{ route('client.home') }}"><i class="fa-solid fa-house"></i></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
                    aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <ul class="navbar-nav text-uppercase text-sm-center">
                        {{-- <li class="nav-item">
                            <a href="{{ route('client.major') }}" class="nav-link text-white">Thông tin xét tuyển</a>
                        </li> --}}
                        <li class="nav-item">
                            <a href="{{ route('client.major') }}" class="nav-link text-white">{{ __('messages.nganhdaotao_') }}</a>
                        </li>

                        @php
                            $listMenu = session()->get('listMenu')
                        @endphp
                        @if(isset( $listMenu ) && is_object( $listMenu ))
                            
                            @foreach($listMenu as $value)
                                <li class="nav-item">
                                    {{-- <a href="{{ route('client.blog.category', ['slug' => $value->slug ] ) }}" class="nav-link text-white">{{ $value->name }}</a> --}}
                                    @if(app()->getLocale() == 'en')
                                    <a href="{{ route('client.blog.category', ['slug' => $value->slug]) }}" class="nav-link text-white">{{ $value->name_en }}</a>
                                    @elseif(app()->getLocale() == 'vi')
                                    <a href="{{ route('client.blog.category', ['slug' => $value->slug]) }}" class="nav-link text-white">{{ $value->name }}</a>
                                @endif
                                </li>
                            @endforeach
                        @endif
                        

                        <li class="nav-item sub-menu-main">
                            
                            <div class="nav-link text-white">{{ __('messages.tienich') }}</div>
                            
                            <div class="sub-menu">
                                    
                                    @php
                                       $listSubMenu = session()->get('listSubMenu')
                                    @endphp
                                    @if(isset( $listSubMenu ) && is_object( $listSubMenu ))
                                        @foreach($listSubMenu as $value)
                                            
                                        {{-- <a href="{{ route('client.blog.category', ['slug' => $value->slug ] ) }}" >{{ $value->name }}</a> --}}
                                        @if(app()->getLocale() == 'en')
                                        <a href="{{ route('client.blog.category', ['slug' => $value->slug]) }}" class="nav-link text-white">{{ $value->name_en }}</a>
                                        @elseif(app()->getLocale() == 'vi')
                                        <a href="{{ route('client.blog.category', ['slug' => $value->slug]) }}" class="nav-link text-white">{{ $value->name }}</a>
                                         @endif
                                         
                                        @endforeach
                                
                                    @endif
                            </div>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('client.contact') }}" class="nav-link text-white">{{ __('messages.lienhe') }}</a>
                        </li>

                        <li class="nav-item d-sm-none d-block">
                            @if(empty(Auth::user()->name))
                            <a href="{{ route('client.login') }}" class="nav-link text-white pointer">{{ __('messages.dangnhap') }}</a>

                        @else

                            <a href="{{ route('admin.logout') }}" class="nav-link text-white pointer">{{ __('messages.dangxuat') }}</a>
                        @endif
                        </li>
                        <style>
                            .sub-menu-main {
                                position: relative;
                            }

                            .sub-menu-main:hover .sub-menu{
                                display: block
                            }
                            .sub-menu {
                                position: absolute;
                                width: auto;
                                height: auto;
                                background-color: #50bbff;
                                display: none;
                                text-align: left;
                                width: 255px;
                                transition: all 0.2s ease-out;
                            }

                            
                            .sub-menu a{
                                padding: 8px 10px;
                                display: block;
                                color: #fff;
                                border-bottom: 1px solid #fff;
                            }

                            .sub-menu a:last-child{
                                
                                border-bottom: none;
                            }

                            .sub-menu a:hover{
                                color: #000000;
                                background-color: #50bbff;
                            }
                        </style>

                    </ul>
                </div>
                <div class="search-wrapper">
                    <form class="search-form" method="get" action="{{ route('client.blog.search') }}" style="display: none;">
                       
                        <input type="text" name="query" placeholder="tìm">
                        <button type="submit"> <i class="la la-search"></i> </button>
                    </form>
                    <i class="fas fa-search search-icon text-white"></i>
                </div>
                <ul class="navbar-nav text-uppercase text-sm-center d-none d-sm-block">
                    <li class="nav-item ml-auto">
                        @if(empty(Auth::user()->name))
                            <a href="{{ route('client.login') }}" class="nav-link text-white pointer">{{ __('messages.dangnhap') }}</a>

                        @else

                            <a href="{{ route('admin.logout') }}" class="nav-link text-white pointer">{{ __('messages.dangxuat') }}</a>
                        @endif
                    </li>
                    
                </ul>
            </div>
            
        </nav>
    
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const searchIcon = document.querySelector('.search-icon');
                const searchForm = document.querySelector('.search-form');
        
                searchIcon.addEventListener('click', function () {
                    searchForm.style.display = (searchForm.style.display === 'none') ? 'flex' : 'none';
                });
            });
        </script>
<script>
    function toggleLanguage() {
        var switcher = document.getElementById('flexSwitchCheckDefault');
        var currentLocale = '{{ app()->getLocale() }}';
        var newLocale = currentLocale === 'en' ? 'vi' : 'en';

        // Gửi yêu cầu POST để thay đổi ngôn ngữ
        fetch('{{ route('change.language') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}' // Đảm bảo gửi CSRF token nếu còn sử dụng
            },
            body: JSON.stringify({ locale: newLocale })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Phản hồi không ổn');
            }
            location.reload(); 
        })
        .catch(error => {
            console.error('Fetch Error:', error);
        });
    }
</script>

