<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Anggota;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use App\Models\AmountBalance;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


     protected $table = 'users'; // Menentukan tabel yang digunakan

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
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function hasRole($roles)
    {
        return $this->roles === $roles;
    }

    public function anggota()
    {
        return $this->hasOne(Anggota::class);
    }

    public function pinjaman(): HasMany
    {
        return $this->hasMany(Pinjaman::class, 'user_id');
    }

   // public function simpanan(): HasMany
    //{
     //   return $this->hasMany(Simpanan::class, 'user_id');
    //}

    public function amount(): HasMany
    {
        return $this->hasMany(AmountBalance::class);
    }
    public function rekenings()
    {
        return $this->hasMany(Rekening::class, 'user_id');
    }
}
