<?php

namespace App\Models\Tabwater;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TabwaterPipeNode extends Model
{
    use HasFactory;

    protected $table = 'tabwater_pipe_nodes';

    protected $fillable = [
        'station_id',
        'node_code',
        'name',
        'type',
        'lat',
        'lng',
        'remark',
        'deleted'
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
    ];

    public function station()
    {
        return $this->belongsTo(TabwaterStation::class, 'station_id');
    }

    public function outgoingPipes()
    {
        return $this->hasMany(TabwaterPipeline::class, 'from_node_id');
    }

    public function incomingPipes()
    {
        return $this->hasMany(TabwaterPipeline::class, 'to_node_id');
    }
}