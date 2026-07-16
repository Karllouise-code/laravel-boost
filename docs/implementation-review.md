# Real-Time Collaborative Kanban Board — Implementation Review

## Overview

Transformed a single-user Laravel todo kanban app into a multi-user collaborative kanban board with real-time updates via Pusher websockets and shareable board URLs.

**Tech Stack:** Laravel 12, Inertia.js 2, Vue 3 (Composition API), Vite 7, Tailwind CSS 4, PHPUnit, Ziggy routes, vuedraggable

---

## Phase 1: Shareable Boards (Pre-existing)

Implemented before this session. Key decisions that affected Phase 2:

- Multiple boards per user, each with a unique 8-char slug for sharing
- Access model: anyone with the shareable link is a full collaborator (add/edit/delete/drag), login required
- Board model has `owner_id` and a `collaborators` pivot table
- BoardPolicy handles authorization via `view`, `update`, `delete`, `manageCollaborators`
- `board.access` middleware checks collaboration status on every board route

**Commits:** `0c40f13` through `e169709`

---

## Phase 2: Real-Time Collaboration

### Task 1 — Install Packages & Configure Pusher

**Commits:** `610d1bc..831fdde`

Installed:
- `pusher/pusher-php-server` ^7.0 (server-side Pusher HTTP API)
- `laravel-echo` (frontend WebSocket client)
- `pusher-js` (Pusher JavaScript SDK)

Registered `channels.php` in `bootstrap/app.php`:
```php
->withRouting(
    channels: __DIR__.'/../routes/channels.php',
    ...
)
```

Added Pusher env vars to `.env.example`.

**Fix applied:** Pinned `pusher/pusher-php-server` to `^7.0` instead of `*` for version stability.

---

### Task 2 — Create Broadcast Events

**Commits:** `831fdde..1831855`

Created 4 broadcast events in `app/Events/`:

| Event | Data | Purpose |
|-------|------|---------|
| `TodoCreated` | Full todo model | New todo added to board |
| `TodoUpdated` | Full todo model | Todo edited |
| `TodoDeleted` | `id`, `board_slug` | Todo removed |
| `TodoReordered` | Full todo model | Todo moved between columns |

All implement `ShouldBroadcastNow`, broadcast on `private-board.{slug}` channels.

Each event has:
- `broadcastOn()` — returns `PrivateChannel('board.'.$slug)`
- `broadcastAs()` — returns the event name (e.g., `'TodoCreated'`)
- `broadcastWith()` — returns the payload

---

### Task 3 — Reorder Endpoint & Event Dispatch

**Commits:** `1831855..526ecd8**

Added `PATCH /boards/{slug}/todos/reorder` route (defined before `{todo}` routes to avoid conflict).

Created `ReorderTodoRequest` — validates `todo_id`, `status`, `priority`.

Updated `TodoController` to dispatch events:
```php
// store() dispatches TodoCreated
// update() dispatches TodoUpdated
// destroy() dispatches TodoDeleted
// reorder() dispatches TodoReordered
```

**Fix applied:** Scoped reorder endpoint to board (`$board->todos()->findOrFail()`) to prevent cross-board todo manipulation.

---

### Task 4 — Channel Authorization

**Commits:** `526ecd8..96e07fc`

Created `routes/channels.php` with two channel types:

```php
// Private channel — for todo events
Broadcast::channel('board.{slug}', function ($user, string $slug) {
    return $user->can('view', $board) ? ['id' => $user->id, 'name' => $user->name] : false;
});

// Presence channel — for online indicators
Broadcast::channel('presence-board.{slug}', function ($user, string $slug) {
    // Same authorization logic
});
```

Both use `BoardPolicy@view` for authorization.

Added `BroadcastsEvents` trait to `Board` model.

---

### Task 5 — Frontend Echo Initialization

**Commits:** `96e07fc..b9ada7a`

Created `resources/js/echo.js`:
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

Imported in `resources/js/app.js`.

---

### Task 6 — Echo Listeners & Online Indicators

**Commits:** `b9ada7a..e00f1a9`

Updated `Boards/Show.vue` with:

**Private channel listeners** (todo events):
```js
echoChannel = window.Echo.private(`board.${props.board.slug}`)
    .listen('.TodoCreated', (e) => { ... })
    .listen('.TodoUpdated', (e) => { ... })
    .listen('.TodoDeleted', (e) => { ... })
    .listen('.TodoReordered', (e) => { ... })
```

**Presence channel** (online users):
```js
echoPresence = window.Echo.join(`presence-board.${props.board.slug}`)
    .here((users) => { ... })
    .joining((user) => { ... })
    .leaving((user) => { ... })
```

**Template additions:**
- Online collaborator avatars with green dots
- `onUnmounted` cleanup for both channels
- `updateTodoStatus` switched to `todos.reorder` endpoint

**Fix applied:** Added guard in `TodoReordered` listener to prevent same-column race condition (vuedraggable already moves the item locally, broadcast would jump it to the bottom).

---

### Task 7 — Test Verification

All tests already existed from Phase 1. 51/51 tests passing.

---

### Task 8 — Pusher Setup Instructions

Created `docs/pusher-setup.md` with Pusher account setup, env vars, free tier limits, and local testing instructions.

---

## Post-Implementation Bugs & Fixes

### Bug 1: Inertia JSON Response Error

**Symptom:** White screen with `"All Inertia requests must receive a valid Inertia response, however a plain JSON response was received."`

**Cause:** `TodoController@reorder` returned `response()->json()` but Inertia expects a redirect response.

**Fix:** Changed to `return back()->with('message', 'Todo reordered');`

---

### Bug 2: No Real-Time Updates (Root Cause)

**Symptom:** Online indicators worked live, but CRUD operations and drag-drop required manual refresh to see changes on other browsers.

**Root Cause (two-part):**

1. **Missing `BroadcastServiceProvider`** — Not auto-discovered in Laravel 12. Registered in `bootstrap/providers.php`:
   ```php
   Illuminate\Broadcasting\BroadcastServiceProvider::class,
   ```

2. **`ShouldBroadcast` queues events** — Events implementing `ShouldBroadcast` get pushed to the `jobs` table instead of being sent to Pusher immediately. The queue worker was failing with `ModelNotFoundException` because serialized Todo models couldn't be restored when the worker processed the job.

**Fix:** Changed all 4 events from `ShouldBroadcast` → `ShouldBroadcastNow` to broadcast synchronously during the HTTP request.

---

### Bug 3: Todo Data Disappeared After Drag

**Symptom:** Dragging a todo to another column made the card data disappear, showing only "invalid date."

**Cause:** `TodoReordered::broadcastWith()` only sent `id`, `status`, `priority`, `board_slug`. The Vue listener pushed this sparse object into the column array, so the `KanbanCard` component had no `title`, `description`, `due_date`, etc.

**Fix:** Changed `broadcastWith()` to return the full model:
```php
public function broadcastWith(): array
{
    return $this->todo->toArray();
}
```

---

## Pusher Configuration

| Setting | Value |
|---------|-------|
| App ID | 2176586 |
| App Key | f3fc13570166df13ed0a |
| Cluster | ap1 (Asia Pacific - Singapore) |
| BROADCAST_CONNECTION | pusher |

### Required `.env` Variables
```
BROADCAST_CONNECTION=pusher
PUSHER_APP_ID=2176586
PUSHER_APP_KEY=f3fc13570166df13ed0a
PUSHER_APP_SECRET=dfe419e336dbfa3c196c
PUSHER_APP_CLUSTER=ap1
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

---

## Key Files Modified (Phase 2)

| File | Purpose |
|------|---------|
| `bootstrap/app.php` | Registered `channels.php` route |
| `bootstrap/providers.php` | Added `BroadcastServiceProvider` |
| `app/Events/TodoCreated.php` | Broadcast event — new todo |
| `app/Events/TodoUpdated.php` | Broadcast event — todo edited |
| `app/Events/TodoDeleted.php` | Broadcast event — todo removed |
| `app/Events/TodoReordered.php` | Broadcast event — todo moved |
| `app/Http/Controllers/TodoController.php` | Dispatches events, reorder endpoint |
| `app/Http/Requests/ReorderTodoRequest.php` | Validates reorder payload |
| `app/Models/Board.php` | `BroadcastsEvents` trait |
| `routes/channels.php` | Channel authorization |
| `routes/web.php` | Reorder route |
| `resources/js/echo.js` | Laravel Echo + Pusher init |
| `resources/js/app.js` | Imports echo.js |
| `resources/js/Pages/Boards/Show.vue` | Echo listeners, online indicators |
| `tests/Feature/BroadcastTest.php` | Tests for all events + reorder |
| `docs/pusher-setup.md` | Setup instructions |
| `.env.example` | Pusher env var template |

---

## Commits (Phase 2)

| Commit | Description |
|--------|-------------|
| `610d1bc` | Install Pusher packages |
| `831fdde` | Create broadcast events |
| `1831855` | Add reorder endpoint + dispatch events |
| `526ecd8` | Channel authorization |
| `96e07fc` | Frontend Echo initialization |
| `b9ada7a` | Echo listeners + online indicators |
| `e00f1a9` | Fix same-column reorder race condition |
| `cd8491d` | Initial Show.vue Echo implementation |

---

## Lessons Learned

1. **Laravel 12 does NOT auto-discover `BroadcastServiceProvider`** — Must be explicitly registered in `bootstrap/providers.php`.

2. **`ShouldBroadcast` ≠ immediate broadcast** — It queues the event. Use `ShouldBroadcastNow` for synchronous delivery.

3. **Queued broadcast events + `SerializesModels` = fragile** — If the model changes or is deleted before the worker processes the job, it throws `ModelNotFoundException`. Fine for high-traffic apps with Redis queues, but risky with database queues.

4. **`broadcastWith()` must include all data the frontend needs** — If you return a subset, the Vue template will render empty/missing fields.

5. **Inertia expects redirects, not JSON** — Any endpoint called via `router.patch/get/delete` must return an Inertia-compatible response (redirect), not raw JSON.

6. **Presence channels work natively; private channels need server push** — If presence works but events don't arrive, the issue is server-side broadcasting, not the Pusher connection.
