  <!-- Navbar Start -->
  <nav class="navbar navbar-expand-lg bg-secondary navbar-dark sticky-top py-2 px-lg-5 wow fadeIn" data-wow-delay="0.1s">
      <!-- logo -->
      <a href="index.html" class="navbar-brand ms-4 ms-lg-0">
          <h2 class="mb-0 text-primary text-uppercase">

              <img src="{{ asset('img/Fire_Refill_logo_with_black_and_red_colors-removebg-preview.png') }}"
                  alt="HairCut Logo" style="height: 1.8em;">
              RefillEase
          </h2>
      </a>


      <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
          <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarCollapse">
          <div class="navbar-nav ms-auto p-4 p-lg-0">
              <a href="{{ route('dashboard.index') }}"
                  class="nav-item nav-link {{ request()->routeIs('dashboard.index') ? 'active' : '' }} py-2">Home</a>
              <a href="{{ route('about') }}"
                  class="nav-item nav-link {{ request()->routeIs('about') ? 'active' : '' }}  py-2">About</a>
              <a href="{{ route('user.services') }}"
                  class="nav-item nav-link {{ request()->routeIs('user.services') ? 'active' : '' }} py-2">Service</a>
              <a href="{{ route('product') }}"
                  class="nav-item nav-link {{ request()->routeIs('product') ? 'active' : '' }} py-2">Product</a>
              <a href="{{ route('contact') }}"
                  class="nav-item nav-link {{ request()->routeIs('contact') ? 'active' : '' }} py-2">Contact</a>

              @guest
                  <a href="{{ url('/login') }}" class="btn btn-primary rounded-0 py-2 px-lg-2 d-none d-lg-block">Login<i
                          class="fa fa-arrow-right ms-3"></i></a>
              @endguest

              @auth
                  <!-- Cart Icon -->
                  <a href="{{ route('cart.view') }}"
                      class="nav-link py-2 {{ request()->routeIs('cart.view') ? 'active' : '' }} px-lg-2">
                      <i class="fas fa-shopping-cart"></i>
                      <span class="badge bg-danger"></span> <!-- Cart Item Count -->
                  </a>
                  <div class="nav-item dropdown">
                      <a href="{{ route('profile') }}"
                          class="nav-link dropdown-toggle {{ request()->routeIs('profile') ? 'active' : '' }} py-2 px-lg-2"
                          data-bs-toggle="dropdown">Profile</a>
                      <div class="dropdown-menu m-0">
                          <a class="dropdown-item" href="{{ route('profile') }}">View Profile</a>
                          <a class="dropdown-item" href="{{ route('order.history') }}">Your Order</a>
                          <a class="dropdown-item" href="{{ route('change.password.form') }}"> Change Passoword</a>
                          <form action="{{ route('logout') }}" method="POST">
                              @csrf
                              <button type="submit" class="dropdown-item" style="cursor: pointer;">Logout</button>
                          </form>
                      </div>
                  </div>
              @endauth
          </div>
      </div>
  </nav>
  <!-- Navbar End -->
