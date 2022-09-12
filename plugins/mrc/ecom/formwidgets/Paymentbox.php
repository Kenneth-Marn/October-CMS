<?php

namespace Mrc\Ecom\FormWidgets;

use Backend\Classes\FormWidgetBase;

class PaymentBox extends FormWidgetBase
{
    public function widgetDetails()
    {
        return [
            'name' => 'PaymentBox',
            'description' => 'Field for stripe payment'
        ];
    }

    public function render()
    {
        $this->vars['id'] = $this->getId();
        $this->vars['name'] = $this->getFieldName();
        $this->vars['value'] = $this->getLoadValue();
        return $this->makePartial('widget');
    }

    public function getSaveValue($value)
    {
        return $value;
    }
}
