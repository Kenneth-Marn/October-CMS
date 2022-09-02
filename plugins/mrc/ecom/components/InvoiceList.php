<?php

namespace Mrc\Ecom\Components;

use Cms\Classes\ComponentBase;
use Mrc\Ecom\Models\Invoice;
use Log;
use Auth;

class InvoiceList extends ComponentBase
{

    /**
     * Registers the hero to be accessible within the component
     * @var Klyp\Surveys\Models\Surveys
     */
    public $invoices;

    public function componentDetails()
    {
        return [
            'name'        => 'Invoice',
            'description' => 'Invoice List.'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        $user = Auth::getUser();
        $this->invoices = Invoice::where('user_id', $user->id)->orderBy('id', 'desc')->get();
    }
}
