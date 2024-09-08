<?php

namespace iProtek\SmsSender\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; 

class _CommonModel extends Model
{
    use HasFactory; 
    /**
     * Override the asDateTime method to prevent timezone conversion.
     *
     * @param  mixed  $value
     * @return \Carbon\Carbon
     */
    protected function asDateTime($value)
    {
        //return $value;
        $timezone = config('app.timezone') ?: 'UTC';
        return parent::asDateTime($value)->setTimezone($timezone);
    }
}
