<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $table = 'conversations';

    protected $fillable = [
        'annonce_id',
        'initiateur_id',
        'destinataire_id',
        'dernier_message_le',
    ];

    protected function casts(): array
    {
        return [
            'dernier_message_le' => 'datetime',
        ];
    }

    public function annonce()
    {
        return $this->belongsTo(Annonce::class, 'annonce_id');
    }

    public function initiateur()
    {
        return $this->belongsTo(Utilisateur::class, 'initiateur_id');
    }

    public function destinataire()
    {
        return $this->belongsTo(Utilisateur::class, 'destinataire_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'conversation_id')
            ->orderBy('created_at');
    }

    public function dernierMessage()
    {
        return $this->hasOne(Message::class, 'conversation_id')->latestOfMany();
    }
}
