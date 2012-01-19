<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Contains methods for the X axis of the graph
 */
class KReport_Element_Axis_X extends KReport_Element_Axis
{
	private $labels = null;

	const LABEL_ROTATE = 31101;
	const LABEL_COLOUR = 31102;
	const LABEL_SIZE   = 31103;
	const LABEL_STEPS  = 31104;

	/**
	 * Instantiate a new OFC2 OFC_Elements_Axis_X object and assign properties to it
	 * 
	 * @return KReport_Element_Axis_X The instance being operated on
	 * @access public
	 */
	function execute()
	{
		$this->ofc_element = new OFC_Elements_Axis_X();

		foreach($this->_config as $var=>$value)
		{
			switch($var)
			{
				case self::LABEL_STEPS:
					if (is_null($this->labels))
						$this->labels = new OFC_Elements_Axis_X_Label_Set();

					$this->labels->set_steps($value);

					$this->ofc_element->set_labels($this->labels);
				break;
				case self::LABEL_SIZE:
					if (is_null($this->labels))
						$this->labels = new OFC_Elements_Axis_X_Label_Set();

					$this->labels->set_size($value);

					$this->ofc_element->set_labels($this->labels);
				break;
				case self::LABEL_COLOUR:
					if (is_null($this->labels))
						$this->labels = new OFC_Elements_Axis_X_Label_Set();

					$this->labels->set_colour($value);

					$this->ofc_element->set_labels($this->labels);
				break;
				case self::LABEL_ROTATE:
					if (is_null($this->labels))
						$this->labels = new OFC_Elements_Axis_X_Label_Set();

					$this->labels->rotate($value);

					$this->ofc_element->set_labels($this->labels);
				break;
				case self::LABELS:
					if (!is_array($value))
						throw new Exception('Labels must be an array');

					if (is_null($this->labels))
						$this->labels = new OFC_Elements_Axis_X_Label_Set();

					$this->labels->set_labels($value);

					$this->ofc_element->set_labels($this->labels);
				break;
			}
		}

		parent::execute();

		return $this;
	}
}