<?php

namespace Mrc\Ecom\FormWidgets;

use Backend\Classes\FormWidgetBase;
use Mrc\Ecom\Models\Coupon;

class CouponBox extends FormWidgetBase
{
    public function widgetDetails()
    {
        return [
            'name' => 'CouponBox',
            'description' => 'Coupon Box'
        ];
    }
    
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('widget');
    }
    
    public function prepareVars()
    {
        $this->vars['id'] = $this->model->id;
        $this->vars['coupons'] = Coupon::all()->lists('name', 'id');
        $this->vars['name'] = $this->formField->getName() . '[]';
        
        if(!empty($this->getLoadValue())) {
            $this->vars['selectedValues'] = $this->getLoadValue();
        } else {
            $this->vars['selectedValues'] = [];
        }
    }
    
    public function loadAssets()
    {
        $this->addJs('js/select2.js');
    }
}