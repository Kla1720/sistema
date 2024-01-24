<?php

namespace Modules\Document\Http\ViewComposers;
use App\Models\Tenant\Document;
use Modules\Order\Models\OrderNote;

class DocumentViewComposer
{
    public function compose($view)
    {
        $view->vc_document = Document::whereNotSent()->count();
        $view->vc_order    = OrderNote::whereNotSent()->count();

        $view->vc_document_regularize_shipping = Document::whereRegularizeShipping()->count();

    }
}
