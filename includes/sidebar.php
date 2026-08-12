<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <!--begin::Sidebar Brand-->
    <div class="sidebar-brand">
        <!--begin::Brand Link-->
        <a href="<?= BASE_URL ?>/index.html" class="brand-link">
            <!--begin::Brand Image-->
            <img
                src="<?= BASE_URL ?>/assets/img/AdminLTELogo.png"
                alt="AdminLTE Logo"
                class="brand-image opacity-75 shadow" />
            <!--end::Brand Image-->
            <!--begin::Brand Text-->
            <span class="brand-text fw-light">AdminLTE 4</span>
            <!--end::Brand Text-->
        </a>
        <!--end::Brand Link-->
    </div>
    <!--end::Sidebar Brand-->
    <!--begin::Sidebar Search-->
    <div class="sidebar-search" role="search">
        <label for="sidebar-search-input" class="visually-hidden">Filter menu</label>
        <input
            type="search"
            id="sidebar-search-input"
            class="form-control form-control-sm"
            placeholder="Filter menu…"
            autocomplete="off"
            data-lte-toggle="sidebar-search"
            data-lte-target="#navigation" />
        <p class="fs-7 text-secondary mt-2 mb-0" data-lte-search-empty role="status" hidden>
            No matching pages.
        </p>
    </div>
    <!--end::Sidebar Search-->
    <!--begin::Sidebar Wrapper-->
    <div class="sidebar-wrapper">
        <nav class="mt-2" aria-label="Main navigation">
            <!--begin::Sidebar Menu-->
            <ul
                class="nav sidebar-menu flex-column"
                data-lte-toggle="treeview"
                data-accordion="false"
                id="navigation">
                <!-- Dashboard link -->
                <li class="nav-item">
                    <?php if (has_permission($pdo, 'view_dashboard')): ?>
                        <a href="<?= BASE_URL ?>/index.php" class="nav-link <?= ($active === 'dashboard' ? 'active' : '') ?>">
                            <i class="nav-icon bi bi-speedometer"></i>
                            <p>
                                Dashboard
                            </p>
                        </a>
                    <?php endif; ?>
                </li>

                <!-- Categories link -->
                <li class="nav-item">
                    <?php if (has_permission($pdo, 'view_categories')): ?>
                        <a href="<?= BASE_URL ?>/admin/categories/index.php" class="nav-link <?= ($active === 'categories' ? 'active' : '') ?>">
                            <i class="nav-icon bi bi-tags"></i>
                            <p>
                                Categories
                            </p>
                        </a>
                    <?php endif; ?>
                </li>

                <!-- Brands link -->
                <li class="nav-item">
                    <?php if (has_permission($pdo, 'view_brands')): ?>
                        <a href="<?= BASE_URL ?>/admin/brands/index.php" class="nav-link <?= ($active === 'brands' ? 'active' : '') ?>">
                            <i class="nav-icon bi bi-bookmark-star"></i>
                            <p>
                                Brands
                            </p>
                        </a>
                    <?php endif; ?>
                </li>


                <!-- Products link -->
                <li class="nav-item">
                    <?php if (has_permission($pdo, 'view_products')): ?>
                        <a href="<?= BASE_URL ?>/admin/products/index.php" class="nav-link <?= ($active === 'products' ? 'active' : '') ?>">
                            <i class="nav-icon bi bi-box-seam"></i>
                            <p>
                                Products
                            </p>
                        </a>
                    <?php endif; ?>
                </li>

                <!-- Partners link -->
                <li class="nav-item">
                    <?php if (has_permission($pdo, 'view_partners')): ?>
                        <a href="<?= BASE_URL ?>/admin/partners/index.php" class="nav-link <?= ($active === 'partners' ? 'active' : '') ?>">
                            <i class="nav-icon bi bi-building"></i>
                            <p>
                                Partners
                            </p>
                        </a>
                    <?php endif; ?>
                </li>

                <!-- Clients link -->
                <li class="nav-item">
                    <?php if (has_permission($pdo, 'view_clients')): ?>
                        <a href="<?= BASE_URL ?>/admin/clients/index.php" class="nav-link <?= ($active === 'clients' ? 'active' : '') ?>">
                            <i class="nav-icon bi bi-people"></i>
                            <p>
                                Clients
                            </p>
                        </a>
                    <?php endif; ?>
                </li>


                <!-- Employees link -->
                <li class="nav-item">
                    <?php if (has_permission($pdo, 'view_employees')): ?>
                        <a href="<?= BASE_URL ?>/admin/employees/index.php" class="nav-link <?= ($active === 'employees' ? 'active' : '') ?>">
                            <i class="nav-icon bi bi-person-badge"></i>
                            <p>
                                Employees
                            </p>
                        </a>
                    <?php endif; ?>
                </li>


                <!-- Orders link -->
                <li class="nav-item">
                    <?php if (has_permission($pdo, 'view_orders')): ?>
                        <a href="<?= BASE_URL ?>/admin/orders/index.php" class="nav-link <?= ($active === 'orders' ? 'active' : '') ?>">
                            <i class="nav-icon bi bi-receipt"></i>
                            <p>
                                Orders
                            </p>
                        </a>
                    <?php endif; ?>
                </li>


                <!-- Reports link -->
                <li class="nav-item">
                    <?php if (has_permission($pdo, 'view_reports')): ?>
                        <a href="<?= BASE_URL ?>/admin/reports/index.php" class="nav-link <?= ($active === 'reports' ? 'active' : '') ?>">
                            <i class="nav-icon bi bi-bar-chart"></i>
                            <p>
                                Reports
                            </p>
                        </a>
                    <?php endif; ?>
                </li>

                <!-- Users link -->
                <li class="nav-item">
                    <?php if (has_permission($pdo, 'manage_users')): ?>
                        <a href="<?= BASE_URL ?>/admin/users/index.php" class="nav-link <?= ($active === 'users' ? 'active' : '') ?>">
                            <i class="nav-icon bi bi-person-gear"></i>
                            <p>
                                Users
                            </p>
                        </a>
                    <?php endif; ?>
                </li>

                <!-- Permissions link -->
                <li class="nav-item">
                    <?php if (has_permission($pdo, 'manage_permissions')): ?>
                        <a href="<?= BASE_URL ?>/admin/permissions/index.php" class="nav-link <?= ($active === 'permissions' ? 'active' : '') ?>">
                            <i class="nav-icon bi bi-shield-lock"></i>
                            <p>
                                Permissions
                            </p>
                        </a>
                    <?php endif; ?>
                </li>

                <!-- Shop link -->
                <li class="nav-item">
                    <?php if (user_role($pdo) === 'client'): ?>
                        <a href="<?= BASE_URL ?>/client/products.php" class="nav-link <?= ($active === 'shop' ? 'active' : '') ?>">
                            <i class="nav-icon bi bi-shop"></i>
                            <p>
                                Shop
                            </p>
                        </a>
                    <?php endif; ?>
                </li>

                <!-- Cart link -->
                <li class="nav-item">
                    <?php if (user_role($pdo) === 'client'): ?>
                        <a href="<?= BASE_URL ?>/client/cart.php" class="nav-link <?= ($active === 'cart' ? 'active' : '') ?>">
                            <i class="nav-icon bi bi-cart3"></i>
                            <p>
                                Cart
                                <span class="badge rounded-pill bg-light text-dark ms-auto">
                                    <?= cart_count($pdo, (int)client_id_for_user($pdo, (int)$user['id'])) ?>
                                </span>
                            </p>
                        </a>
                    <?php endif; ?>
                </li>

                <!-- My Orders link -->
                <li class="nav-item">
                    <?php if (user_role($pdo) === 'client'): ?>
                        <a href="<?= BASE_URL ?>/client/orders.php" class="nav-link <?= ($active === 'myorders' ? 'active' : '') ?>">
                            <i class="nav-icon bi bi-bag-check"></i>
                            <p>
                                My Orders
                            </p>
                        </a>
                    <?php endif; ?>
                </li>
            </ul>
            <!--end::Sidebar Menu-->
        </nav>
    </div>
    <!--end::Sidebar Wrapper-->
</aside>