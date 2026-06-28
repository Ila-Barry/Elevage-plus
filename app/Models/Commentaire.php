<?php
// app/Models/Commentaire.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commentaire extends Model
{
    use HasFactory;

    protected $table = 'commentaires';

    protected $fillable = [
        'publication_id',
        'user_id',
        'parent_id',
        'contenu',
        'nbr_likes',
        'is_edited',
    ];

    protected $casts = [
        'nbr_likes' => 'integer',
        'is_edited' => 'boolean',
    ];

    public function publication()
    {
        return $this->belongsTo(Publication::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(Commentaire::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Commentaire::class, 'parent_id');
    }
}