<?php
/*
Plugin Name: DH Events Calendar
Plugin URI: http://wordpress.org/extend/plugins/dh-events-calendar
Description: Create, and manage a new type of content, the events, also setup a shortcodes for event calendar and a list year-month-day of events.
Version: 2.0
Author: Diego Jesus Hincapie Espinal
Author URI: http://diegojesushincapie.wordpress.com/
License:
Create, and manage a new type of content, the events, also setup a shortcodes for event calendar and a list year-month-day of events.
Copyright (C) 2011  Diego Jesus Hincapie Espinal

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/


if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
    die('You are not allowed to call this page directly.');
}


if (!class_exists('Dheventscalendar'))
{
    class Dheventscalendar
    {
        public function __construct()
        {
            if (is_admin())
            {
				add_action( 'init', array($this, 'create_post_type' ));
					
				add_action('add_meta_boxes', array($this, 'add_date_metaboxes'));
				add_action('save_post', array($this, 'savedates'));
            }
				
            add_shortcode('dhecdatepicker', array($this, 'dhecdatepicker_function'));
            
            wp_enqueue_script('jquery');
            wp_enqueue_script('jqueryui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js', array('jquery'));
            wp_register_style( 'jqueryuicss', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css' );
            wp_enqueue_style( 'jqueryuicss');
            wp_register_style('dheccss', plugins_url( 'css/dhecmain.css' , __FILE__ ));
            wp_enqueue_style('dheccss');
        }
		
		function create_post_type() 
		{
			register_post_type( 'eventsdh', array('labels' => array('name' => __( 'Events' ), 'singular_name' => __( 'Event' ), 'add_new' => __('New Event'), 'edit_item', __('Edit event'), 'menu_name' => 'Events dh', 'add_new_item' => __('New event')), 'public' => true));
		}
		
		public function dhecdatepicker_function()
		{
            $cadena = '';
            $cadena .= '<div id="dheccontainer">';
            $cadena .= '<div id="dheceventlist">';

            if(isset($_POST['datehidden']))
            {
                $cadena .= $this -> eventlist($_POST['datehidden']);
            }
            else
            {
                $cadena .= __('Please, select a date');
            }

            $cadena .= '</div>';
            $cadena .= '<div id="dhecdatepicker"></div>';
			
			ob_start();
			include('views/calendar.php');
    	    $cadena .= ob_get_clean();
			
            $cadena .= '</div>';
            return $cadena;
		}
		
		protected function eventList($date)
		{
			$cadena = '';
			
            global $wpdb;

			$query = "SELECT ".$wpdb->prefix."posts.ID, ".$wpdb->prefix."posts.post_title, ".$wpdb->prefix."posts.post_content, ".$wpdb->prefix."posts.post_excerpt, ".$wpdb->prefix."posts.guid FROM ".$wpdb->prefix."posts, ".$wpdb->prefix."postmeta WHERE meta_key='from_datefield' AND meta_value<='".$date."' AND post_id IN (SELECT post_id FROM ".$wpdb->prefix."postmeta WHERE meta_key='to_datefield' AND meta_value>='".$date."') AND post_id=ID AND post_status='publish' AND post_type='eventsdh'";
			
            $events = $wpdb->get_results($query, OBJECT);
			
			$eventsa = array();
			
			foreach($events as $key => $item)
			{
				$query = "SELECT ".$wpdb->prefix."postmeta.* FROM ".$wpdb->prefix."postmeta WHERE post_id=".$item->ID." AND (meta_key='from_datefield' OR meta_key='to_datefield')";
			
	            $eventdate = $wpdb->get_results($query, OBJECT);	
				
				$eventsa[$key] = array('id' => $item->ID, 'title' => $item->post_title, 'content' => $item->post_content, 'excerpt' => $item->post_excerpt, 'guid' => $item->guid);
				
				foreach($eventdate as $key2=> $item2)
				{
					$eventsa[$key][$item2->meta_key] = $item2->meta_value;
				}
			}
			
			ob_start();
			include('views/list.php');
    	    $cadena .= ob_get_clean();
			
            $cadena .= '</div>';
            return $cadena;
		}
		
		public function add_date_metaboxes()
		{	
			add_meta_box('wp_event_date_dh', __('Range date'), array($this, 'datefield'), 'eventsdh', 'side');
		}
		
		public function datefield()
		{
			global $post;
			
			$from_datefield = get_post_meta($post->ID, 'from_datefield', true);
			
			$to_datefield = get_post_meta($post->ID, 'to_datefield', true);
			
			if($from_datefield==='')
			{
				$from_datefield = date('Y-m-d');
			}
			
			if($to_datefield==='')
			{
				$to_datefield = date('Y-m-d');
			}
			
			include('views/datefield.php');
		}
		
		public function savedates($post_id)
		{
			global $post;
			if( $post->post_type != 'revision' )
			{
				if(get_post_meta($post_id, 'from_datefield', false))
				{
					update_post_meta($post_id, 'from_datefield', $_POST['from_datefield']);
				}
				else
				{
					add_post_meta($post_id, 'from_datefield', $_POST['from_datefield']);
				}
				if(get_post_meta($post_id, 'to_datefield', false))
				{
					update_post_meta($post_id, 'to_datefield', $_POST['to_datefield']);
				}
				else
				{
					add_post_meta($post_id, 'to_datefield', $_POST['to_datefield']);
				}
			}
		}
    }

    $dheventscalendar = new Dheventscalendar();
}


        function dhec_excerpt($text, $excerpt = false)
        {
            if ($excerpt) return $excerpt;

            $text = strip_shortcodes( $text );

            //$text = apply_filters('the_content', $text);
            $text = str_replace(']]>', ']]&gt;', $text);
            $text = strip_tags($text);
            $excerpt_length = apply_filters('excerpt_length', 55);
            $excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');
            $words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
            if ( count($words) > $excerpt_length ) {
                    array_pop($words);
                    $text = implode(' ', $words);
                    $text = $text . $excerpt_more;
            } else {
                    $text = implode(' ', $words);
            }

            return apply_filters('wp_trim_excerpt', $text, $raw_excerpt);
        }

?>