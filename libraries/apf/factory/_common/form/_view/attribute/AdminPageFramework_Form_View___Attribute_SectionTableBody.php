<?php 
/**
	Admin Page Framework v3.8.13 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/recipepress-reloaded>
	Copyright (c) 2013-2016, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
class AdminPageFramework_Form_View___Attribute_SectionTableBody extends AdminPageFramework_Form_View___Attribute_Base {
    public $sContext = 'section_table_content';
    protected function _getAttributes() {
        $_sCollapsibleType = $this->getElement($this->aArguments, array('collapsible', 'type'), 'box');
        return array('class' => $this->getAOrB($this->aArguments['_is_collapsible'], 'recipepress-reloaded-collapsible-section-content' . ' ' . 'recipepress-reloaded-collapsible-content' . ' ' . 'accordion-section-content' . ' ' . 'recipepress-reloaded-collapsible-content-type-' . $_sCollapsibleType, null),);
    }
}