<script type="text/javascript">
	jQuery(document).ready
	(
		function()
		{
			jQuery("#dhecdatepicker").datepicker
			(
				{
					dateFormat: 'yy-mm-dd',
					onSelect: function(dateText, inst)
					{
						jQuery("#datehidden").val(dateText);
						jQuery("#dhecdatepickerhform").submit();
		    		}
				}
			);
	    }
	);
</script>
<form method="post" action="" id="dhecdatepickerhform">
    <input type="hidden" name="datehidden" id="datehidden" />
	<input type="hidden" name="type" id="type" value="2" />
</form>