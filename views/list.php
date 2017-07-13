<div id="dhecaccordion">
<?php
	$result = '';
            foreach($eventsa as $event)
            {
                $result .= '<h3><a href="#">'.$event['title'].'</a></h3>';
                $result .= '<div>';
                $result .= '<span>';
                $result .= __('Since').': ';
                $result .= $event['from_datefield'];
                $result .= '</span><br />';
                $result .= '<span>';
                $result .= __('To').': ';
                $result .= $event['to_datefield'];
                $result .= '</span><br />';

                $result .= '<span>';

                $result .= dhec_excerpt($event['content']);

                $result .= '</span><br />';

                $result .= '<a href="'.get_permalink($event->ID).'">'.__('View more').'</a>';
                $result .= '</div>';
            }
	echo $result;
?>
</div>
<script type="text/javascript">
jQuery(document).ready(function(){
jQuery("#dhecaccordion").accordion();
});
</script>