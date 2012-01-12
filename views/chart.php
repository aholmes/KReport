<script type="text/javascript" src="/KReport/js"></script>
<script type="text/javascript">
function kreport_chart_data_<?php echo $chart->__toString(); ?>_get()
{
	return JSON.stringify(<?php echo $chart->execute()->as_json(); ?>);
}

swfobject.embedSWF("/KReport/swf", "KReport_<?php echo $chart->__toString(); ?>", "<?php echo ($chart->get_width()) ? $chart->get_width() : 500; ?>", "<?php echo ($chart->get_height()) ? $chart->get_height() : 500; ?>", "9.0.0", {'get-data':'kreport_chart_data_<?php echo $chart->__toString(); ?>_get'}, {'get-data':'kreport_chart_data_<?php echo $chart->__toString(); ?>_get'}, {'get-data':'kreport_chart_data_<?php echo $chart->__toString(); ?>_get',wmode:'opaque'});
</script>
<div class="KReport_Chart" id="KReport_<?php echo $chart->__toString(); ?>"></div>
<?php if ($chart->get_exportable() === true) { ?>
<div class="KReport_Export_Link"><a href="?export_<?php echo $chart->__toString(); ?>">Export CSV</a></div>
<?php } ?>