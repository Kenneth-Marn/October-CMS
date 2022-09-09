<?php

namespace Mrc\Ecom\FormWidgets;

use Backend\Classes\FormWidgetBase;
use Config;
use RainLab\User\Models\User;

class UserBox extends FormWidgetBase
{
    public function widgetDetails()
    {
        return [
            'name' => 'Userbox',
            'description' => 'Field for adding users'
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
        $this->vars['users'] = User::all()->lists('name', 'id');
        $this->vars['name'] = $this->formField->getName() . '[]';
        
        if(!empty($this->getLoadValue())) {
            $this->vars['selectedValues'] = $this->getLoadValue();
        } else {
            $this->vars['selectedValues'] = [];
        }
    }

    public function loadAssets()
    {
        $this->addCss('css/select2.css');
        $this->addJs('js/select2.js');
    }
}
