<?php

/**
 *
 * Example:
 * <code php>
 * new MultiSelectField(
 *    $name = "topics",
 *    $title = "I am interested in the following topics",
 *    $source = array(
 *       "1" => "Technology",
 *       "2" => "Gardening",
 *       "3" => "Cooking",
 *       "4" => "Sports"
 *    ),
 *    $value = "1"
 * )
 * </code>
 *
 * 
 * @package multiselectfield
 */
class MultiSelectField extends CheckboxSetField {

	/**
	 * @param array
	 *
	 * @return HTML
	 */
	public function Field($properties = array()) {
		Requirements::javascript(THIRDPARTY_DIR."/jquery/jquery.js");
		Requirements::javascript(THIRDPARTY_DIR."/jquery-livequery/jquery.livequery.js");
		Requirements::javascript("multiselectfield/javascript/multiselectfield.js");
		Requirements::css('multiselectfield/css/multiselectfield.css');

		// Suffix [] to name to allow multiple values
		$arrayName = $this->getName().'[]';
		$attributes = array(
			'name'=>$arrayName,
			'id'=>$this->id(),
			'multiple'=>'multiple',
			'class'=> $this->extraClass() .' no-chzn',
		);
		
		if ($this->disabled) {
			$attributes['disabled'] = 'disabled';
		}

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


	public function Type() {
		return 'no-chzn multiselect optionset checkboxset';
	}

	/**
	 * Save value to dataobject
	 *
	 * @see forms/CheckboxSetField::saveInto()
	 */
	public function saveInto(DataObjectInterface $record) {
		$fieldName = $this->getName();

		$valueArray = (isset($this->value[0]) && strpos($this->value[0],',')) ? explode(',',$this->value[0]) : $this->value;

		if ($fieldName && ($record->has_many($fieldName) || $record->many_many($fieldName))) {
			// Set related records
			$record->$fieldName()->setByIDList($valueArray);
		} else {
			$record->$fieldName = implode(', ', $valueArray);
			$record->write();
		}
	}

	/**
	 * @return array
	 */
	public function dataValue() {
		return (is_array($this->value)) ? implode(', ', $this->value) : $this->value;
	}

	/**
	 * @return array
	 */
	public function Value() {
		return $this->dataValue();
	}

	/**
	 * Get array of selected IDs
	 *
	 * @return array
	 */
	public function getSelected() {
		$value = explode(', ', $this->value);

		// If value not set, try to get it from the form
		if (!$value && is_object($this->form)) {
			$record = $this->form->getRecord();

			if ($record) {
				if($record->has_many($fieldName) || $record->many_many($fieldName)) {
					$methodName = $this->name;
					$join = $record->$methodName();
					
					if ($join) {
						foreach ($join as $joinItem) {
							$value[] = $joinItem->ID;
						}
					}
				}
			}
		} else {
			$output = array();

			foreach($value as $k => $v) {
				if(isset($this->source[$v])) {
					$output[$v] = $this->source[$v];
				}
			}

			return $output;
		}

		return $value;
	}

	/**
	 * Get array of unselected IDs
	 *
	 * @return array
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
	 * @return ReadonlyField
	 */
	public function performReadonlyTransformation() {
		$values = implode(', ',$this->getSelected());
		$field = new ReadonlyField($this->name, $this->title, $values);
		$field->setForm($this->form);

		return $field;
	}
}
