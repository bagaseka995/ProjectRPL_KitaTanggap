<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Kolom yang dapat diisi secara massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama_lengkap',
        'email',
        'password',
        'no_telepon',
        'peran',
        'status_akun',
        'lokasi_domisili',
        'notif_aktif',
    ];

    /**
     * Kolom yang disembunyikan dari serialisasi (JSON / array).
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Cast tipe data kolom.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'notif_aktif'       => 'boolean',
        ];
    }

    /* ─── Relasi ─────────────────────────────────────────────── */

    /**
     * Bencana yang dibuat oleh admin ini.
     */
    public function bencana(): HasMany
    {
        return $this->hasMany(Bencana::class, 'admin_id');
    }

    public function relawan(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Relawan::class);
    }

    public function fcmTokens(): HasMany
    {
        return $this->hasMany(UserFcmToken::class);
    }

    /* ─── Helper ─────────────────────────────────────────────── */

    public function isAdmin(): bool
    {
        return $this->peran === 'admin';
    }

    public function isRelawan(): bool
    {
        return $this->peran === 'relawan';
    }

    public function isDonatur(): bool
    {
        return $this->peran === 'donatur';
    }
}

