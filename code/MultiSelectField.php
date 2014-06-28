<?php
/**
 * Based on CheckboxSetField with the following changes:
 * - Renders as a standard listbox (select with multiple option)
 * - Transformed with javascript (jQuery) to a pair of listboxes showing selected and unselected options
 * - $source must be an associative array (use toDropdownMap())
 * - $value must be a simple array
 *
 * @author jamie
 */
class MultiSelectField extends CheckboxSetField {

	/**
	 * Generate field HTML
	 *
	 * @see forms/CheckboxSetField::Field()
	 */
	function Field() {
		Requirements::javascript(THIRDPARTY_DIR."/jquery/jquery.js");
		Requirements::javascript(THIRDPARTY_DIR."/jquery-livequery/jquery.livequery.js");
		Requirements::javascript("multiselectfield/javascript/multiselectfield.js");
		Requirements::css('multiselectfield/css/multiselectfield.css');
		// Suffix [] to name to allow multiple values
		$arrayName = $this->Name().'[]';
		$attributes = array(
			'name'=>$arrayName,
			'id'=>$this->id(),
			'multiple'=>'multiple',
			'tabindex'=>$this->getTabIndex(),
			'class'=>$this->extraClass(),
		);
		if ($this->disabled)
		$attributes['disabled'] = 'disabled';

		// Build options
		$content = '';
		$source = $this->source;
		$value = $this->getSelected();
		foreach($source as $index => $item) {
			$selected = (isset($value[$index])) ? 'selected' : '';
			$content .= "<option $selected value=\"$index\">$item</option>";
		}

		return $this->createTag('select', $attributes, $content);
	}

	/**
	 * Save value to dataobject
	 *
	 * @see forms/CheckboxSetField::saveInto()
	 */
	public function saveInto(DataObject $record) {
		$fieldName = $this->name;

		// TODO - SiteTree admin seems to serialise value, whilst modeladmin doesn't
		// this explodes the serialised string, but there is probably a more elegant solution
		$valueArray = (isset($this->value[0]) && strpos($this->value[0],',')) ? explode(',',$this->value[0]) : $this->value;

		if ($fieldName && ($record->has_many($fieldName) || $record->many_many($fieldName))) {
			// Set related records
			$record->$fieldName()->setByIDList($valueArray);
		} else {
			$record->$fieldName = implode(', ',$valueArray);
		}
	}

	/**
	 * Get array of selected IDs
	 */
	public function getSelected() {
		$value = $this->value;
		// If value not set, try to get it from the form
		if (!$value && is_object($this->form)) {
			$record = $this->form->getRecord();
			if ($record && $record->hasMethod($this->name)) {
				$methodName = $this->name;
				$join = $record->$methodName();
				if ($join) {
					foreach ($join as $joinItem) {
						$value[] = $joinItem->ID;
					}
				}
			}
		}
		return $value;
	}

	/**
	 * Get array of unselected IDs
	 */
	public function getUnselected() {
		$items = array();
		$selected = $this->getSelected();
		$source = $this->source;
		foreach ($source as $key => $item) {
			if (!isset($selected[$key])) {
				$items[$key] = $item;
			}
		}
		return $items;
	}

	/**
	 * Return list of IDs for read only
	 *
	 * @see forms/CheckboxSetField::performReadonlyTransformation()
	 */
	public function performReadonlyTransformation() {
		$values = implode(', ',$this->getSelected());
		$field = new ReadonlyField($this->name, $this->title, $values);
		$field->setForm($this->form);
		return $field;
	}

}
?>
