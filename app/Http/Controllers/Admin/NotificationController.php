<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationSetting;
use App\Models\NotificationRead;
use App\Models\Tenants;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    // Halaman utama notifications (sama seperti index7)
    public function index()
    {
        $settings = NotificationSetting::getCurrentSettings();
        $tenants = Tenants::select('id', 'name', 'email', 'status')->get();

        return view('super-admin.index7', compact('settings', 'tenants'));
    }

    // Get notifications untuk dropdown navbar (AJAX)
    public function getNotifications(Request $request)
    {
        $user = Auth::user();
        $tenantId = null;

        // Jika user bukan admin, ambil tenant_id dari relasi atau session
        if (!$user->isAdmin()) {
            // Logika untuk mendapatkan tenant_id untuk user non-admin
            // Sesuaikan dengan struktur aplikasi kamu
            $tenantId = $request->get('tenant_id');
        }

        $settings = NotificationSetting::getCurrentSettings();
        $limit = $settings->dashboard_display_count;

        $notifications = Notification::getForUser($user->id, $tenantId, $limit);
        $unreadCount = Notification::getUnreadCountForUser($user->id, $tenantId);

        $notificationData = $notifications->map(function ($notification) use ($user, $tenantId) {
            return [
                'id' => $notification->id,
                'title' => $notification->title,
                'message' => strlen($notification->message) > 60
                    ? substr($notification->message, 0, 60) . '...'
                    : $notification->message,
                'priority' => $notification->priority,
                'priority_badge' => $notification->priority_badge,
                'created_at' => $notification->created_at->diffForHumans(),
                'is_read' => $notification->isReadBy($user->id, $tenantId)
            ];
        });

        return response()->json([
            'notifications' => $notificationData,
            'unread_count' => $unreadCount,
            'total_count' => $notifications->count()
        ]);
    }

    // Get all notifications untuk halaman All Notifications (AJAX)
    public function getAllNotifications(Request $request)
    {
        $query = Notification::with('creator')
            ->active()
            ->orderBy('created_at', 'desc');

        // Filter berdasarkan search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan priority
        if ($request->has('priority') && $request->priority) {
            $query->byPriority($request->priority);
        }

        // Filter berdasarkan target type
        if ($request->has('target_type') && $request->target_type) {
            $query->byTargetType($request->target_type);
        }

        // Pagination
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);

        $notifications = $query->paginate($perPage, ['*'], 'page', $page);

        $data = $notifications->getCollection()->map(function ($notification) {
            return [
                'id' => $notification->id,
                'title' => $notification->title,
                'message' => strlen($notification->message) > 100
                    ? substr($notification->message, 0, 100) . '...'
                    : $notification->message,
                'priority' => $notification->priority,
                'priority_badge' => $notification->priority_badge,
                'target_audience' => $notification->target_audience,
                'delivery_methods' => $notification->delivery_methods_string,
                'created_at' => $notification->created_at->format('d M Y, H:i'),
                'created_by' => $notification->creator->name
            ];
        });

        return response()->json([
            'data' => $data,
            'pagination' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
                'from' => $notifications->firstItem(),
                'to' => $notifications->lastItem()
            ]
        ]);
    }

    // Get archived notifications (AJAX)
    public function getArchivedNotifications(Request $request)
    {
        $query = Notification::with('creator')
            ->archived()
            ->orderBy('updated_at', 'desc'); // Urutkan berdasarkan kapan diarsip

        // Filter berdasarkan search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan target type
        if ($request->has('target') && $request->target) {
            if ($request->target === 'all') {
                $query->where('target_type', 'all');
            } elseif ($request->target === 'specific') {
                $query->where('target_type', 'specific');
            }
        }

        // Filter berdasarkan priority (opsional untuk archived)
        if ($request->has('priority') && $request->priority) {
            $query->where('priority', $request->priority);
        }

        // Pagination
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);

        $notifications = $query->paginate($perPage, ['*'], 'page', $page);

        $data = $notifications->getCollection()->map(function ($notification) {
            return [
                'id' => $notification->id,
                'title' => $notification->title,
                'message' => strlen($notification->message) > 100
                    ? substr($notification->message, 0, 100) . '...'
                    : $notification->message,
                'priority' => $notification->priority,
                'priority_badge' => $notification->priority_badge,
                'target_audience' => $notification->target_audience,
                'delivery_methods' => $notification->delivery_methods_string,
                'archived_at' => $notification->updated_at->format('d M Y, H:i'),
                'created_by' => $notification->creator->name
            ];
        });

        return response()->json([
            'data' => $data,
            'pagination' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
                'from' => $notifications->firstItem(),
                'to' => $notifications->lastItem()
            ]
        ]);
    }

    // Store notification baru
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'required|in:Normal,Important,Critical',
            'target_type' => 'required|in:all,specific',
            'target_tenant_ids' => 'required_if:target_type,specific|array',
            'target_tenant_ids.*' => 'exists:tenants,id',
            'delivery_methods' => 'required|array|min:1',
            'delivery_methods.*' => 'in:Dashboard,Email,Push Notifications'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $notification = Notification::create([
            'title' => $request->title,
            'message' => $request->message,
            'priority' => $request->priority,
            'delivery_methods' => $request->delivery_methods,
            'target_type' => $request->target_type,
            'target_tenant_ids' => $request->target_type === 'specific' ? $request->target_tenant_ids : null,
            'created_by' => Auth::id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification created successfully',
            'data' => $notification
        ]);
    }

    // Archive notification
    public function archive($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['is_archived' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Notification archived successfully'
        ]);
    }

    // Restore notification dari archive
    public function restore($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['is_archived' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Notification restored successfully'
        ]);
    }

    // Delete notification permanent
    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);

        // Hapus semua read records
        $notification->reads()->delete();

        // Hapus notification
        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted permanently'
        ]);
    }

    // Mark notification sebagai read
    public function markAsRead(Request $request, $id)
    {
        $notification = Notification::findOrFail($id);
        $user = Auth::user();
        $tenantId = $request->get('tenant_id');

        $notification->markAsReadBy($user->id, $tenantId);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }

    // Get tenants untuk dropdown
    public function getTenants(Request $request)
    {
        $query = Tenants::select('id', 'name', 'email', 'status');

        // Filter berdasarkan search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $tenants = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $tenants
        ]);
    }

    // Get current settings
    public function getSettings()
    {
        $settings = NotificationSetting::getCurrentSettings();

        return response()->json([
            'success' => true,
            'data' => $settings->getDefaultsForForm()
        ]);
    }

    // Update settings - PERBAIKAN
    public function updateSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'default_priority' => 'required|in:Normal,Important,Critical',
            'default_delivery_methods' => 'required|array|min:1',
            'default_delivery_methods.*' => 'in:Dashboard,Push Notifications',
            'push_enabled' => 'boolean',
            'dashboard_display_count' => 'required|integer|min:1|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $settings = NotificationSetting::updateSettings([
            'default_priority' => $request->default_priority,
            'default_delivery_methods' => $request->default_delivery_methods,
            'push_enabled' => $request->push_enabled ?? false,
            'dashboard_display_count' => $request->dashboard_display_count
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully',
            'data' => $settings->getDefaultsForForm()
        ]);
    }

    // Bulk archive notifications
    public function bulkArchive(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notification_ids' => 'required|array|min:1',
            'notification_ids.*' => 'exists:notifications,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        Notification::whereIn('id', $request->notification_ids)
            ->update(['is_archived' => true]);

        return response()->json([
            'success' => true,
            'message' => count($request->notification_ids) . ' notifications archived successfully'
        ]);
    }

    // Bulk delete notifications (dari archive)
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notification_ids' => 'required|array|min:1',
            'notification_ids.*' => 'exists:notifications,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Hapus read records dulu
        NotificationRead::whereIn('notification_id', $request->notification_ids)->delete();

        // Hapus notifications
        Notification::whereIn('id', $request->notification_ids)->delete();

        return response()->json([
            'success' => true,
            'message' => count($request->notification_ids) . ' notifications deleted permanently'
        ]);
    }
}
