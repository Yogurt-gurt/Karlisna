<?php

namespace App\Models;

use App\Models\Anggota;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use App\Models\AmountBalance;
use App\Models\Rekening;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Menentukan tabel yang digunakan
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'roles',
        'anggota_id', // Tambahkan anggota_id di sini
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    /**
     * Check if the user has a specific role.
     *
     * @param string $roles
     * @return bool
     */
    public function hasRole(string $roles): bool
    {
        return $this->roles === $roles;
    }

    /**
     * Relation with Anggota (BelongsTo).
     *
     * @return BelongsTo
     */
    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class, 'anggota_id');
    }

    /**
     * Relation with Pinjaman (One-to-Many).
     *
     * @return HasMany
     */
    public function pinjaman(): HasMany
    {
        return $this->hasMany(Pinjaman::class, 'user_id');
    }

    /**
     * Relation with Simpanan (One-to-Many).
     *
     * @return HasMany
     */
   

    /**
     * Relation with AmountBalance (One-to-Many).
     *
     * @return HasMany
     */
    public function amount(): HasMany
    {
        return $this->hasMany(AmountBalance::class);
    }

    /**
     * Relation with Rekening (One-to-Many).
     *
     * @return HasMany
     */
    public function rekenings(): HasMany
    {
        return $this->hasMany(Rekening::class, 'user_id');
    }
}
