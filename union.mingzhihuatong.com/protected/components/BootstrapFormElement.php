<?php
/**
 *
 * 通过配置，生成表单元素的html
 *
 * Created by PhpStorm.
 * User: mortyu
 * Date: 14-8-25
 * Time: 下午9:45
 */

class BootstrapFormElement {

    private $name, $value, $config;

    public function __construct($name, $value, $config) {
        $this->name = $name;
        $this->value = $value;
        $this->config = $config;
    }

    public function __toString() {
        switch ($this->config['type']) {
            case 'hidden':
                $html = "<input class='form-control' type = 'hidden' value='{$this->value}' name='{$this->name}' //>";
                break;
            case 'select':
                $options = array('' => '请选择');
                foreach($this->config['options'] as $value => $label) {
                    $selected = ($value == $this->value) ? "selected" : '';
                    $options[] = "<option value='{$value}' $selected>{$label}</option>";
                }
                $options = join('', $options);
                $html = "<select class='form-control' name='{$this->name}' value='{$this->value}'>{$options}</select>";
                break;
            case'textarea':
                $html = "<textarea rows = 5 class='form-control'  name='{$this->name}' >{$this->value}</textarea>";
                break;
            case 'input':
            default:
                /*改变上传的input为隐藏 11-03*/
                if($this->name =="Attr[download_url]" ){
                    $html = "<input class='form-control' type = 'hidden' id='download_url'
                       value='{$this->value}'  name='{$this->name}'//>";
                    break;
                }
                /*end*/
                $html = "<input class='form-control' type = 'input' value='{$this->value}' name='{$this->name}'//>";
                break;
        }
        return $html;
    }
}
