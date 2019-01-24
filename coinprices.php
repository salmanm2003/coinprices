<?php
/*
Plugin Name: Coin Prices
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: Displays the latest coin prices from coinmarketcap API endpoint.
Version: 1.0
Author: Salman Murad
Author URI: http://URI_Of_The_Plugin_Author
License: GPL2
*/

defined( 'ABSPATH' ) or die( 'You cannot access this page.' );

// The widget class
class Coin_Prices_Widget extends WP_Widget 
{
    //Sets up the widgets name etc
    public function __construct() 
	{
        $widget_ops = array('classname'   => 'coin_prices_widget',
                            'description' => 'Displays list of coins and there prices in Dollars and Euros');
		parent::__construct('coin_prices_widget', 'Coin Prices', $widget_ops);
	}
	
	//Outputs the content of the widget
	//@param array $args
	//@param array $instance
	
    public function widget($args, $instance)
	{
        // outputs the content of the widget
        extract( $args );
		
        // Check the widget options
        $title = isset($instance['title'])?apply_filters('widget_title', $instance['title']):'';
		$limit = isset($instance['limit'])?$instance['limit']:'100';
		
		// Retrieve the data from the WPcache if available
        $request = wp_cache_get( 'request');
        if ( false === $request ) {
			// If not available send an API request
	        $request = wp_remote_get('https://api.coinmarketcap.com/v2/ticker/?limit='.$limit);
	        wp_cache_set( 'request', $request, '', '3600' );
		}
		
        // Get the coins list 
		$body = wp_remote_retrieve_body($request);
		
		// WordPress core before_widget hook (always include )
		echo $before_widget;
		
		// Display widget title if defined
		if($title) {
			echo $before_title.$title.$after_title;
		}

        // Display the widget
        // Add external css and js files
		wp_register_script ( 'pluginjs', plugins_url ( '/_inc/coinsprice.js', __FILE__ ) );
        wp_register_style ( 'plugincss', plugins_url ( '/_inc/coinsprice.css', __FILE__ ) );
		wp_enqueue_script('pluginjs');
        wp_enqueue_style('plugincss');
        ?>
		
        <div id="coinprices">
            <div class="tabs">
                <input class="input" name="tabs" type="radio" id="tab-1" checked="checked"/>
                <label class="label" for="tab-1">USD</label>
                <div class="panel">
                    <?php
                    // Display text field
                    $coins_list = json_decode($body)->data;
                    $up_arrow 	= "<img src='".plugin_dir_url( __FILE__ )."_inc/img/up.svg' width='30'>";
                    $down_arrow = "<img src='".plugin_dir_url( __FILE__ )."_inc/img/down.svg' width='30'>";
				    
		            if ($coins_list) {
					?>
                    <table class="table" id="table_1">
                        <?php
				        foreach ($coins_list as $coin) {
					    ?>
                        <tr class="table--row" id="t1_rw_<?=strtolower($coin->symbol)?>">
                            <td width="50" class="table--row--cell">
							    <img src="<?=plugin_dir_url( __FILE__ ).'_inc/img/icon/'.strtolower($coin->symbol).'.svg'; ?>">
							</td>
                            <td width="100" class="table--row--cell table--cell--left">
					            <?=$coin->symbol;?>
                            </td>
                            <td class="table--row--cell table--cell--right table--cell--blue">
                                $<?=number_format($coin->quotes->USD->price,2);?> 
                                (<?=$coin->quotes->USD->percent_change_1h;?>%)
                            </td>
                            <td width="50" class="table--row--cell table--cell--right">
                                <img src="<?=plugin_dir_url( __FILE__ )?>_inc/img/up.svg" onclick="upward('<?=strtolower($coin->symbol)?>')" >
                            </td>
                        </tr>
                        <?php
						}
						?>
					</table>
					<?php
					} else { echo "No internet connection"; }
					?>
                </div>
                <input class="input" name="tabs" type="radio" id="tab-2"/>
                <label class="label" for="tab-2"> EUR &nbsp;&nbsp;&nbsp;</label>
                <div class="panel">
					<?php
					if($coins_list) {
					?>
					<table class="table" id="table_2">
                        <?php
				        foreach ($coins_list as $coin) {
					    ?>
                        <tr class="table--row">
                            <td width="50" class="table--row--cell">
							    <img src="<?=plugin_dir_url( __FILE__ ).'_inc/img/icon/'.strtolower($coin->symbol).'.svg'; ?>">
							</td>
                            <td width="100" class="table--row--cell table--cell--left">
					            <?=$coin->symbol;?>
                            </td>
                            <td class="table--row--cell table--cell--right table--cell--blue">
                                &euro;<?=number_format($coin->quotes->USD->price*0.88,2);?> 
                                (<?=$coin->quotes->USD->percent_change_1h;?>%)
                            </td>
                            <td width="50" class="table--row--cell table--cell--right">
                                <img src="<?=plugin_dir_url( __FILE__ )?>_inc/img/up.svg" onclick="upward('<?=strtolower($coin->symbol)?>')" >
                            </td>
                        </tr>
                        <?php
						}
						?>
					</table>
					<?php
					} else { echo "No internet connection"; }
					?>
                 </div>
				 <label class="label" for="limit"> Limit: </label>
				 <input name="limit" value="100" id="limit" class="limit" onchange="changeLimit()" />
             </div>
		 </div>
		 <?php
		 // WordPress core after_widget hook (always include )
		 echo $after_widget;
    }
	
	// Outputs the options form on admin
	// @param array $instance The widget options
    
	public function form( $instance ) {
		// outputs the options form on admin
		// Set widget defaults
	    $defaults = array(
			'title' => 'Coin Prices',
			'limit' => '100',
		);
		
		// Parse current settings with defaults
		extract( wp_parse_args((array) $instance, $defaults )); 
		
		// Widget Title
		?>        
        <p>
            <label for="<?=esc_attr( $this->get_field_id( 'title' ) ); ?>">
                <?php _e( 'Widget Title', 'title' ); ?>
            </label>
            <input class = "widefat"
          		   id    = "<?=esc_attr( $this->get_field_id( 'title' ) ); ?>" 
                   name  = "<?=esc_attr( $this->get_field_name( 'title' ) ); ?>" 
                   type  = "text" 
                 value="<?php echo esc_attr( $title ); ?>" 
             />
        </p>
        
        <?php // Limit Field ?>
        <p>
            <label for="<?=esc_attr( $this->get_field_id( 'limit' ) ); ?>">
                <?php _e( 'Limit:', 'limit' ); ?>
            </label>
            <input class = "widefat" 
          		   id    = "<?=esc_attr( $this->get_field_id( 'limit' ) ); ?>" 
                   name  = "<?=esc_attr( $this->get_field_name( 'limit' ) ); ?>" 
                   type  = "text" 
                   value = "<?=esc_attr( $limit ); ?>" 
            />
        </p>
    <?php
    }
	
	// Processing widget options on save
	// @param array $new_instance The new options
	// @param array $old_instance The previous options
	// @return array

	public function update( $new_instance, $old_instance ) {
        // processes widget options to be saved
	    $instance = $old_instance;
		$instance['title'] = isset( $new_instance['title'] ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
		$instance['limit'] = isset( $new_instance['limit'] ) ? wp_strip_all_tags( $new_instance['limit'] ) : '';
		return $instance;
	}
	
	function frontendEnqueue() {
        wp_enqueue_style( 'coinpricesStyle', plugins_url( '/_inc/coinsprice.css', __FILE__ ));
        wp_enqueue_script( 'CoinpricesScript', plugins_url( '/_inc/js/coinsprice.js', __FILE__ ));
    }
	
}

add_action( '_init', 'addCss' );

add_action( 'widgets_init', function() {
	register_widget( 'Coin_Prices_Widget' );
});


