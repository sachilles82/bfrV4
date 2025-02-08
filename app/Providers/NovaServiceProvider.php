<?php

namespace App\Providers;

use App\Nova\Dashboards\Main;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Menu\Menu;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Nova::userTimezone(function (Request $request) {
            return $request->user()?->timezone;
        });

        Nova::withBreadcrumbs();

        $this->footer();


        Nova::userMenu(function (Request $request, Menu $menu) {
            return $menu
//                ->append(MenuItem::externalLink('API Docs', 'http://example.com'))
                ->prepend(MenuItem::link('Mein Profil', '/resources/admins/'.$request->user()->getKey()));
        });



        Nova::mainMenu(function (Request $request) {

            return [
                MenuSection::dashboard(Main::class)
                    ->icon('presentation-chart-line'),

                MenuSection::make('Organisation', [
//                    MenuItem::make('Alle Firmen', '/resources/companies'),
//                    MenuItem::make('Alle Inhaber', '/resources/owners'),
//                    MenuItem::make('Alle Mitarbeiter', '/resources/employees'),
//                    MenuItem::make('Neue Firma', '/resources/companies/new'),
//                    MenuItem::make('Adressen', '/resources/addresses'),
//                    MenuItem::make('Alle Teams', '/resources/teams'),

                ])->icon('office-building')->collapsable(),

                MenuSection::make('User', [
//                    MenuItem::make('Alle Inhaber', '/resources/owners'),
                ])->icon('document-text')->collapsable(),


                MenuSection::make('Nova Settings', [
//                    MenuItem::resource(Admin::class),
//                    MenuItem::resource(Team::class),
//                    MenuItem::make('Alle Accounts', '/resources/users'),
//                    MenuItem::resource(Team::class),
                ])->icon('user')->collapsable(),


                MenuSection::make('Authorization', [
//                    MenuItem::resource(Role::class),
//                    MenuItem::resource(Permission::class),
                ])->icon('shield-check')->collapsable(),

                MenuSection::make('Einstellungen', [
                    MenuItem::make('Branchen', '/resources/industries'),
//                    MenuItem::resource(Country::class),
//                    MenuItem::resource(State::class),
//                    MenuItem::resource(City::class),
                ])->icon('cog')->collapsable(),
            ];
        });
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
                ->withAuthenticationRoutes()
                ->withPasswordResetRoutes()
                ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return in_array($user->email, [
                //
            ]);
        });
    }

    /**
     * Get the dashboards that should be listed in the Nova sidebar.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [
            new \App\Nova\Dashboards\Main,
        ];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    public function footer():void
    {
        Nova::footer(function ($request){
            return \Blade::render(string: 'nova/footer');
        });
    }
}
