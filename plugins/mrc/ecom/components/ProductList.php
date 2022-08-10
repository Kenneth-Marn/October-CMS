<?php namespace Mrc\Ecom\Components;

use Cms\Classes\ComponentBase;
use ApplicationException;
use Mrc\Ecom\Models\Product;

class ProductList extends ComponentBase
{

    /**
     * Registers the hero to be accessible within the component
     * @var Klyp\Surveys\Models\Surveys
     */
    public $products;
    
    public function componentDetails()
    {
        return [
            'name'        => 'Product',
            'description' => 'Product List.'
        ];
    }

    public function defineProperties()
    {
        return [
        ];
    }

    public function onRun()
    {
       $this->products = Product::get();
    }
}
