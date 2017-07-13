<script type="text/javascript">
	jQuery(document).ready
	(
		function()
		{
			var dates = jQuery(".datefield").datepicker
			(
				{
					dateFormat: 'yy-mm-dd',
					changeMonth: true,
					onSelect: function(selectedDate)
					{
						var option = this.id == "from_datefield" ? "minDate" : "maxDate", instance = jQuery( this ).data( "datepicker" ),date = jQuery.datepicker.parseDate(instance.settings.dateFormat || jQuery.datepicker._defaults.dateFormat,selectedDate, instance.settings);
						dates.not( this ).datepicker( "option", option, date );
					}
				}
			);
		}
	);
</script>
<?php echo __('From'); ?>:<input type="text" name="from_datefield" id="from_datefield" class="datefield" value="<?php echo $from_datefield; ?>" readonly="readonly" />
<br />
<?php echo __('To'); ?>:&nbsp;<input type="text" name="to_datefield" id="to_datefield" class="datefield" value="<?php echo $to_datefield; ?>" readonly="readonly" />
		