<?php

class BootstrapLinkPager extends CLinkPager {
    public $internalPageCssClass = 'paginate_button';
    public $selectedPageCssClass = 'active';
    public $header = '';
    public $htmlOptions = array(
        'id' => '',
        'class' => 'pagination'
    );

}
