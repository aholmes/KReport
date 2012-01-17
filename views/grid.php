<?php
// TODO hbar/sbar display and bar bottom / top values
$table_data = array();

// get a count of all rows for each chart type so that columns will line up properly
$chart_counts = array();
foreach($charts as $chart_name=>$chart_data)
{
	if (!isset($chart_counts[$chart_data['class']]) || (count($chart_data['x']) - 1) > $chart_counts[$chart_data['class']])
		$chart_counts[$chart_data['class']] = (count($chart_data['x']) - 1);
}

foreach($charts as $chart_name=>$chart_data)
{
	if (in_array($chart_data['class'], array('KReport_Chart_HBar', 'KReport_Chart_StackBar')))
		$axis_key = 'y_axis';
	else
		$axis_key = 'x_axis';

	if (isset($chart_data[$axis_key]))
	{
		foreach($chart_data[$axis_key] as $index=>$x_axis)
		{
			if (isset($chart_data['x'][$index]) && !isset($table_data[$chart_data['class']][$index]))
			{
				$table_data[$chart_data['class']][$index] = array(
					'label'  => $x_axis,
					'charts' => array()
				);
			}
		}
	}
	else
	{
		foreach($chart_data['x'] as $index=>$x)
		{
			if (isset($chart_data['x'][$index]) && !isset($table_data[$chart_data['class']][$index]))
			{
				$table_data[$chart_data['class']][$index] = array(
					'label'  => $index,
					'charts' => array()
				);
			}
		}
	}

	if (!isset($table_data[$chart_data['class']]['charts']))
		$table_data[$chart_data['class']]['charts'] = array();

	if (!isset($table_data[$chart_data['class']]['totals']))
		$table_data[$chart_data['class']]['totals'] = array();

	foreach($chart_data['x'] as $index=>$x)
	{
		if (!isset($table_data[$chart_data['class']][$index]['charts'][$chart_name]))
			$table_data[$chart_data['class']][$index]['charts'][$chart_name] = array();

		if (!isset($table_data[$chart_data['class']]['totals'][$chart_name]))
			$table_data[$chart_data['class']]['totals'][$chart_name] = 0;

		if (!isset($table_data[$chart_data['class']]['charts'][$chart_name]))
			$table_data[$chart_data['class']]['charts'][$chart_name] = $chart_name;

		$table_data[$chart_data['class']][$index]['charts'][$chart_name] = $x;

		if (is_int($x))
		{
			$table_data[$chart_data['class']]['totals'][$chart_name] += $x;
		}
		else if (is_array($x))
		{
			if (isset($x[0]) && isset($x[1]))
			{
				$table_data[$chart_data['class']]['totals'][$chart_name] += $value[1];
			}
			else if (isset($x['y']))
			{
				$table_data[$chart_data['class']]['totals'][$chart_name] += $x['y'];
			}
		}
	}

	while ($index < $chart_counts[$chart_data['class']])
	{
		$index++;
		$table_data[$chart_data['class']][$index]['charts'][$chart_name] = 0;
	}
}
?>

<?php if ($show_header === true) { ?>
<h1 class="KReport_Data_Header"><?php echo $chart->get_title(); ?></h1>
<?php }

foreach($table_data as $class_name=>$chart_data) { ?>
<table class="KReport_Data_Grid">
	<tr>
		<th align="center"><?php echo ($chart->get_x_alias() ? $chart->get_x_alias() : 'X'); ?></th>
		<?php foreach($chart_data['charts'] as $chart_name) { ?>
		<th align="center"><?php echo $chart_name; ?></th>
		<?php } ?>
		<?php if (count($chart_data['charts']) > 1) { ?>
		<th>Totals</th>
		<?php } ?>
			
	</tr>
	<tr class="KReport_Totals_Row">
		<td class="KReport_Totals_Col" align="right">Totals</td>
		<?php $row_total = 0; foreach($chart_data['totals'] as $chart_total) { ?>
		<td class="KReport_Totals_Col_Total" align="right"><?php if (is_int($chart_total)) { echo number_format($chart_total, (strpos($chart_total, '.')) ? 2 : 0); $row_total += $chart_total; } ?></td>
		<?php } unset($chart_data['totals']); ?>
		<?php if (count($chart_data['charts']) > 1) { ?>
		<td class="KReport_Totals_Col_RowTotal" align="right"><?php echo number_format($row_total, (strpos($row_total, '.')) ? 2 : 0); ?></td>
		<?php } ?>
	</tr>
	<?php foreach($chart_data as $index=>$data) { if (!is_int($index)) continue; ?>
	<tr class="KReport_Data_Row">
		<td class="KReport_Data_Col_Label" align="right"><?php echo (isset($data['label']) ? $data['label'] : $index); ?></td>
		<?php $x_total = 0; foreach($data['charts'] as $chart_name=>$value) { ?>
		<td class="KReport_Data_Col_Data" align="right">
		<?php if (is_array($value)) { 
			if (isset($value[0]) && isset($value[1]))
			{
				echo number_format($value[0], (strpos($value[0], '.')) ? 2 : 0) . ' > ' . number_format($value[1], (strpos($value[1], '.')) ? 2 : 0); $x_total += $value[1];
			}
			else if (isset($value['y']))
			{
				echo number_format($value['y'], (strpos($value['y'], '.')) ? 2 : 0); $x_total += $value['y'];
			}
			// FIXME bar chart only
		} elseif(is_object($value)) {
			echo number_format($value->top, (strpos($value->top, '.')) ? 2 : 0); $x_total += $value->top;
		} else {
			echo number_format($value, (strpos($value, '.')) ? 2 : 0); $x_total += $value;
		} } ?>
		</td>
		<?php if (count($chart_data['charts']) > 1) { ?>
		<td class="KReport_Data_Col_Total" align="right">
			<?php echo number_format($x_total, (strpos($x_total, '.')) ? 2 : 0); ?>
		</td>
		<?php } ?>
	</tr>
	<?php } ?>
</table>
<?php } ?>