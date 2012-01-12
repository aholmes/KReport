<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Contains methods for OFC2 Tag Charts
 */
class KReport_Chart_Tag extends KReport_Chart
{
	const FONT     = 26002;
	const PADDING  = 26003;
	const X_ALIGN  = 26004;
	const Y_ALIGN  = 26005;
	const TEXT     = 26006;
	const ON_CLICK = 26007;
	const STYLE    = 26008;
	const ROTATE   = 26009;

	/**
	 * Instantiate a new OFC2 OFC_Tags object and assign properties to it
	 * 
	 * @return KReport_Chart_Tag The instance being operated on
	 * @access public
	 */
	function execute()
	{
		$this->ofc_chart = new OFC_tags();

		foreach($this->_config as $var=>$value)
		{
			switch($var)
			{
				case self::COLOUR:
					$this->ofc_chart->colour($value);
				break;
				case self::FONT:
					$this->ofc_chart->font($value['font_name'], $value['font_size']);
				break;
				case self::PADDING:
					$this->ofc_chart->padding($value['horizontal'], $value['vertical']);
				break;
				case self::X_ALIGN:
					switch($value)
					{
						case 'left':
							$this->ofc_chart->align_x_left();
						break;
						case 'center':
							$this->ofc_chart->align_x_center();
						break;
						case 'right':
							$this->ofc_chart->align_x_right();
						break;
					}
				break;
				case self::Y_ALIGN:
					switch($value)
					{
						case 'above':
							$this->ofc_chart->align_y_above();
						break;
						case 'center':
							$this->ofc_chart->align_y_center();
						break;
						case 'below':
							$this->ofc_chart->align_y_below();
						break;
					}
				break;
				case self::TEXT:
					$this->ofc_chart->text($value);
				break;
				case self::ON_CLICK:
					$this->ofc_chart->on_click($value);
				break;
				case self::STYLE:
					$this->ofc_chart->style($value['bold'], $value['underline'], $value['border'], $value['alpha']);
				break;
				case self::ROTATE:
					$this->ofc_chart->rotate($value);
				break;
			}
		}

		parent::execute();

		return $this;
	}

	/**
	 * Set the text for each of the tags
	 * 
	 * @param string $text The text of the tag
	 * @return KReport_Chart_Tag The instance being operated on
	 * @access public
	 */
	function text($text)
	{
		return $this->set(self::TEXT, $text);
	}

	/**
	 * Set the font for the tag text
	 * 
	 * @param string $font_name The name of the font
	 * @param integer $font_size The size of the font
	 * @return KReport_Chart_Tag The instance being operated on
	 * @access public
	 */
	function font($font_name, $font_size = 10)
	{
		return $this->set(self::FONT, array(
			'font_name' => $font_name,
			'font_size' => intval($font_size)
		));
	}

	/**
	 * Set the horizontal and vertical padding of the tag
	 *
	 * @param integer $horizontal The horizontal padding
	 * @param integer $vertical The vertical padding
	 * @return KReport_Chart_Tag The instance being operated on
	 * @access public
	 */
	function padding($horizontal, $vertical)
	{
		return $this->set(self::PADDING, array(
			'horizontal' => intval($horizontal),
			'vertical'   => intval($vertical)
		));
	}

	/**
	 * Set the X alignment of the tag
	 *
	 * @param string $align The alignment. Can be one of 'left,' 'center,' or 'right'
	 * @return KReport_Chart_Tag The instance being operated on
	 * @access public
	 */
	function x_align($align)
	{
		return $this->set(self::X_ALIGN, $align);
	}

	/**
	 * Set the Y alignment of the tag
	 *
	 * @param string $align The alignment. Can be one of 'above,' 'center,' or 'below'
	 * @return KReport_Chart_Tag The instance being operated on
	 * @access public
	 */
	function y_align($align)
	{
		return $this->set(self::Y_ALIGN, $align);
	}

	/**
	 * Set an onclick() javascript handler for the stack
	 * 
	 * @param string $click The javascript handler
	 * @return KReport_Chart_Tag The instance being operated on
	 * @access public
	 */
	function on_click($on_click)
	{
		return $this->set(self::ON_CLICK, $on_click);
	}

	/**
	 * Set the font style of the tag
	 *
	 * @param bool $bold Whether to make the tag bold
	 * @param bool $underline Whether to make the tag underlined
	 * @param integer $border The border size of the tag
	 * @param integer $alpha The alpha of the tag
	 * @return KReport_Chart_Tag The instance being operated on
	 * @access public
	 */
	function style($bold = false, $underline = false, $border = 0, $alpha = 1)
	{
		return $this->set(self::STYLE, array(
			'bold'      => ($bold) ? true : false,
			'underline' => ($underline) ? true : false,
			'border'    => intval($border),
			'alpha'     => $alpha
		));
	}

	/**
	 * Set the angle at which the tag should be rotated
	 *
	 * @param integer $angle The angle of the tag
	 * @return KReport_Chart_Tag The instance being operated on
	 * @access public
	 */
	function rotate($angle)
	{
		return $this->set(self::ROTATE, $angle);
	}
}