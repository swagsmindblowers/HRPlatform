<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PolicyTemplate extends Model
{
    use HasFactory;

    protected $table = 'policy_templates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'jurisdiction',
        'category',
        'title',
        'body',
        'is_system_default',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_system_default' => 'boolean',
    ];

    /**
     * Render the template body with the company's name interpolated in
     * place of the {{company_name}} placeholder.
     *
     * @param Company $company
     * @return string
     */
    public function renderFor(Company $company): string
    {
        return str_replace('{{company_name}}', $company->name, $this->body);
    }
}
