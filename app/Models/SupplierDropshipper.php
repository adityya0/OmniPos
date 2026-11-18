<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierDropshipper extends Model
{
    use HasFactory;

    protected $table = 'supplier_dropshipper';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'supplier_id',
        'dropshipper_id',
        'wallet_balance',
        'status',
        'disconnect_requested_at',
        'disconnect_requested_by',
        'disconnect_reason',
        'disconnect_rejection_reason',
        'disconnect_approved_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'wallet_balance' => 'decimal:2',
            'disconnect_requested_at' => 'datetime',
            'disconnect_approved_at' => 'datetime',
        ];
    }

    /**
     * Supplier toko tujuan.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    /**
     * Akun dropshipper.
     */
    public function dropshipper(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dropshipper_id');
    }

    /**
     * Hitung hari kerja (Senin - Jumat) antara dua tanggal.
     */
    public static function calculateBusinessDaysBetween(CarbonInterface $start, CarbonInterface $end): int
    {
        $start = $start->copy()->startOfDay();
        $end = $end->copy()->startOfDay();

        if ($start->gte($end)) {
            return 0;
        }

        $businessDays = 0;
        $cursor = $start->copy()->addDay();
        while ($cursor->lte($end)) {
            if (!$cursor->isWeekend()) {
                $businessDays++;
            }
            $cursor->addDay();
        }

        return $businessDays;
    }

    /**
     * Tentukan tanggal target ketika syarat N hari kerja terpenuhi.
     */
    public static function getTargetEligibleDate(CarbonInterface $start, int $requiredBusinessDays = 5): Carbon
    {
        $cursor = $start->copy()->startOfDay();
        $added = 0;
        while ($added < $requiredBusinessDays) {
            $cursor->addDay();
            if (!$cursor->isWeekend()) {
                $added++;
            }
        }
        return $cursor;
    }

    /**
     * Periksa kepatuhan 3 syarat pemutusan kemitraan:
     * 1. Bebas transaksi berjalan (semua pesanan harus selesai atau dibatalkan)
     * 2. Minimal 5 hari kerja tidak memesan
     * 3. Menunggu persetujuan kedua belah pihak
     */
    public static function getDisconnectEligibility(int $dropshipperId, int $supplierId): array
    {
        // 1. Cek Transaksi yang Masih Aktif / Berjalan
        $activeOrders = Order::where('user_id', $dropshipperId)
            ->where('supplier_id', $supplierId)
            ->where(function ($q) {
                $q->whereNotIn('status', ['selesai', 'dibatalkan'])
                  ->orWhere('payment_status', 'pending');
            })
            ->get();

        $activeOrdersCount = $activeOrders->count();
        $isOrdersCleared = ($activeOrdersCount === 0);

        // 2. Cek Riwayat Pemesanan Terakhir & Syarat 5 Hari Kerja
        $lastOrder = Order::where('user_id', $dropshipperId)
            ->where('supplier_id', $supplierId)
            ->latest('created_at')
            ->first();

        $minBusinessDays = 5;
        $businessDaysPassed = $lastOrder
            ? self::calculateBusinessDaysBetween($lastOrder->created_at, now())
            : 999;

        $isDaysThresholdMet = ($lastOrder === null || $businessDaysPassed >= $minBusinessDays);

        $targetEligibleDate = null;
        if ($lastOrder && !$isDaysThresholdMet) {
            $targetEligibleDate = self::getTargetEligibleDate($lastOrder->created_at, $minBusinessDays);
        }

        // 3. Relasi Kemitraan
        $relation = self::where('dropshipper_id', $dropshipperId)
            ->where('supplier_id', $supplierId)
            ->first();

        $status = $relation?->status ?? 'none';
        $isPendingApproval = ($status === 'pending_disconnect');

        // Sisa kewajiban saldo
        $walletBalance = (float)($relation?->wallet_balance ?? 0);
        $isWalletCleared = ($walletBalance >= 0);

        // Syarat untuk bisa mengajukan permohonan lepas
        $canRequest = $isOrdersCleared && $isDaysThresholdMet && $isWalletCleared && ($status === 'approved');

        $errors = [];
        if (!$isOrdersCleared) {
            $errors[] = "Masih terdapat {$activeOrdersCount} pesanan aktif yang belum selesai/dibatalkan.";
        }
        if (!$isDaysThresholdMet) {
            $daysRemaining = max(0, $minBusinessDays - $businessDaysPassed);
            $targetStr = $targetEligibleDate ? $targetEligibleDate->translatedFormat('d M Y') : '-';
            $orderDateStr = $lastOrder ? $lastOrder->created_at->translatedFormat('d M Y') : '-';
            $errors[] = "Minimal 5 hari kerja tanpa pemesanan. Terakhir memesan pada {$orderDateStr} (baru {$businessDaysPassed} hari kerja, kurang {$daysRemaining} hari kerja hingga {$targetStr}).";
        }
        if (!$isWalletCleared) {
            $errors[] = "Masih terdapat sisa kewajiban saldo negatif sebesar Rp " . number_format(abs($walletBalance), 0, ',', '.') . ".";
        }
        if ($isPendingApproval) {
            $errors[] = "Permohonan pelepasan kemitraan telah diajukan dan sedang menunggu persetujuan.";
        }

        return [
            'can_request' => $canRequest,
            'is_orders_cleared' => $isOrdersCleared,
            'active_orders_count' => $activeOrdersCount,
            'last_order' => $lastOrder,
            'business_days_passed' => $businessDaysPassed,
            'min_business_days' => $minBusinessDays,
            'is_days_threshold_met' => $isDaysThresholdMet,
            'target_eligible_date' => $targetEligibleDate,
            'wallet_balance' => $walletBalance,
            'is_wallet_cleared' => $isWalletCleared,
            'status' => $status,
            'is_pending_approval' => $isPendingApproval,
            'relation' => $relation,
            'errors' => $errors,
        ];
    }
}
