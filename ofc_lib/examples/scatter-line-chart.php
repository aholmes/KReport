<?php

require_once(OFC_BASE_PATH . '/OFC_Chart.php');

/**
example of a scatter line chart

this uses most of the new features i've added to the php5 code base

*/

//setup 3 points, all with different javascript on click events
$data = array();
$value = new OFC_Charts_Scatter_Value(10, 20);
$value->on_click("alert('point1')");
array_push($data, $value);
$value = new OFC_Charts_Scatter_Value(20, 40);
$value->on_click("alert('point2')");
array_push($data, $value);
$value = new OFC_Charts_Scatter_Value(30, 50);
$value->on_click("alert('point3')");
array_push($data, $value);
		
$chart = new KReport_Chart();
$chart->set_bg_colour('#FFFFFF');
$chart->set_number_format(0, false, false, true);

//title
$title = new OFC_Elements_Title("");	//blank title
$chart->set_title($title);

//scatter data
$scatter = new OFC_Charts_Scatter_Line('#ffa551', 10);
//setup tool tip for all points
$dot = new OFC_Dot("hollow-dot");
$dot->set_dot_size(3);
$dot->set_halo_size(2);
$dot->set_tip("Detail:<br>Distance: #x#km<br>Weight: #y#kg");
$scatter->set_default_dot_style($dot);
$scatter->set_values($data);

//add scatter data to the chart
$chart->add_element($scatter);

//y axis
$y = new OFC_Elements_Axis_Y();
$y->set_range(0, 50, 10);
$y->set_grid_colour('#CCCCCC');
$chart->set_y_axis($y);

//x axis
$x = new OFC_Elements_Axis_X();
$x->set_range(0, 30, 5);
$x->set_grid_colour('#CCCCCC');
$x->set_offset(true);
$chart->set_x_axis($x);

//tooltip style for whole chart
$t = new OFC_Elements_Tooltip();
$t->set_shadow(true);
$t->set_stroke(5);
$t->set_colour("#F0F8FF");
$t->set_background_colour("#FFFFFF");
$t->set_title_style("{font-size: 12px; font-weight: bold; color: #0D628B;}");
$t->set_body_style("{font-size: 10px; color: #000000;}");
$t->set_hover();
$chart->set_tooltip($t);

//legends
$xLeg = new OFC_Elements_Legend_X("Distance");
$xLeg->set_style('{font-size: 10px;');
$chart->set_x_legend($xLeg);
$yLeg = new OFC_Elements_Legend_Y("Weight");
$yLeg->set_style('{font-size: 10px;');
$chart->set_y_legend($yLeg);

echo $chart->toPrettyString();

?>

