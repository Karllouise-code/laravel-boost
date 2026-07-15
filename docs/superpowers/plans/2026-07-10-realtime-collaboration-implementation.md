# Real-Time Collaborative Kanban — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add real-time updates to the kanban board so collaborators see todo changes and presence instantly without refreshing.

**Architecture:** Pusher free tier hosts the websocket server. Laravel broadcasts events via `ShouldBroadcast` on private/presence channels. Vue frontend subscribes via Laravel Echo and updates reactive state.

**Tech Stack:** `pusher/pusher-php-server`, `laravel-echo`, `pusher-js`, Laravel Broadcasts trait, Vue 3 Composition API

## Global Constraints

- PHP 8.2+, Laravel 12, Vue 3 (Composition API, plain JS — no TypeScript)
- No ESLint, no TypeScript — Vue SFCs use `<script setup>` with plain JS
- Tests: PHPUnit (not Pest), SQLite `:memory:`, run via `composer run test`
- Code formatting: `vendor/bin/pint --dirty` before committing
- Run `php artisan ziggy:generate` after editing `routes/web.php`

---

## File Structure

### New files
| File | Responsibility |
|------|---------------|
| `app/Events/TodoCreated.php` | Broadcast when a todo is created |
| `app/Events/TodoUpdated.php` | Broadcast when a todo is updated |
| `app/Events/TodoDeleted.php` | Broadcast when a todo is deleted |
| `app/Events/TodoReordered.php` | Broadcast when a todo is dragged to new column/position |
| `routes/channels.php` | Channel authorization for private/presence channels |
| `resources/js/echo.js` | Initialize Laravel Echo with Pusher |
| `tests/Feature/BroadcastTest.php` | Test events dispatch on correct channels |

### Modified files
| File | Change |
|------|--------|
| `app/Http/Controllers/TodoController.php` | Add `reorder` method, dispatch events in store/update/destroy |
| `app/Models/Board.php` | Add `Broadcasts` trait |
| `bootstrap/app.php` | Register broadcast routes |
| `resources/js/app.js` | Import echo.js |
| `resources/js/Pages/Boards/Show.vue` | Subscribe to channels, online indicators, optimistic drag-drop |
| `routes/web.php` | Add reorder route |
| `.env.example` | Add Pusher env vars |
| `composer.json` | Add pusher/pusher-php-server |
| `package.json` | Add laravel-echo, pusher-js |

---

### Task 1: Install packages and configure Pusher

**Files:**
- Modify: `composer.json`
- Modify: `package.json`
- Modify: `.env.example`
- Modify: `bootstrap/app.php`

**Interfaces:**
- Consumes: nothing (first task)
- Produces: Pusher env vars available, broadcast routes registered

- [ ] **Step 1: Install Pusher PHP server package**

```bash
composer require pusher/pusher-php-server
```

- [ ] **Step 2: Install frontend packages**

```bash
npm install laravel-echo pusher-js
```

- [ ] **Step 3: Add Pusher env vars to `.env.example`**

Add these lines after `BROADCAST_CONNECTION=log`:

```
BROADCAST_CONNECTION=pusher

PUSHER_APP_ID=your-app-id
PUSHER_APP_KEY=your-app-key
PUSHER_APP_SECRET=your-app-secret
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

Also change the first `BROADCAST_CONNECTION` line from `log` to `pusher`.

- [ ] **Step 4: Register broadcast routes in `bootstrap/app.php`**

Add `channels: __DIR__.'/../routes/channels.php'` to the `withRouting` call:

```php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
```

- [ ] **Step 5: Run tests to verify nothing broke**

```bash
composer run test
```

Expected: All existing tests pass (no behavior changed yet).

- [ ] **Step 6: Commit**

```bash
git add composer.json composer.lock package.json package-lock.json .env.example bootstrap/app.php
git commit -m "chore: install pusher packages and configure broadcasting"
```

---

### Task 2: Create broadcast events

**Files:**
- Create: `app/Events/TodoCreated.php`
- Create: `app/Events/TodoUpdated.php`
- Create: `app/Events/TodoDeleted.php`
- Create: `app/Events/TodoReordered.php`
- Test: `tests/Feature/BroadcastTest.php`

**Interfaces:**
- Consumes: `App\Models\Todo`, `App\Models\Board`
- Produces: 4 event classes that implement `ShouldBroadcast` and broadcast on `board.{slug}` private channels

- [ ] **Step 1: Create `app/Events/TodoCreated.php`**

```php
<?php

namespace App\Events;

use App\Models\Todo;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TodoCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Todo $todo)
    {
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('board.'.$this->todo->board->slug)];
    }

    public function broadcastAs(): string
    {
        return 'TodoCreated';
    }
}
```

- [ ] **Step 2: Create `app/Events/TodoUpdated.php`**

```php
<?php

namespace App\Events;

use App\Models\Todo;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TodoUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Todo $todo)
    {
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('board.'.$this->todo->board->slug)];
    }

    public function broadcastAs(): string
    {
        return 'TodoUpdated';
    }
}
```

- [ ] **Step 3: Create `app/Events/TodoDeleted.php`**

```php
<?php

namespace App\Events;

use App\Models\Todo;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TodoDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public int $id, public string $boardSlug)
    {
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('board.'.$this->boardSlug)];
    }

    public function broadcastAs(): string
    {
        return 'TodoDeleted';
    }

    public function broadcastWith(): array
    {
        return ['id' => $this->id, 'board_slug' => $this->boardSlug];
    }
}
```

- [ ] **Step 4: Create `app/Events/TodoReordered.php`**

```php
<?php

namespace App\Events;

use App\Models\Todo;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TodoReordered implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Todo $todo)
    {
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('board.'.$this->todo->board->slug)];
    }

    public function broadcastAs(): string
    {
        return 'TodoReordered';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->todo->id,
            'status' => $this->todo->status,
            'priority' => $this->todo->priority,
            'board_slug' => $this->todo->board->slug,
        ];
    }
}
```

- [ ] **Step 5: Write broadcast tests in `tests/Feature/BroadcastTest.php`**

```php
<?php

namespace Tests\Feature;

use App\Events\TodoCreated;
use App\Events\TodoDeleted;
use App\Events\TodoReordered;
use App\Events\TodoUpdated;
use App\Models\Board;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Broadcast;
use Tests\TestCase;

class BroadcastTest extends TestCase
{
    use RefreshDatabase;

    public function test_todo_created_event_broadcasts_on_correct_channel()
    {
        $user = User::factory()->create();
        $board = Board::factory()->create(['owner_id' => $user->id]);
        $board->collaborators()->attach($user->id);
        $todo = Todo::factory()->create(['board_id' => $board->id]);

        $event = new TodoCreated($todo);

        $channels = $event->broadcastOn();
        $this->assertEquals('board.'.$board->slug, $channels[0]->name);
    }

    public function test_todo_updated_event_broadcasts_on_correct_channel()
    {
        $user = User::factory()->create();
        $board = Board::factory()->create(['owner_id' => $user->id]);
        $board->collaborators()->attach($user->id);
        $todo = Todo::factory()->create(['board_id' => $board->id]);

        $event = new TodoUpdated($todo);

        $channels = $event->broadcastOn();
        $this->assertEquals('board.'.$board->slug, $channels[0]->name);
    }

    public function test_todo_deleted_event_broadcasts_on_correct_channel()
    {
        $user = User::factory()->create();
        $board = Board::factory()->create(['owner_id' => $user->id]);
        $board->collaborators()->attach($user->id);

        $event = new TodoDeleted(1, $board->slug);

        $channels = $event->broadcastOn();
        $this->assertEquals('board.'.$board->slug, $channels[0]->name);
        $this->assertEquals(['id' => 1, 'board_slug' => $board->slug], $event->broadcastWith());
    }

    public function test_todo_reordered_event_broadcasts_on_correct_channel()
    {
        $user = User::factory()->create();
        $board = Board::factory()->create(['owner_id' => $user->id]);
        $board->collaborators()->attach($user->id);
        $todo = Todo::factory()->create(['board_id' => $board->id, 'status' => 'done', 'priority' => 1]);

        $event = new TodoReordered($todo);

        $channels = $event->broadcastOn();
        $this->assertEquals('board.'.$board->slug, $channels[0]->name);
        $this->assertEquals([
            'id' => $todo->id,
            'status' => 'done',
            'priority' => 1,
            'board_slug' => $board->slug,
        ], $event->broadcastWith());
    }

    public function test_events_implement_should_broadcast()
    {
        $user = User::factory()->create();
        $board = Board::factory()->create(['owner_id' => $user->id]);
        $board->collaborators()->attach($user->id);
        $todo = Todo::factory()->create(['board_id' => $board->id]);

        $this->assertInstanceOf(\Illuminate\Contracts\Broadcasting\ShouldBroadcast::class, new TodoCreated($todo));
        $this->assertInstanceOf(\Illuminate\Contracts\Broadcasting\ShouldBroadcast::class, new TodoUpdated($todo));
        $this->assertInstanceOf(\Illuminate\Contracts\Broadcasting\ShouldBroadcast::class, new TodoReordered($todo));
        $this->assertInstanceOf(\Illuminate\Contracts\Broadcasting\ShouldBroadcast::class, new TodoDeleted($todo->id, $board->slug));
    }
}
```

- [ ] **Step 6: Run tests**

```bash
composer run test
```

Expected: All tests pass including 6 new broadcast tests.

- [ ] **Step 7: Commit**

```bash
git add app/Events/ tests/Feature/BroadcastTest.php
git commit -m "feat: add broadcast events for todo CRUD and reorder"
```

---

### Task 3: Add reorder endpoint and dispatch events from TodoController

**Files:**
- Modify: `app/Http/Controllers/TodoController.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/BroadcastTest.php` (add reorder tests)

**Interfaces:**
- Consumes: `TodoCreated`, `TodoUpdated`, `TodoDeleted`, `TodoReordered` events from Task 2
- Produces: `PATCH /boards/{slug}/todos/reorder` route, events dispatched in store/update/destroy

- [ ] **Step 1: Add reorder route to `routes/web.php`**

Add BEFORE the `Route::patch('/boards/{slug}/todos/{todo}')` line (to avoid route conflict):

```php
Route::patch('/boards/{slug}/todos/reorder', [TodoController::class, 'reorder'])
    ->middleware('board.access')->name('todos.reorder');
```

- [ ] **Step 2: Update `app/Http/Controllers/TodoController.php`**

Add use statements at top:

```php
use App\Events\TodoCreated;
use App\Events\TodoDeleted;
use App\Events\TodoReordered;
use App\Events\TodoUpdated;
use App\Http\Requests\ReorderTodoRequest;
use Illuminate\Http\JsonResponse;
```

Update the `store` method to dispatch event:

```php
public function store(StoreTodoRequest $request, string $slug): RedirectResponse
{
    $board = Board::where('slug', $slug)->firstOrFail();

    $todo = $board->todos()->create($request->validated());

    TodoCreated::dispatch($todo);

    return redirect()
        ->route('boards.show', $slug)
        ->with('message', 'Todo created successfully!');
}
```

Update the `update` method to dispatch event:

```php
public function update(UpdateTodoRequest $request, string $slug, Todo $todo): RedirectResponse
{
    $todo->update($request->validated());

    TodoUpdated::dispatch($todo);

    return redirect()
        ->route('boards.show', $slug)
        ->with('message', 'Todo updated successfully!');
}
```

Update the `destroy` method to dispatch event:

```php
public function destroy(DestroyTodoRequest $request, string $slug, Todo $todo): RedirectResponse
{
    $boardSlug = $todo->board->slug;
    $todoId = $todo->id;

    $todo->delete();

    TodoDeleted::dispatch($todoId, $boardSlug);

    return redirect()
        ->route('boards.show', $slug)
        ->with('message', 'Todo deleted successfully!');
}
```

Add the `reorder` method:

```php
public function reorder(ReorderTodoRequest $request, string $slug): JsonResponse
{
    $todo = Todo::findOrFail($request->validated('todo_id'));

    $todo->update([
        'status' => $request->validated('status'),
        'priority' => $request->validated('priority'),
    ]);

    TodoReordered::dispatch($todo);

    return response()->json(['message' => 'Todo reordered']);
}
```

- [ ] **Step 3: Create `app/Http/Requests/ReorderTodoRequest.php`**

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReorderTodoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'todo_id' => 'required|integer|exists:todos,id',
            'status' => 'required|string|in:todo,in_progress,done',
            'priority' => 'required|integer|min:1',
        ];
    }
}
```

- [ ] **Step 4: Add reorder broadcast tests to `tests/Feature/BroadcastTest.php`**

Add these test methods to the existing `BroadcastTest` class:

```php
public function test_reorder_dispatches_event()
{
    $user = User::factory()->create();
    $board = Board::factory()->create(['owner_id' => $user->id]);
    $board->collaborators()->attach($user->id);
    $todo = Todo::factory()->create(['board_id' => $board->id, 'status' => 'todo', 'priority' => 1]);

    $response = $this->actingAs($user)
        ->patchJson(route('todos.reorder', $board->slug), [
            'todo_id' => $todo->id,
            'status' => 'done',
            'priority' => 3,
        ]);

    $response->assertOk();
    $this->assertEquals('done', $todo->fresh()->status);
    $this->assertEquals(3, $todo->fresh()->priority);
}

public function test_reorder_validates_required_fields()
{
    $user = User::factory()->create();
    $board = Board::factory()->create(['owner_id' => $user->id]);
    $board->collaborators()->attach($user->id);

    $response = $this->actingAs($user)
        ->patchJson(route('todos.reorder', $board->slug), []);

    $response->assertUnprocessable();
}

public function test_user_cannot_reorder_todo_on_board_they_have_no_access_to()
{
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $board = Board::factory()->create(['owner_id' => $owner->id]);
    $board->collaborators()->attach($owner->id);
    $todo = Todo::factory()->create(['board_id' => $board->id]);

    $response = $this->actingAs($stranger)
        ->patchJson(route('todos.reorder', $board->slug), [
            'todo_id' => $todo->id,
            'status' => 'done',
            'priority' => 1,
        ]);

    $response->assertForbidden();
}
```

- [ ] **Step 5: Run tests**

```bash
composer run test
```

Expected: All tests pass including 3 new reorder tests.

- [ ] **Step 6: Regenerate Ziggy routes**

```bash
php artisan ziggy:generate
```

- [ ] **Step 7: Commit**

```bash
git add app/Http/Controllers/TodoController.php app/Http/Requests/ReorderTodoRequest.php routes/web.php tests/Feature/BroadcastTest.php resources/js/ziggy.js
git commit -m "feat: add todo reorder endpoint and dispatch broadcast events"
```

---

### Task 4: Channel authorization

**Files:**
- Create: `routes/channels.php`
- Modify: `app/Models/Board.php`

**Interfaces:**
- Consumes: `App\Models\User`, `App\Models\Board`, `BoardPolicy`
- Produces: Channel authorization for `board.{slug}` and `presence-board.{slug}`

- [ ] **Step 1: Add `Broadcasts` trait to `app/Models/Board.php`**

Add use statement:

```php
use Illuminate\Database\Eloquent\Broadcasts;
```

Add trait to class:

```php
class Board extends Model
{
    use HasFactory, Broadcasts;
```

- [ ] **Step 2: Create `routes/channels.php`**

```php
<?php

use App\Models\Board;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Broadcast::channel('board.{slug}', function ($user, string $slug) {
        $board = Board::where('slug', $slug)->first();

        if (! $board) {
            return false;
        }

        if ($user->can('view', $board)) {
            return ['id' => $user->id, 'name' => $user->name];
        }

        return false;
    });

    Broadcast::channel('presence-board.{slug}', function ($user, string $slug) {
        $board = Board::where('slug', $slug)->first();

        if (! $board) {
            return false;
        }

        if ($user->can('view', $board)) {
            return ['id' => $user->id, 'name' => $user->name];
        }

        return false;
    });
});
```

- [ ] **Step 3: Run tests**

```bash
composer run test
```

Expected: All tests pass (channels.php is loaded but not exercised by existing tests yet).

- [ ] **Step 4: Commit**

```bash
git add routes/channels.php app/Models/Board.php
git commit -m "feat: add channel authorization for board broadcast channels"
```

---

### Task 5: Frontend — Echo initialization

**Files:**
- Create: `resources/js/echo.js`
- Modify: `resources/js/app.js`

**Interfaces:**
- Consumes: `VITE_PUSHER_APP_KEY`, `VITE_PUSHER_APP_CLUSTER` env vars from Task 1
- Produces: `window.Echo` available globally for Vue components

- [ ] **Step 1: Create `resources/js/echo.js`**

```js
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

window.Pusher = Pusher

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
})
```

- [ ] **Step 2: Import echo.js in `resources/js/app.js`**

Add import after `import './bootstrap';`:

```js
import './echo';
```

- [ ] **Step 3: Build to verify no errors**

```bash
npm run build
```

Expected: Build succeeds with no errors.

- [ ] **Step 4: Commit**

```bash
git add resources/js/echo.js resources/js/app.js
git commit -m "feat: initialize Laravel Echo with Pusher"
```

---

### Task 6: Frontend — Echo listeners and online indicators in Show.vue

**Files:**
- Modify: `resources/js/Pages/Boards/Show.vue`

**Interfaces:**
- Consumes: `window.Echo` from Task 5, broadcast events from Task 2
- Produces: Real-time todo updates, online user indicators

- [ ] **Step 1: Update `Boards/Show.vue` — add imports and state**

Replace the `<script setup>` imports section (add Echo import and onlineUsers ref):

```js
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import draggable from 'vuedraggable';
import KanbanCard from '@/Components/KanbanCard.vue';
import ShareModal from '@/Components/ShareModal.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { toast } from 'vue-sonner';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';
```

Add `onlineUsers` ref after the existing refs:

```js
const onlineUsers = ref([]);
```

- [ ] **Step 2: Add Echo subscription in `onMounted`**

Add after the existing `const page = usePage();` watch block:

```js
let echoChannel = null;
let echoPresence = null;

onMounted(() => {
    if (window.Echo) {
        echoChannel = window.Echo.private(`board.${props.board.slug}`)
            .listen('.TodoCreated', (e) => {
                if (!todoTasks.value.find(t => t.id === e.id) &&
                    !inProgressTasks.value.find(t => t.id === e.id) &&
                    !doneTasks.value.find(t => t.id === e.id)) {
                    const column = e.status === 'todo' ? todoTasks :
                                   e.status === 'in_progress' ? inProgressTasks : doneTasks;
                    column.value.push(e);
                    toast.info(`New todo added: ${e.title}`);
                }
            })
            .listen('.TodoUpdated', (e) => {
                for (const col of [todoTasks, inProgressTasks, doneTasks]) {
                    const idx = col.value.findIndex(t => t.id === e.id);
                    if (idx !== -1) {
                        col.value[idx] = e;
                        break;
                    }
                }
            })
            .listen('.TodoDeleted', (e) => {
                todoTasks.value = todoTasks.value.filter(t => t.id !== e.id);
                inProgressTasks.value = inProgressTasks.value.filter(t => t.id !== e.id);
                doneTasks.value = doneTasks.value.filter(t => t.id !== e.id);
                toast.info('A todo was deleted');
            })
            .listen('.TodoReordered', (e) => {
                for (const col of [todoTasks, inProgressTasks, doneTasks]) {
                    const idx = col.value.findIndex(t => t.id === e.id);
                    if (idx !== -1) {
                        col.value.splice(idx, 1);
                        break;
                    }
                }
                const targetCol = e.status === 'todo' ? todoTasks :
                                  e.status === 'in_progress' ? inProgressTasks : doneTasks;
                targetCol.value.push({ ...targetCol.value.find(t => t.id === e.id) || e, status: e.status, priority: e.priority });
            });

        echoPresence = window.Echo.join(`presence-board.${props.board.slug}`)
            .here((users) => {
                onlineUsers.value = users;
            })
            .joining((user) => {
                if (!onlineUsers.value.find(u => u.id === user.id)) {
                    onlineUsers.value.push(user);
                    toast.info(`${user.name} joined the board`);
                }
            })
            .leaving((user) => {
                onlineUsers.value = onlineUsers.value.filter(u => u.id !== user.id);
                toast.info(`${user.name} left the board`);
            });
    }
});

onUnmounted(() => {
    if (echoChannel) {
        window.Echo.leave(`board.${props.board.slug}`);
    }
    if (echoPresence) {
        window.Echo.leave(`presence-board.${props.board.slug}`);
    }
});
```

- [ ] **Step 3: Add online indicators to template**

Add after the stats bar and before the kanban board div:

```html
<!-- Online Collaborators -->
<div v-if="onlineUsers.length > 0" class="flex items-center gap-2 mb-4">
    <span class="text-xs font-medium" style="color:var(--color-text-muted);">Online:</span>
    <div class="flex -space-x-2">
        <div v-for="user in onlineUsers" :key="user.id"
            class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold border-2 border-white relative"
            :style="{ background: 'var(--color-accent)', color: 'var(--color-accent-text)' }"
            :title="user.name">
            {{ user.name.charAt(0).toUpperCase() }}
            <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-green-500 rounded-full border-2 border-white"></span>
        </div>
    </div>
    <span class="text-xs" style="color:var(--color-text-muted);">{{ onlineUsers.length }} online</span>
</div>
```

- [ ] **Step 4: Update `updateTodoStatus` to use reorder endpoint for drag-drop**

Replace the existing `updateTodoStatus` function:

```js
const updateTodoStatus = (todoId, newStatus, priority) => {
    router.patch(route('todos.reorder', props.board.slug), {
        todo_id: todoId,
        status: newStatus,
        priority: priority,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};
```

Update `onDragChange` to calculate priority:

```js
const onDragChange = (evt) => {
    if (evt.added) {
        const todo = evt.added.element;
        const newStatus = getStatusFromColumn(todo);
        const columnIndex = getColumnArray(newStatus).value.indexOf(todo);
        const priority = getColumnArray(newStatus).value.length - columnIndex;

        updateTodoStatus(todo.id, newStatus, priority);
    }
};

const getColumnArray = (status) => {
    if (status === 'todo') return todoTasks;
    if (status === 'in_progress') return inProgressTasks;
    return doneTasks;
};
```

- [ ] **Step 5: Build to verify no errors**

```bash
npm run build
```

Expected: Build succeeds.

- [ ] **Step 6: Commit**

```bash
git add resources/js/Pages/Boards/Show.vue
git commit -m "feat: add real-time Echo listeners and online indicators to board view"
```

---

### Task 7: Final verification and cleanup

**Files:**
- All files from previous tasks

**Interfaces:**
- Consumes: all previous tasks
- Produces: passing tests, clean code, committed changes

- [ ] **Step 1: Run all tests**

```bash
composer run test
```

Expected: All tests pass (existing + new broadcast tests).

- [ ] **Step 2: Run Pint**

```bash
vendor/bin/pint --dirty
```

Expected: No issues (or auto-fixed).

- [ ] **Step 3: Build frontend**

```bash
npm run build
```

Expected: Build succeeds.

- [ ] **Step 4: Regenerate Ziggy routes**

```bash
php artisan ziggy:generate
```

- [ ] **Step 5: Final commit if Pint made changes**

```bash
git add -N . && git diff --name-only
```

If dirty files exist:

```bash
git add -p .
git commit -m "style: apply pint formatting"
```

---

### Task 8: Pusher setup instructions (documentation)

**Files:**
- Create: `docs/pusher-setup.md`

**Interfaces:**
- Consumes: all previous tasks
- Produces: setup guide for creating a free Pusher account

- [ ] **Step 1: Create `docs/pusher-setup.md`**

```markdown
# Pusher Setup Guide

## Create Free Account

1. Go to https://pusher.com and sign up for free
2. Create a new app in the dashboard
3. Go to "App Keys" tab

## Environment Variables

Add these to your `.env` file:

```
BROADCAST_CONNECTION=pusher
PUSHER_APP_ID=your-app-id-from-dashboard
PUSHER_APP_KEY=your-key-from-dashboard
PUSHER_APP_SECRET=your-secret-from-dashboard
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

## Free Tier Limits

- 100,000 messages/day
- 100 simultaneous connections
- Plenty for a kanban app with a few collaborators

## Testing Locally

After setting up the env vars, run `npm run dev` and open two browser windows
logged in as different users viewing the same board. Changes made in one window
should appear instantly in the other.
```

- [ ] **Step 2: Commit**

```bash
git add docs/pusher-setup.md
git commit -m "docs: add Pusher setup guide"
```
