<!--begin::Sidebar-->
<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
  <!--begin::Sidebar Brand-->
  <div class="sidebar-brand">
    <a href="<?= BASE_URL . 'home' ?>" class="brand-link">
      <img
        src="<?= asset('dist/assets/img/AdminLTELogo.png') ?>"
        alt="AdminLTE Logo"
        class="brand-image opacity-75 shadow"
      />
      <span class="brand-text fw-light">Quản Lý Tour</span>
    </a>
  </div>
  <!--end::Sidebar Brand-->

  <div class="sidebar-wrapper">
    <nav class="mt-2">
      <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
        
        <!-- Dashboard -->
        <li class="nav-item">
          <a href="<?= BASE_URL . 'home' ?>" class="nav-link">
            <i class="nav-icon bi bi-speedometer"></i>
            <p>Dashboard</p>
          </a>
        </li>

        <!-- Quản lý Tour -->
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon bi bi-airplane-engines"></i>
            <p>
              Quản lý Tour
              <i class="nav-arrow bi bi-chevron-right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">

              <a href="<?= BASE_URL . 'tours' ?>" class="nav-link">
                <i class="nav-icon bi bi-circle"></i>
                <p>Danh sách Tour</p>
          </a>
            </li>
            </li>
            <li class="nav-item">
              <a href="<?= BASE_URL . 'tours/create' ?>" class="nav-link">
                <i class="nav-icon bi bi-circle"></i>
                <p>Thêm Tour mới</p>
              </a>
            </li>
          </ul>
        </li>

        <!-- Quản lý Khách hàng -->
        
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon bi bi-journal-check"></i>
            <p>
              Quản lý Booking
              <i class="nav-arrow bi bi-chevron-right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="<?= BASE_URL . 'bookings' ?>" class="nav-link">
                <i class="nav-icon bi bi-circle"></i>
                <p>Danh sách Booking </p>
              </a>

            </li>
            <li class="nav-item">
               <a href="#" class="nav-link">
                  <i class="nav-icon bi bi-circle"></i>
                  <p>Thêm Booking mới</p>
               </a>
            </li>
          </ul>

        </li>
                 <?php if (isAdmin()): ?>

        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon bi bi-journal-check"></i>
            <p>
              Quản lý Danh muc
              <i class="nav-arrow bi bi-chevron-right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="<?= BASE_URL . 'categories' ?>" class="nav-link">
                <i class="nav-icon bi bi-circle"></i>
                <p>Danh sách Danh muc </p>
              </a>

            </li>
            <li class="nav-item">
              <a href="index.php?act=category-add" class="nav-link">

                <i class="nav-icon bi bi-circle"></i>
                <p>Thêm danh mục </p>
              </a>

            </li>
            
            
          </ul>
          <ul class="nav nav-treeview">
            
            
          </ul>

        </li>
                <?php endif; ?>


        <!-- Quản lý Người dùng (chỉ admin) -->
        <?php if (isAdmin()): ?>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon bi bi-person-gear"></i>
            <p>
              Quản lý Người dùng
              <i class="nav-arrow bi bi-chevron-right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="<?= BASE_URL . 'users' ?>" class="nav-link">
                <i class="nav-icon bi bi-circle"></i>
                <p>Danh sách Người dùng</p>
              </a>
            </li>
          </ul>
        </li>
        <?php endif; ?>

        <!-- Hệ thống -->
        <li class="nav-header">HỆ THỐNG</li>
        <li class="nav-item">
          <a href="<?= BASE_URL . 'logout' ?>" class="nav-link">
            <i class="nav-icon bi bi-box-arrow-right"></i>
            <p>Đăng xuất</p>
          </a>
        </li>

      </ul>
    </nav>
  </div>
</aside>
<!--end::Sidebar-->
