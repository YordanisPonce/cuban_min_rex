<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    // Si la tabla no sigue la convención de pluralización, especifica el nombre de la tabla
    protected $table = 'order_items';

    // Si deseas especificar las columnas que se pueden asignar masivamente
    protected $fillable = [
        'order_id',
        'file_id',
    ];
    
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function file(): BelongsTo { return $this->belongsTo(File::class); }
}
