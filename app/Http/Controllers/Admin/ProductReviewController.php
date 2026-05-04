<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ProductReviewController extends Controller
{
    private const STATUSES = ['pending', 'approved', 'rejected'];

    public function index(Request $request): View
    {
        $perPage = max(1, min((int) $request->integer('per_page', 15), 50));
        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', 'all');
        $rating = (string) $request->query('rating', 'all');
        $dateFrom = $request->date('date_from');
        $dateTo = $request->date('date_to');

        $query = ProductReview::query()
            ->with(['product:id,name,slug,sku', 'customer:id,name,email,phone'])
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('title', 'like', '%' . $search . '%')
                    ->orWhere('content', 'like', '%' . $search . '%')
                    ->orWhere('author_name', 'like', '%' . $search . '%')
                    ->orWhere('author_email', 'like', '%' . $search . '%')
                    ->orWhereHas('product', function ($productQuery) use ($search): void {
                        $productQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('sku', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('customer', function ($customerQuery) use ($search): void {
                        $customerQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    });
            });
        }

        if (in_array($status, self::STATUSES, true)) {
            $query->where('status', $status);
        }

        if (in_array($rating, ['1', '2', '3', '4', '5'], true)) {
            $query->where('rating', (int) $rating);
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom->toDateString());
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo->toDateString());
        }

        return view('admin.review.index', [
            'reviews' => $query->paginate($perPage)->withQueryString(),
            'statuses' => self::STATUSES,
            'stats' => [
                'total' => ProductReview::count(),
                'pending' => ProductReview::where('status', 'pending')->count(),
                'approved' => ProductReview::where('status', 'approved')->count(),
                'rejected' => ProductReview::where('status', 'rejected')->count(),
                'average_rating' => round((float) ProductReview::where('rating', '>', 0)->avg('rating'), 1),
            ],
            'filters' => [
                'q' => $search,
                'status' => $status,
                'rating' => $rating,
                'date_from' => $dateFrom?->toDateString(),
                'date_to' => $dateTo?->toDateString(),
                'per_page' => $perPage,
            ],
        ]);
    }

    public function updateStatus(Request $request, ProductReview $review): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', self::STATUSES)],
        ]);

        $review->update([
            'status' => $data['status'],
            'is_public' => $data['status'] === 'approved',
            'approved_at' => $data['status'] === 'approved' ? ($review->approved_at ?? Carbon::now()) : null,
        ]);

        return redirect()
            ->route('admin.reviews', $request->query())
            ->with('message', 'Cập nhật trạng thái đánh giá thành công.');
    }
}
