<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait HasCompany
{
    /**
     * Boot the trait.
     *
     * @return void
     */
    protected static function bootHasCompany()
    {
        static::addGlobalScope('company', function (Builder $builder) {
            if (Auth::check()) {
                $builder->where($builder->qualifyColumn('comp_id'), Auth::user()->comp_id);
            }
        });

        static::creating(function ($model) {
            if (Auth::check()) {
                $model->comp_id = Auth::user()->comp_id;
            }
        });
    }

    /**
     * Initialize the trait.
     *
     * @return void
     */
    public function initializeHasCompany()
    {
        // Add comp_id to the fillable array if it's not already there
        if (! in_array('comp_id', $this->fillable)) {
            $this->fillable[] = 'comp_id';
        }
    }

    /**
     * Exclude the company scope.
     */
    public static function withoutCompanyScope()
    {
        return static::withoutGlobalScope('company');
    }
}
