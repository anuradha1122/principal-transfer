<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferAppealDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'transfer_appeal_id',
        'document_name',
        'original_name',
        'file_path',
        'disk',
        'mime_type',
        'file_size',
        'uploaded_by',
    ];

    public function transferAppeal(): BelongsTo
    {
        return $this->belongsTo(TransferAppeal::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
