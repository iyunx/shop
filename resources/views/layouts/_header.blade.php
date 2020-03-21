<nav class="navbar navbar-expand-lg navbar-light bg-light navbar-static-top">
    <div class="container">
      <!-- Branding Image -->
      <a class="navbar-brand " href="{{ url('/') }}">
        Laravel Shop
      </a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
  
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <!-- Left Side Of Navbar -->
        <ul class="navbar-nav mr-auto">
  
        </ul>
  
        <!-- Right Side Of Navbar -->
        <ul class="navbar-nav navbar-right">
          <!-- Authentication Links -->
          @auth
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              {{Auth::user()->name}}
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="{{route('address.index')}}">收获地址</a>
              <a class="dropdown-item" href="{{route('products.favorites')}}">商品收藏</a>
              <div class="dropdown-divider"></div>
              <form action="{{route('logout')}}" method="post">
                @csrf
                <button class="dropdown-item">退出</button>
              </form>
            </div>
          </li>
          @else
          <li class="nav-item"><a class="nav-link" href="{{route('login')}}">登录</a></li>
          <li class="nav-item"><a class="nav-link" href="{{route('register')}}">注册</a></li>
          @endauth
        </ul>
      </div>
    </div>
  </nav>