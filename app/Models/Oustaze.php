<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Oustaze extends Model
{
  protected $fillable = [
    'nom_complet',
    'specialites',
    'telephone',
    'association_id',
  ];

  public function association()
  {
    return $this->belongsTo(Association::class);
  }
}
