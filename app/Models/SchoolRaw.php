<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolRaw extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'school_raw';

    // Primary key configuration
    protected $primaryKey = 'udise_code';
    public $incrementing = false;
    protected $keyType = 'string';

    // No timestamps (since your table doesn't have created_at / updated_at)
    public $timestamps = false;

    // Allow mass assignment on these columns
    protected $fillable = [
        'udise_code',
        'school_name',
        'state',
        'district',
        'block',
        'village',
        'cluster',
        'location',
        'state_mgmt',
        'national_mgmt',
        'school_category',
        'school_type',
        'school_status',
    ];
}
