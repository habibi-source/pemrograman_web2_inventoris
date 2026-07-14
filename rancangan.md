# Rancangan Aplikasi Inventaris — Laravel + Bootstrap

## 1. Stack Teknologi yang Digunakan Coba Dua Arah Tes Ke 2 1234156
- **Framework:** Laravel 11
- **CSS Framework:** Bootstrap 5 (via Laravel UI)
- **Auth Scaffolding:** `laravel/ui` (`php artisan ui bootstrap --auth`)
- **Ikon:** Font Awesome 6 Free
- **Chart:** Chart.js
- **Database:** MySQL (via Laragon)
- **Font:** Inter + JetBrains Mono (Google Fonts)

## 2. Database Schema

### Tabel `categories`
| Column | Type | Constraint |
|--------|------|------------|
| id | bigint AI | PK |
| name | varchar(100) | NOT NULL |
| slug | varchar(100) | UNIQUE |
| timestamps | - | - |

### Tabel `items`
| Column | Type | Constraint |
|--------|------|------------|
| id | bigint AI | PK |
| item_code | varchar(50) | UNIQUE |
| name | varchar(255) | NOT NULL |
| category_id | bigint | FK → categories.id |
| unit_price | decimal(12,2) | default 0 |
| stock_level | integer | default 0 |
| status | enum('in_stock','low_stock','out_of_stock') | default 'in_stock' |
| timestamps | - | - |

### Tabel `transactions`
| Column | Type | Constraint |
|--------|------|------------|
| id | bigint AI | PK |
| type | enum('incoming','outgoing') | NOT NULL |
| item_id | bigint | FK → items.id |
| quantity | integer | NOT NULL |
| location | varchar(100) | nullable |
| reference | varchar(100) | nullable |
| user_id | bigint | FK → users.id |
| status | enum('verified','shipped','pending','completed','in_transit','damaged') | default 'pending' |
| notes | text | nullable |
| timestamps | - | - |

### Tabel `users` (tambahan)
| Column | Type | Constraint |
|--------|------|------------|
| role | enum('admin','staff','manager','auditor') | default 'staff' |
| status | enum('active','inactive') | default 'active' |
| phone | varchar(20) | nullable |
| avatar | varchar(255) | nullable |

## 3. Models & Relationships

```
Category
  ├── hasMany(Item)

Item
  ├── belongsTo(Category)
  └── hasMany(Transaction)

Transaction
  ├── belongsTo(Item)
  └── belongsTo(User)

User
  └── hasMany(Transaction)
```

## 4. Halaman & Routing

| Method | URI | Controller | View |
|--------|-----|-----------|------|
| GET | / | DashboardController@index | dashboard/index |
| GET | /items | ItemController@index | items/index |
| GET | /items/create | ItemController@create | items/create |
| POST | /items | ItemController@store | - |
| GET | /items/{id}/edit | ItemController@edit | items/edit |
| PUT | /items/{id} | ItemController@update | - |
| DELETE | /items/{id} | ItemController@destroy | - |
| GET | /categories | CategoryController@index | categories/index |
| POST | /categories | CategoryController@store | - |
| PUT | /categories/{id} | CategoryController@update | - |
| DELETE | /categories/{id} | CategoryController@destroy | - |
| GET | /transactions | TransactionController@index | transactions/index |
| POST | /transactions | TransactionController@store | - |
| GET | /reports | ReportController@index | reports/index |
| GET | /users | UserController@index | users/index |
| POST | /users | UserController@store | - |
| PUT | /users/{id} | UserController@update | - |
| DELETE | /users/{id} | UserController@destroy | - |

Semua route di-group dengan middleware `auth`.

## 5. Struktur Layout

```
┌─────────────┬──────────────────────────────────────┐
│             │         NAVBAR (fixed-top)            │
│  SIDEBAR    │  Search | Notif | User Dropdown      │
│  (fixed)    ├──────────────────────────────────────┤
│  240px      │                                       │
│             │         CONTENT (ms-240 pt-56)        │
│  - Logo     │                                       │
│  - Nav Menu │  Dashboard / Items / Categories       │
│  - Add SKU  │  Transactions / Reports / Users       │
│             │                                       │
└─────────────┴──────────────────────────────────────┘
```

## 6. Fitur per Halaman

### Dashboard
- 4 stat cards (Total SKUs, Low Stock, Pending Sync, Categories)
- Chart.js bar chart (stock movement 7 hari)
- Low stock alerts card
- Recent transactions table (5 baris)

### Items
- Table: checkbox, item_code, name, category, price, stock, status, actions
- Filter: dropdown kategori, dropdown status, search input
- Pagination
- Modal form create/edit item

### Categories
- Table: name, total items, actions
- Modal form create/edit category

### Transactions
- Tabs: All / Incoming / Outgoing
- Table: type, timestamp, SKU, location, qty, operator, status
- Modal: Record Movement (type toggle, SKU, qty, location, reference, notes)
- Pagination

### Reports
- Stat cards: Total SKU, Turnover Ratio, Active Shipments
- Chart.js trend chart
- Low stock alerts sidebar
- Historical movement table with filter/sort
- Export PDF (modal progress)

### Users
- Stat cards: Total Users, Active, Admins, Pending
- Table: avatar, name, email, role (badge), status, last login, actions
- Modal: Create/Edit User (name, email, role, status, password)

## 7. Frontend Dependencies
- bootstrap 5.x
- @fortawesome/fontawesome-free 6.x
- chart.js 4.x
- sass
- @popperjs/core
- vite

## 8. Urutan Implementasi
1. Install Laravel + Laravel UI + Bootstrap auth
2. Setup database & migrations
3. Models + relationships + seeders
4. Layout blade (sidebar + navbar)
5. Dashboard page
6. Items CRUD
7. Categories CRUD
8. Transactions page + modal
9. Reports page
10. Users CRUD
