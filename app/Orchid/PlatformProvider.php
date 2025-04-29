<?php

declare(strict_types=1);

namespace App\Orchid;

use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;
use Orchid\Support\Color;

class PlatformProvider extends OrchidServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param Dashboard $dashboard
     *
     * @return void
     */
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);

        // ...
    }

    /**
     * Register the application menu.
     *
     * @return Menu[]
     */
    public function menu(): array
    {
        //digcomp.empresas
        return [
            Menu::make('Home')
                ->icon('bs.book')
                ->title('Navigation')
                ->route(config('platform.index')),
            
            Menu::make('Empresas')
                ->icon('bs.book')
                ->title('Navigation')
                ->route('digcomp.empresas'),

            Menu::make('Cargar Datos')
                ->icon('bs.card-list')
                ->route('dashboard.upload'),
                //->active('*/examples/form/*'),

/*             Menu::make('Datos demográficos')
                ->icon('bs.collection')
                ->route('digcomp.general.data'), */
/*                 ->active([
                    'digcomp.charts',
                    'digcomp.',
                ]), */
               /*  ->badge(fn () => 6), */

/*             Menu::make('DigComp e ICD')
                ->icon('bs.bar-chart')
                ->route('digcomp.charts.empty'),
 */
/*              Menu::make('Form Advanced')
                ->icon('bs.card-list')
                ->route('platform.example.advanced') */
               // ->active('*/examples/form/*'), 

/*             Menu::make('Overview Layouts')
                ->icon('bs.window-sidebar')
                ->route('platform.example.layouts'), */

/*             Menu::make('Grid System')
                ->icon('bs.columns-gap')
                ->route('platform.example.grid'), */



/*             Menu::make('Cards')
                ->icon('bs.card-text')
                ->route('platform.example.cards')
                ->divider(), */

            Menu::make(__('Users'))
                ->icon('bs.people')
                ->route('platform.systems.users')
                ->permission('platform.systems.users')
                ->title(__('Access Controls')),

            Menu::make(__('Roles'))
                ->icon('bs.shield')
                ->route('platform.systems.roles')
                ->permission('platform.systems.roles')
                ->divider(),

/*             Menu::make('Documentation')
                ->title('Docs')
                ->icon('bs.box-arrow-up-right')
                ->url('https://orchid.software/en/docs')
                ->target('_blank'),

            Menu::make('Changelog')
                ->icon('bs.box-arrow-up-right')
                ->url('https://github.com/orchidsoftware/platform/blob/master/CHANGELOG.md')
                ->target('_blank')
                ->badge(fn () => Dashboard::version(), Color::DARK), */
        ];
    }

    /**
     * Register permissions for the application.
     *
     * @return ItemPermission[]
     */
    public function permissions(): array
    {
        return [
            ItemPermission::group(__('System'))
                ->addPermission('platform.systems.roles', __('Roles'))
                ->addPermission('platform.systems.users', __('Users')),
        ];
    }
}
