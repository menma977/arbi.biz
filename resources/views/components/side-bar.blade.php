<aside class="main-sidebar sidebar-light-danger elevation-4">
  <!-- Brand Logo -->
  <a href="#" class="brand-link">
    <img src="{{ asset('logo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-1"
         style="opacity: .8">
    <div class="brand-text font-weight-light"><strong>SEO</strong> <small>Catalog</small></div>
  </a>

  <div class="sidebar">
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img src="{{ asset('logo.png') }}" class="img-circle elevation-2" alt="User Image">
      </div>
      <div class="info">
        <a href="#" class="d-block text-wrap">{{ \Illuminate\Support\Facades\Auth::user()->username }}</a>
      </div>
    </div>

    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item">
          <a href="{{ route('dashboard.index') }}" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
            <i class="nav-icon fa fa-home"></i>
            <p>
              Home
            </p>
          </a>
        </li>

        <li class="nav-item">
          <a href="{{ route('user.index') }}" class="nav-link {{ request()->is(['user', 'user/*']) ? 'active' : '' }}">
            <i class="nav-icon fa fa-users"></i>
            <p>
              Users
            </p>
          </a>
        </li>

        <li class="nav-item">
          <a href="{{ route('notification.index') }}" class="nav-link {{ request()->is(['notification', 'notification/*']) ? 'active' : '' }}">
            <i class="nav-icon fa fa-paper-plane"></i>
            <p>
              Announcement
            </p>
          </a>
        </li>

        <li class="nav-item">
          <a href="{{ route('ticket.index') }}" class="nav-link {{ request()->is(['ticket', 'ticket/*']) ? 'active' : '' }}">
            <i class="nav-icon fa fas fa-sim-card"></i>
            <p>
              Ticket
            </p>
          </a>
        </li>

        <li class="nav-item">
          <a href="{{ route('setting.index') }}" class="nav-link {{ request()->is(['setting', 'setting/*']) ? 'active' : '' }}">
            <i class="nav-icon fa fas fa-wrench"></i>
            <p>
              Setting
            </p>
          </a>
        </li>

        <li class="nav-item">
          <a href="{{ route('binary.index') }}" class="nav-link {{ request()->is(['binary', 'binary/*']) ? 'active' : '' }}">
            <i class="nav-icon fa fas fa-network-wired"></i>
            <p>
              Network
            </p>
          </a>
        </li>

        <li class="nav-item">
          <a href="{{ route('logout') }}"
             onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="nav-link">
            <i class="nav-icon fas fa-power-off"></i>
            <p>
              Logout
            </p>
          </a>
        </li>
      </ul>
    </nav>
  </div>
</aside>
