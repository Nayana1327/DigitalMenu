<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link" style="text-align: center;">
      <img src="" alt="" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">CMS</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">

    <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item {{ Route::is('home') ? 'menu-is-opening menu-open' : '' }}">
            <a href="/home" class="nav-link {{ Route::is('home') ? 'active' : '' }}">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
              </p>
            </a>
          </li>
          <li class="nav-item {{ (Route::is('category-list') || Route::is('category') || Route::is('portion-list')  || Route::is('portion-add')) ? 'menu-is-opening menu-open' : '' }}">
            <a href="#" class="nav-link {{ (Route::is('category-list') || Route::is('category') || Route::is('portion-list')  || Route::is('portion-add')) ? 'active' : '' }}">
              <p>
                Particulars
                <i class="right fas fa-angle-right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="/category-list" class="nav-link {{ (Route::is('category-list') || Route::is('category-add')) ? 'active' : '' }}">
                  <i class="fas fa-chevron-circle-right nav-icon"></i>
                  <p>Menu Categories</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/portion-list" class="nav-link {{ (Route::is('portion-list')  || Route::is('portion-add')) ? 'active' : '' }}">
                  <i class="fas fa-chevron-circle-right nav-icon"></i>
                  <p>Menu Portions</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/cuisine-list" class="nav-link {{ (Route::is('cuisine-list')  || Route::is('cuisine-add')) ? 'active' : '' }}">
                  <i class="fas fa-chevron-circle-right nav-icon"></i>
                  <p>Cuisines</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="/menu-list" class="nav-link {{ (Route::is('menu-add') || Route::is('menu-list')) ? 'active' : '' }}">
              <p>
                Menu
                <i class="right fas fa-angle-right"></i>
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/waiter-list" class="nav-link {{ (Route::is('waiter-list') || Route::is('waiter-add')) ? 'active' : '' }}">
              <p>
                Waiters
                <i class="right fas fa-angle-right"></i>
              </p>
            </a>
          </li>
          <li class="nav-item {{ (Route::is('table-list') || Route::is('table-add')  || Route::is('table-availability')) ? 'menu-is-opening menu-open' : '' }}">
            <a href="#" class="nav-link {{ (Route::is('table-list') || Route::is('table-add') || Route::is('table-availability')) ? 'active' : '' }}">
              <p>
                Tables
                <i class="right fas fa-angle-right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="/table-list" class="nav-link {{ (Route::is('table-list') || Route::is('table-add')) ? 'active' : '' }}">
                  <i class="fas fa-chevron-circle-right nav-icon"></i>
                  <p>List Tables</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/table-availability" class="nav-link {{ (Route::is('table-availability')) ? 'active' : '' }}">
                  <i class="fas fa-chevron-circle-right nav-icon"></i>
                  <p>Table Availability</p>
                </a>
              </li>
            </ul>
          </li>
          {{--  <li class="nav-item">
            <a href="/order-list" class="nav-link {{ (Route::is('order-list') || Route::is('')) ? 'active' : '' }}">
              <p>
               Orders
                <i class="right fas fa-angle-right"></i>
              </p>
            </a>
          </li>  --}}
          {{--  <li class="nav-item">
            <a href="/reports" class="nav-link {{ (Route::is('reports') || Route::is('export_report')) ? 'active' : '' }}">
              <p>
               Reports
                <i class="right fas fa-angle-right"></i>
              </p>
            </a>
          </li>  --}}
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
