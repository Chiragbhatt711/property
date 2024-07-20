<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="{{route('dashboard')}}" class="brand-link">
    <img width="" src="{{ asset('/image/socialking-logo.jpg') }}"/>
  </a>
  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ Request::is('dashboard/*') ||Request::is('dashboard') ? 'active' : '' }}">
                <i class="nav-icon fa fa-list-alt"></i>
                <p>
                    Dashboard
                </p>
            </a>
        </li>
        {{--  <li class="nav-item">
            <a href="{{ route('users.index') }}" class="nav-link {{ Request::is('users/*') ||Request::is('users') ? 'active' : '' }}">
                <i class="nav-icon fa fa-list-alt"></i>
                <p>
                    Users
                </p>
            </a>
        </li>  --}}
        <li class="nav-item">
            <a href="{{ route('property.index') }}" class="nav-link {{ Request::is('property/*') ||Request::is('property') ? 'active' : '' }}">
                <i class="nav-icon fa fa-list-alt"></i>
                <p>
                    Property
                </p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('inquiry') }}" class="nav-link {{ Request::is('inquiry/*') ||Request::is('inquiry') ? 'active' : '' }}">
                <i class="nav-icon fa fa-list-alt"></i>
                <p>
                    Inquiry
                </p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('poster.index') }}" class="nav-link {{ Request::is('poster/*') ||Request::is('poster') ? 'active' : '' }}">
                <i class="nav-icon fa fa-list-alt"></i>
                <p>
                    Poster Images
                </p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('my_profile') }}" class="nav-link {{ Request::is('my-profile/*') ||Request::is('my-profile') ? 'active' : '' }}">
                <i class="nav-icon fa fa-list-alt"></i>
                <p>
                    My Profile
                </p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('change_password') }}" class="nav-link {{ Request::is('change-password/*') ||Request::is('change-password') ? 'active' : '' }}">
                <i class="nav-icon fa fa-list-alt"></i>
                <p>
                    Change Password
                </p>
            </a>
        </li>
      </ul>
    </nav>
  </div>
</aside>
