<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'dropship_store_name',
        'address',
        'password',
        'role',
        'supplier_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Supplier toko yang dimiliki oleh user (jika role admin_supplier).
     */
    public function ownedSupplier(): HasOne
    {
        return $this->hasOne(Supplier::class, 'owner_id');
    }

    /**
     * Supplier toko tempat user terafiliasi (sebagai admin_supplier atau staff toko).
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    /**
     * Toko supplier aktif (baik sebagai pemilik toko atau sebagai staf yang ditugaskan).
     */
    public function activeSupplier(): ?Supplier
    {
        return $this->ownedSupplier ?? $this->supplier;
    }

    /**
     * Relasi ke pivot supplier_dropshipper.
     */
    public function supplierDropshipperRelations(): HasMany
    {
        return $this->hasMany(SupplierDropshipper::class, 'dropshipper_id');
    }

    /**
     * Toko supplier yang diikuti oleh dropshipper.
     */
    public function dropshipSuppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class, 'supplier_dropshipper', 'dropshipper_id', 'supplier_id')
            ->withPivot([
                'id',
                'wallet_balance',
                'status',
                'disconnect_requested_at',
                'disconnect_requested_by',
                'disconnect_reason',
                'disconnect_rejection_reason',
                'disconnect_approved_at',
            ])
            ->withTimestamps();
    }

    /**
     * Helper pemeriksaan role
     */
    public function isAdminSupplier(): bool
    {
        return $this->role === 'admin_supplier';
    }

    public function isDropshipper(): bool
    {
        return $this->role === 'dropshipper';
    }

    public function isTimMedia(): bool
    {
        return $this->role === 'tim_media';
    }

    public function isTimPacking(): bool
    {
        return $this->role === 'tim_packing';
    }
}
