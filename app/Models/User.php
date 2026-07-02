<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        'password',
        'nik',
        'phone_number',
        'address',
        'gender',
        'role',
    ];

    // Helper functions for roles
    public function isAdmin() { return $this->role === 'admin'; }
    public function isApoteker() { return $this->role === 'apoteker'; }
    public function isPetugasMedis() { return $this->role === 'petugas_medis'; }
    public function isWarga() { return $this->role === 'warga'; }

    // Compatibility aliases for legacy roles
    public function isPetugasKesehatan() { return $this->isPetugasMedis(); }
    public function isDokter() { return $this->isPetugasMedis(); }
    public function isPetugasImunisasi() { return $this->isPetugasMedis(); }
    public function isKepalaPuskesmas() { return $this->isAdmin(); }
    public function isOperator() { return $this->isPetugasMedis() || $this->isAdmin(); }

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
}
