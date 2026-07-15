# Real-Time Collaborative Kanban — Design Spec

## Overview

Add real-time updates to the kanban board so collaborators see changes instantly without refreshing. Uses **Pusher free tier** as the websocket host and **Laravel Echo** on the frontend to listen for broadcast events.

**Scope:** Todo CRUD events, drag-drop reorder, and presence indicators (who's online). No collaborator join/leave events for now.

## Architecture

```
Controller action
  → dispatch broadcast event (ShouldBroadcast)
  → Pusher routes to connected clients
  → Laravel Echo receives on Vue frontend
  → Vue reactivity updates DOM
```

- **Channel type:** Private channels (`board.{slug}`) for events, presence channels (`presence-board.{slug}`) for online indicators
- **Authorization:** Channel auth checks `BoardPolicy@view` — same middleware logic as HTTP routes
- **No websocket server to run** — Pusher is hosted, works on Render free tier

## Backend

### Packages to install

- `composer require pusher/pusher-php-server`

### Environment variables (`.env.example`)

```
BROADCAST_CONNECTION=pusher
PUSHER_APP_ID=your-app-id
PUSHER_APP_KEY=your-app-key
PUSHER_APP_SECRET=your-app-secret
PUSHER_APP_CLUSTER=mt1
VITE_PUSHER_APP_KEY=${PUSHER_APP_KEY}
VITE_PUSHER_APP_CLUSTER=${PUSHER_APP_CLUSTER}
```

### Broadcast events (`app/Events/`)

All events implement `ShouldBroadcast` and broadcast on private/presence channels.

| Event | Channel | Payload | Dispatched from |
|-------|---------|---------|-----------------|
| `TodoCreated` | `board.{slug}` | Full todo object | `TodoController@store` |
| `TodoUpdated` | `board.{slug}` | Full todo object | `TodoController@update` |
| `TodoDeleted` | `board.{slug}` | `{ id, board_slug }` | `TodoController@destroy` |
| `TodoReordered` | `board.{slug}` | `{ id, status, position, board_slug }` | `TodoController@reorder` |

Each event's `broadcastOn()` returns `new PrivateChannel('board.' . $this->todo->board->slug)`.

### Reorder endpoint

New route: `PATCH /boards/{slug}/todos/reorder`

Accepts: `{ todo_id: int, status: string, position: int }`

Updates the todo's `status` and `priority` (position maps to priority), then broadcasts `TodoReordered` to all other collaborators on the board. Uses `broadcast()->others()` to skip the sender.

### Channel authorization (`routes/channels.php`)

```php
Broadcast::channel('board.{slug}', function (User $user, string $slug) {
    $board = Board::where('slug', $slug)->first();
    if ($board && $user->allBoards->contains('id', $board->id)) {
        return ['id' => $user->id, 'name' => $user->name];
    }
    return false;
});

Broadcast::channel('presence-board.{slug}', function (User $user, string $slug) {
    // Same logic — returns user info for presence
});
```

### Model: `Board` — add `broadcasts()` trait

The `Board` model gets the `Broadcasts` trait so events can be dispatched via `$board->broadcast(new TodoCreated($todo))`.

## Frontend

### Packages to install

- `npm install laravel-echo pusher-js`

### Echo initialization (`resources/js/echo.js`)

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

Import in `resources/js/app.js`.

### Board subscription (`Boards/Show.vue`)

On mount, subscribe to the board's private channel and presence channel:

```js
const channel = Echo.private(`board.${board.slug}`)

channel.listen('TodoCreated', (e) => {
    // Add todo to local state if not already present (optimistic check)
    if (!todos.value.find(t => t.id === e.id)) {
        todos.value.push(e)
    }
})

channel.listen('TodoUpdated', (e) => {
    const idx = todos.value.findIndex(t => t.id === e.id)
    if (idx !== -1) todos.value[idx] = e
})

channel.listen('TodoDeleted', (e) => {
    todos.value = todos.value.filter(t => t.id !== e.id)
})

channel.listen('TodoReordered', (e) => {
    const idx = todos.value.findIndex(t => t.id === e.id)
    if (idx !== -1) {
        todos.value[idx].status = e.status
        todos.value[idx].position = e.position
    }
})

// Presence channel for online indicators
const presence = Echo.join(`presence-board.${board.slug}`)
presence.here((users) => { onlineUsers.value = users })
presence.joining((user) => { onlineUsers.value.push(user) })
presence.leaving((user) => {
    onlineUsers.value = onlineUsers.value.filter(u => u.id !== user.id)
})
```

On unmount, call `Echo.leave(...)` to clean up subscriptions.

### Online indicators

A small section at the top of `Boards/Show.vue` showing avatars/initials of online collaborators with a green dot. Uses the `onlineUsers` ref populated by the presence channel.

### Optimistic updates for drag-drop

When a card is dragged:
1. Move the card in local state immediately (vuedraggable handles this)
2. Send `PATCH /boards/{slug}/todos/reorder` with new status/position
3. On success, do nothing (local state is already correct)
4. On error, revert the local state and show a toast

### Conflict handling

Last-write-wins. When a `TodoUpdated` event arrives for a todo the user is currently editing, show a brief toast: "Updated by {name}". No merge logic — too complex for a kanban app.

## Testing

- **PHPUnit:** Test that events implement `ShouldBroadcast`, broadcast on correct channels, have correct payloads
- **PHPUnit:** Test that `TodoController@reorder` broadcasts `TodoReordered` to the right channel
- **PHPUnit:** Test channel authorization — collaborator can subscribe, non-collaborator cannot
- **PHPUnit:** Test that sender doesn't receive their own `TodoReordered` event (uses `broadcast()->others()`)

## Files to create/modify

### New files
- `app/Events/TodoCreated.php`
- `app/Events/TodoUpdated.php`
- `app/Events/TodoDeleted.php`
- `app/Events/TodoReordered.php`
- `resources/js/echo.js`
- `routes/channels.php`

### Modified files
- `app/Http/Controllers/TodoController.php` — add `reorder` method, dispatch events in store/update/destroy
- `app/Models/Board.php` — add `Broadcasts` trait
- `app/Http/Kernel.php` or `bootstrap/app.php` — register broadcast middleware
- `resources/js/app.js` — import echo.js
- `resources/js/Pages/Boards/Show.vue` — subscribe to channels, add online indicators, optimistic drag-drop
- `resources/js/Pages/Boards/Index.vue` — minor: no changes needed
- `routes/web.php` — add reorder route
- `.env.example` — add Pusher env vars
- `composer.json` — add pusher/pusher-php-server
- `package.json` — add laravel-echo, pusher-js
- `Dockerfile` — no changes needed (Pusher is hosted)
