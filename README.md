# STARE Management Dashboard — Complete Native PHP Edition

A complete Native PHP + MySQL + Bootstrap management system based on the supplied starter project, rebuilt around the requested specification.

## Included
- Authentication: Login / Register / Logout
- Roles: Admin / Employee / Client
- Database-backed permissions with `role_permissions`
- Admin dashboard with live statistics
- Categories CRUD
- Brands CRUD
- Products CRUD with SKU, cost/selling price, discount, stock, category, brand and partner
- Partners CRUD
- Clients CRUD linked to `users`
- Employees CRUD linked to `users`
- Employee registration with pending approval
- Cart persisted in MySQL
- Quantity update / remove / checkout
- Real order creation using DB transactions
- Stock validation and stock deduction during checkout
- Order status: Pending, Confirmed, Processing, Shipped, Delivered, Cancelled
- Order ownership: clients can only see their own orders
- Reports
- Role Permissions management UI for Employee and Client roles
- Responsive Bootstrap sidebar/offcanvas layout
- PDO prepared statements
- Password hashing
- CSRF protection on state-changing forms
- Permission checks enforced server-side, not only by hiding links

## Installation on XAMPP
1. Put the `STARE_Complete` folder inside `htdocs`.
2. Start Apache and MySQL.
3. Open phpMyAdmin.
4. Import `database/database.sql`.
5. Visit `http://localhost/STARE_Complete/`.
6. If your MySQL credentials differ from the XAMPP defaults, edit `config/config.php`.

## Demo Accounts
- Admin: `admin@stare.local` / `password`
- Employee: `employee@stare.local` / `password`
- Client: `client@stare.local` / `password`

## Important
The project intentionally uses a NEW database named `stare_management`, not the database from the supplied starter project.

The supplied project was a useful starting point, but it did not contain the requested authentication/roles/permissions/brands/partners/employees architecture. Those parts are included here and the original schema's denormalized client/password and cart/order design has been replaced with the requested relational structure.

## Permission model
Use `require_permission($pdo, 'permission_name')` in protected pages. Admin has all permissions; employee permissions are assigned through the database; clients are restricted to storefront and their own order data.

## Security notes
- Passwords use `password_hash()` and `password_verify()`.
- Database access uses PDO with prepared statements.
- POST state changes require CSRF tokens.
- Client order queries are filtered by the authenticated client's ID.
- Checkout locks cart products, validates stock, creates the order/items, deducts stock and clears the cart inside a transaction.
