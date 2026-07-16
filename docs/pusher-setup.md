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
