<?php

namespace App\Models\Tabwater;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TabwaterPipeline extends Model
{
    use HasFactory;

    protected $table = 'tabwater_pipelines';

    protected $fillable = [
        'station_id',
        'pipe_code',
        'name',
        'from_node_id',
        'to_node_id',
        'level',
        'size_mm',
        'material',
        'length_m',
        'path_coordinates',
        'deleted'
    ];

    protected $casts = [
        'size_mm' => 'integer',
        'length_m' => 'float',
        'path_coordinates' => 'array',
    ];

    public function station()
    {
        return $this->belongsTo(TabwaterStation::class, 'station_id');
    }

    public function fromNode()
    {
        return $this->belongsTo(TabwaterPipeNode::class, 'from_node_id');
    }

    public function toNode()
    {
        return $this->belongsTo(TabwaterPipeNode::class, 'to_node_id');
    }

    // Accessor กำหนดสีเส้นตามระดับท่อ
    public function getLineColorAttribute()
    {
        return match ($this->level) {
            'main' => '#0284c7',         // สีฟ้าประปาหลัก (ท่อเมน)
            'sub_main' => '#16a34a',     // สีเขียว (ท่อประธาน)
            'distribution' => '#ca8a04', // สีส้ม/ทอง (ท่อบริการเข้าซอย)
            default => '#6b7280',
        };
    }

    // Accessor กำหนดความหนาเส้นตามขนาดท่อ
    public function getLineWeightAttribute()
    {
        return match ($this->level) {
            'main' => 6,
            'sub_main' => 4,
            'distribution' => 2,
            default => 3,
        };
    }
}