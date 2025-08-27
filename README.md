# ✨ My Boosted Todo App

A beautiful, modern todo list application built with Laravel 12, Vue.js 3, and Inertia.js. Stay organized and productive with an aesthetic interface and powerful features.

![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)
![Vue.js](https://img.shields.io/badge/Vue.js-3.x-green.svg)
![Inertia.js](https://img.shields.io/badge/Inertia.js-2.x-purple.svg)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4.x-blue.svg)

## ✨ Features

- **Beautiful, Modern UI** - Gradient backgrounds, smooth animations, and responsive design
- **Complete CRUD Operations** - Create, read, update, and delete todos seamlessly
- **Priority Management** - 5-level priority system (Low to Urgent) with color-coded badges
- **Due Date Tracking** - Set due dates with overdue indicators
- **Smart Filtering** - Filter todos by status (All, Pending, Completed)
- **Dashboard Statistics** - Real-time overview of your todo statistics
- **Completion Toggle** - Mark todos as complete/pending with one click
- **Form Validation** - Comprehensive client and server-side validation
- **MySQL Database** - Robust data storage with proper relationships

## 🛠️ Tech Stack

- **Backend**: Laravel 12, PHP 8.2
- **Frontend**: Vue.js 3, Inertia.js
- **Styling**: Tailwind CSS 4.0
- **Database**: MySQL
- **Build Tool**: Vite
- **Code Quality**: Laravel Pint

## 🚀 Quick Start

### Prerequisites

- PHP 8.2 or higher
- Node.js 18 or higher
- MySQL
- Composer
- XAMPP (for local development)

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd my-boosted-app
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**
   ```bash
   npm install
   ```

4. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure Database**
   - Start XAMPP and ensure MySQL is running
   - Create a database named `my_boosted_app` in phpMyAdmin
   - Update your `.env` file:
     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=my_boosted_app
     DB_USERNAME=root
     DB_PASSWORD=
     ```

6. **Run Migrations**
   ```bash
   php artisan migrate
   ```

7. **Seed Sample Data (Optional)**
   ```bash
   php artisan tinker --execute="App\Models\Todo::factory()->count(10)->create();"
   ```

8. **Build Assets**
   ```bash
   npm run build
   ```

9. **Start Development Server**
   ```bash
   php artisan serve
   ```

10. **Visit your application**
    ```
    http://127.0.0.1:8000
    ```

## 🎯 Usage

### Creating Todos
1. Click the "Add New Todo" button
2. Fill in the title, description (optional), priority, and due date
3. Click "Create Todo"

### Managing Todos
- **Complete/Uncomplete**: Click the checkbox next to any todo
- **Edit**: Click the edit icon on any todo card
- **Delete**: Click the delete icon and confirm
- **View Details**: Click on any todo to see full details

### Filtering
Use the filter tabs to view:
- **All**: All todos
- **Pending**: Incomplete todos
- **Completed**: Finished todos

## 📁 Project Structure

```
my-boosted-app/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── TodoController.php
│   │   ├── Middleware/
│   │   │   └── HandleInertiaRequests.php
│   │   └── Requests/
│   │       ├── StoreTodoRequest.php
│   │       └── UpdateTodoRequest.php
│   └── Models/
│       └── Todo.php
├── database/
│   ├── factories/
│   │   └── TodoFactory.php
│   └── migrations/
│       └── *_create_todos_table.php
├── resources/
│   ├── js/
│   │   ├── Components/
│   │   │   └── TodoCard.vue
│   │   └── Pages/
│   │       └── Todos/
│   │           ├── Index.vue
│   │           ├── Create.vue
│   │           ├── Edit.vue
│   │           └── Show.vue
│   └── views/
│       └── app.blade.php
└── routes/
    └── web.php
```

## 🗃️ Database Schema

### Todos Table
- `id` - Primary key
- `title` - Todo title (required)
- `description` - Todo description (optional)
- `completed` - Completion status (boolean)
- `priority` - Priority level (1-5)
- `due_date` - Due date (optional)
- `created_at` - Creation timestamp
- `updated_at` - Last update timestamp

## 🎨 UI Components

### TodoCard Component
- Displays individual todo items
- Handles completion toggle
- Shows priority badges and due dates
- Provides edit and delete actions

### Dashboard
- Statistics overview
- Filter tabs
- Action buttons
- Empty state handling

## 📝 API Routes

| Method | URI | Action | Description |
|--------|-----|--------|-------------|
| GET | `/todos` | index | Display all todos |
| GET | `/todos/create` | create | Show create form |
| POST | `/todos` | store | Store new todo |
| GET | `/todos/{todo}` | show | Show single todo |
| GET | `/todos/{todo}/edit` | edit | Show edit form |
| PATCH | `/todos/{todo}` | update | Update todo |
| DELETE | `/todos/{todo}` | destroy | Delete todo |

## 🔧 Development Commands

```bash
# Start development server
php artisan serve

# Build assets for development
npm run dev

# Build assets for production
npm run build

# Run code formatting
vendor/bin/pint

# Generate routes for JavaScript
php artisan ziggy:generate

# Run migrations
php artisan migrate

# Fresh migration with sample data
php artisan migrate:fresh --seed
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Run code formatting: `vendor/bin/pint`
5. Submit a pull request

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
