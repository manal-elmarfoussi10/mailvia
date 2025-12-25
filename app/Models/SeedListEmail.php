<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class SeedListEmail extends Model
{
    use HasFactory;

    protected $fillable = [
        'seed_list_id', 'email', 'mailbox_type',
        'imap_host', 'imap_port', 'imap_user', 'imap_password', 'imap_encryption'
    ];

    protected $hidden = ['imap_password'];

    public function seedList(): BelongsTo
    {
        return $this->belongsTo(SeedList::class);
    }

    /**
     * Set the IMAP password, encrypted.
     */
    public function setImapPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['imap_password'] = Crypt::encryptString($value);
        }
    }

    /**
     * Get the decrypted IMAP password.
     */
    public function getDecryptedPasswordAttribute()
    {
        if ($this->imap_password) {
            return Crypt::decryptString($this->imap_password);
        }
        return null;
    }
}
