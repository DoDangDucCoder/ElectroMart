<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Infrastructure\Persistence\Eloquent\Cms\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    private const STATUSES = ['new', 'read', 'replied', 'archived'];

    public function index(Request $request): View
    {
        $perPage = max(1, min((int) $request->integer('per_page', 15), 50));
        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', 'all');
        $dateFrom = $request->date('date_from');
        $dateTo = $request->date('date_to');

        $query = ContactMessage::query()->orderByDesc('created_at')->orderByDesc('id');

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('subject', 'like', '%' . $search . '%')
                    ->orWhere('message', 'like', '%' . $search . '%');
            });
        }

        if (in_array($status, self::STATUSES, true)) {
            $query->where('status', $status);
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom->toDateString());
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo->toDateString());
        }

        return view('admin.contact_messages.index', [
            'messages' => $query->paginate($perPage)->withQueryString(),
            'statuses' => self::STATUSES,
            'stats' => [
                'total' => ContactMessage::count(),
                'new' => ContactMessage::where('status', 'new')->count(),
                'read' => ContactMessage::where('status', 'read')->count(),
                'replied' => ContactMessage::where('status', 'replied')->count(),
                'archived' => ContactMessage::where('status', 'archived')->count(),
            ],
            'filters' => [
                'q' => $search,
                'status' => $status,
                'date_from' => $dateFrom?->toDateString(),
                'date_to' => $dateTo?->toDateString(),
                'per_page' => $perPage,
            ],
        ]);
    }

    public function updateStatus(Request $request, ContactMessage $contactMessage): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', self::STATUSES)],
        ]);

        $contactMessage->update([
            'status' => $data['status'],
        ]);

        return redirect()
            ->route('admin.contact-messages.index', $request->query())
            ->with('message', 'Cập nhật trạng thái liên hệ thành công.');
    }
}
