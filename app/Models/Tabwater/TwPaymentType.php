<?php
namespace App\Models\Tabwater;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class TwPaymentType extends Model {
    use HasFactory;
    protected $fillable = ['name', 'description'];
    public function tabwaterMembers() { return $this->hasMany(TwMeters::class, 'payment_id'); }
}