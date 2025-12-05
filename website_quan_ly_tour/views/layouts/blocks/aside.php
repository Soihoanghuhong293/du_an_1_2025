<!--begin::Sidebar-->
<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
  <div class="sidebar-brand">
    <a href="<?= url('home') ?>" class="brand-link">
      <img src="<?= asset('dist/assets/img/AdminLTELogo.png') ?>" 
           alt="AdminLTE Logo" 
           class="brand-image opacity-75 shadow"/>
      <span class="brand-text fw-light">Quản Lý Tour</span>
    </a>
  </div>

  <div class="sidebar-wrapper">
    <nav class="mt-2">
      <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu">

        <!-- Dashboard -->
        <li class="nav-item">
          <a href="<?= url('home') ?>" class="nav-link">
            <i class="nav-icon bi bi-speedometer"></i>
            <p>Dashboard</p>
          </a>
        </li>

        <!-- Quản lý Tour -->
        <?php if (isAdmin()): ?>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon bi bi-airplane-engines"></i>
            <p>Quản lý Tour<i class="nav-arrow bi bi-chevron-right"></i></p>
          </a>

          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="<?= url('tours') ?>" class="nav-link">
                <i class="nav-icon bi bi-circle"></i><p>Danh sách Tour</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?= url('tour-add') ?>" class="nav-link">
                <i class="nav-icon bi bi-circle"></i><p>Thêm Tour mới</p>
              </a>
            </li>
          </ul>
        </li>
        <?php endif; ?>

        <!-- Quản lý Booking -->
        <?php if (isAdmin()): ?>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon bi bi-journal-check"></i>
            <p>Quản lý Booking<i class="nav-arrow bi bi-chevron-right"></i></p>
          </a>

          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="<?= url('bookings') ?>" class="nav-link">
                <i class="nav-icon bi bi-circle"></i><p>Danh sách Booking</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?= url('booking-create') ?>" class="nav-link">
                <i class="nav-icon bi bi-circle"></i><p>Thêm Booking</p>
              </a>
            </li>
          </ul>
        </li>
        <?php endif; ?>

        <!-- Quản lý Danh mục -->
        <?php if (isAdmin()): ?>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon bi bi-journal-bookmark"></i>
            <p>Quản lý Danh mục<i class="nav-arrow bi bi-chevron-right"></i></p>
          </a>

          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="<?= url('categories') ?>" class="nav-link">
                <i class="nav-icon bi bi-circle"></i><p>Danh sách Danh mục</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?= url('category-add') ?>" class="nav-link">
                <i class="nav-icon bi bi-circle"></i><p>Thêm Danh mục</p>
              </a>
            </li>
          </ul>
        </li>
        <?php endif; ?>

        <!-- Quản lý Người dùng -->
        <?php if (isAdmin()): ?>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon bi bi-people"></i>
            <p>Quản lý Người dùng<i class="nav-arrow bi bi-chevron-right"></i></p>
          </a>

          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="<?= url('users') ?>" class="nav-link">
                <i class="nav-icon bi bi-circle"></i><p>Danh sách Người dùng</p>
              </a>
            </li>
          </ul>
        </li>
        <?php endif; ?>

        <!-- Hướng dẫn viên -->
        <?php if (isGuide()): ?>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon bi bi-person-vcard"></i>
            <p>Hướng dẫn viên<i class="nav-arrow bi bi-chevron-right"></i></p>
          </a>

          <ul class="nav nav-treeview">

            <li class="nav-item">
              <a href="<?= url('guide-tours') ?>" class="nav-link">
                <i class="nav-icon bi bi-circle"></i><p>Tour được phân công</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?= url('guide-schedule') ?>" class="nav-link">
                <i class="nav-icon bi bi-circle"></i><p>Lịch làm việc</p>
              </a>
            </li>

          </ul>
        </li>
        <?php endif; ?>

        <!-- Hệ thống -->
        <li class="nav-header">HỆ THỐNG</li>
        <li class="nav-item">
          <a href="<?= url('logout') ?>" class="nav-link">
            <i class="nav-icon bi bi-box-arrow-right"></i><p>Đăng xuất</p>
          </a>
        </li>

      </ul>
    </nav>
  </div>
</aside>
<!--end::Sidebar-->
