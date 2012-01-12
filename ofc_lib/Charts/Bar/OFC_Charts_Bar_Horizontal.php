<?php
/**
 * PHP Integration of Open Flash Chart
 * Copyright (C) 2008 John Glazebrook <open-flash-chart@teethgrinder.co.uk>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 */

require_once(OFC_BASE_PATH . '/Charts/OFC_Charts_Bar.php');

class OFC_Charts_Bar_Horizontal_Value
{
	function OFC_Charts_Bar_Horizontal_Value( $left, $right )
    {
		$this->left = $left;
		$this->right = $right;
	}

	public function on_click($value){	
		$prop = "on-click";     //to work around properties with hyphens
		$this->$prop = $value;
	}

    public function set_tooltip($tip){
        $this->tip = $tip;
    }

}

class OFC_Charts_Bar_Horizontal extends OFC_Charts_Bar
{

	public function __construct()
    	{
		parent::OFC_Charts_Bar();
		$this->type      = "hbar";
	}

}

