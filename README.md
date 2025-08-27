# ✨ My Boosted Todo App

A beautiful, modern Kanban board todo application built with Laravel 12, Vue.js 3, and Inertia.js. Stay organized and productive with an intuitive drag-and-drop interface and powerful project management features.

![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)
![Vue.js](https://img.shields.io/badge/Vue.js-3.x-green.svg)
![Inertia.js](https://img.shields.io/badge/Inertia.js-2.x-purple.svg)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4.x-blue.svg)

## ✨ Features

- **Kanban Board Interface** - Visual workflow management with three columns: To Do, In Progress, Done
- **Drag & Drop Functionality** - Seamlessly move tasks between columns with intuitive drag-and-drop
- **Beautiful, Modern UI** - Gradient backgrounds, smooth animations, and responsive design
- **Complete CRUD Operations** - Create, read, update, and delete todos seamlessly
- **Priority Management** - 5-level priority system (Low to Urgent) with color-coded badges
- **Due Date Tracking** - Set due dates with overdue indicators
- **Status Management** - Automatic status updates when moving between columns
- **Dashboard Statistics** - Real-time overview of your todo statistics across all columns
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

### Kanban Board Workflow

The application features a three-column Kanban board:
- **To Do**: New tasks and items that haven't been started
- **In Progress**: Tasks currently being worked on
- **Done**: Completed tasks

### Managing Todos

#### Creating Todos
1. Click the "Add New Todo" button
2. Fill in the title, description (optional), priority, and due date
3. New todos automatically start in the "To Do" column

#### Moving Tasks
- **Drag & Drop**: Simply drag any todo card from one column to another
- **Status Updates**: Tasks automatically update their status based on the column they're moved to

#### Other Actions
- **Edit**: Click the edit icon on any todo card to modify details
- **Delete**: Click the delete icon and confirm removal
- **View Details**: Click on any todo to see full information

### Dashboard Statistics
View real-time statistics showing:
- Total number of todos in each column
- Overdue tasks
- Priority distribution
- Completion rates

## 🗃️ Database Schema

### Todos Table
- `id` - Primary key
- `title` - Todo title (required)
- `description` - Todo description (optional)
- `status` - Current status (todo, in_progress, done)
- `priority` - Priority level (1-5)
- `due_date` - Due date (optional)
- `created_at` - Creation timestamp
- `updated_at` - Last update timestamp

## 🎨 Key Features

### Drag & Drop Interface
- Smooth animations during drag operations
- Visual feedback when hovering over drop zones
- Automatic status updates when dropped in different columns
- Touch-friendly for mobile devices

### Status Management
- **To Do**: Initial state for new tasks
- **In Progress**: Active tasks being worked on
- **Done**: Completed tasks with visual indicators

### Priority System
Color-coded priority badges:
- **Low (1)**: Gray
- **Medium (2)**: Blue  
- **High (3)**: Yellow
- **Critical (4)**: Orange
- **Urgent (5)**: Red

## 📝 API Routes

| Method | URI | Action | Description |
|--------|-----|--------|-------------|
| GET | `/todos` | index | Display Kanban board |
| GET | `/todos/create` | create | Show create form |
| POST | `/todos` | store | Store new todo |
| GET | `/todos/{todo}` | show | Show single todo |
| GET | `/todos/{todo}/edit` | edit | Show edit form |
| PATCH | `/todos/{todo}` | update | Update todo (including status) |
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