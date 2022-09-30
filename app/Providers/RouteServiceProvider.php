<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        $this->modelBindings();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapFrontRoutes();

        $this->mapCmsRoutes();

        $this->mapClientRoutes();

        $this->mapAgentRoutes();

        $this->mapStoreRoutes();
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::middleware('api')
            ->domain(config('app.domain.api'))
            ->name('api.')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }

    /**
     * Define the "front" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapFrontRoutes()
    {
        Route::middleware('web')
            ->domain(config('app.domain.front'))
            ->namespace($this->namespace)
            ->group(base_path('routes/front.php'));
    }

    /**
     * Define the "cms" routes for the application.     
     *
     * @return void
     */
    protected function mapCmsRoutes()
    {
        Route::middleware(['web', 'menu:cms'])
            ->domain(config('app.domain.cms'))
            ->namespace($this->namespace)
            ->group(base_path('routes/cms.php'));
    }

    /**
     * Define the "client" routes for the application.     
     *
     * @return void
     */
    protected function mapClientRoutes()
    {
        Route::middleware(['web', 'menu:client', 'locale:client', 'auth:client'])
            ->name('client.')
            ->domain(config('app.domain.client'))
            ->namespace($this->namespace)
            ->group(base_path('routes/client.php'));
    }

    /**
     * Define the "agent" routes for the application.     
     *
     * @return void
     */
    protected function mapAgentRoutes()
    {
        Route::middleware(['web', 'menu:agent', 'locale:client', 'auth:agent'])
            ->name('agent.')
            ->domain(config('app.domain.agent'))
            ->namespace($this->namespace)
            ->group(base_path('routes/agent.php'));
    }

    /**
     * Define the "store" routes for the application.     
     *
     * @return void
     */
    protected function mapStoreRoutes()
    {
        Route::middleware(['web', 'locale:client'])
            ->name('store.')
            ->domain(config('app.domain.store'))
            ->namespace($this->namespace)
            ->group(base_path('routes/store.php'));
    }

    /**
     * 라우트 파라미터 모델 바인딩
     *
     * @return void
     */
    protected function modelBindings()
    {
        // Route::model('byappsuser', \App\Models\Byapps\User::class);
        // Route::model('passportClient', \App\Models\Passport\Client::class);
        // Route::model('service', \App\Models\Agent\Service::class);
        // Route::model('oauthClient', \App\Models\Passport\Client::class);
        Route::model('site', \App\Models\Client\Site::class);
        // Route::model('refund', \App\Models\PaymentRefund::class);
        // Route::model('review', \App\Models\StoreReview::class);
        // Route::model('client_inquiry', \App\Models\ClientInquiry::class);
        // Route::model('agent_inquiry', \App\Models\AgentInquiry::class);
    }
}
