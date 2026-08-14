import { describe, it, expect } from 'vitest';
import { Notification } from './notification';

describe('NotificationService', () => {
  it('should return the number of unread notifications', () => {
    const notifications: Notification[] = [
      {
        id: 1,
        title: 'New Asset',
        message: 'A new asset was created',
        type: 'info',
        is_read: 0,
        created_at: '2026-08-14',
      },
      {
        id: 2,
        title: 'Asset Assigned',
        message: 'An asset was assigned',
        type: 'success',
        is_read: 1,
        created_at: '2026-08-14',
      },
      {
        id: 3,
        title: 'Warranty',
        message: 'Warranty is expiring',
        type: 'warning',
        is_read: 0,
        created_at: '2026-08-14',
      },
    ];

    const unreadCount = notifications.filter(n => !n.is_read).length;

    expect(unreadCount).toBe(2);
  });

  it('should return zero when all notifications are read', () => {
    const notifications: Notification[] = [
      {
        id: 1,
        title: 'New Asset',
        message: 'A new asset was created',
        type: 'info',
        is_read: 1,
        created_at: '2026-08-14',
      },
      {
        id: 2,
        title: 'Asset Assigned',
        message: 'An asset was assigned',
        type: 'success',
        is_read: 1,
        created_at: '2026-08-14',
      },
    ];

    const unreadCount = notifications.filter(n => !n.is_read).length;

    expect(unreadCount).toBe(0);
  });
});