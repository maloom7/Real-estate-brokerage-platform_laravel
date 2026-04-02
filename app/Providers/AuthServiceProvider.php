<?php

namespace App\Providers;

use App\Models\Property;
use App\Models\PropertyDocument;
use App\Models\Client;
use App\Models\Deal;
use App\Policies\PropertyPolicy;
use App\Policies\PropertyDocumentPolicy;
use App\Policies\ClientPolicy;
use App\Policies\DealPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Property::class => PropertyPolicy::class,
        PropertyDocument::class => PropertyDocumentPolicy::class,
        Client::class => ClientPolicy::class,
        Deal::class => DealPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}