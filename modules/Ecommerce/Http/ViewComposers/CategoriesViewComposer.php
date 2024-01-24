<?php

namespace Modules\Ecommerce\Http\ViewComposers;

use App\Models\Tenant\Catalogs\Tag;
use App\Repositories\UserRepository;
use Illuminate\View\View;

class CategoriesViewComposer
{

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $categories = Tag::orderBy('id', 'DESC')->get();

        $view->with('categories', $categories);
    }
}
