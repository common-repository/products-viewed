<?php
/**
 * Products Viewed
 * 
 * @package Products Viewed
 * @author LBK
 * @copyright 2021 LBK
 * @license GPL-2.0-or-later
 * @category plugin
 * @version 1.0.0
 * 
 * @wordpress-plugin
 * Plugin Name:       Products Viewed
 * Plugin URI:        https://lbk.vn/
 * Description:       Plugin Name always appear on the website
 * Version:           1.0.0
 * Requires at least: 1.0.0
 * Requires PHP:      7.4
 * Author:            LBK
 * Author             URI: https://www.facebook.com/profile.php?id=100008413214141
 * Text Domain:       plugin-menu-slug
 * License:           GPLv2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain path:       /languages/
 * 
 * Products Viewed is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *  
 * Products Viewed is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *  
 * You should have received a copy of the GNU General Public License
 * along with Products Viewed. If not, see <http://www.gnu.org/licenses/>.
*/

// Die if accessed directly
if ( !defined('ABSPATH') ) die( 'What are you doing here? You silly human!' );

if ( !class_exists('ProductsViewedWidget') ) {
	class ProductsViewedWidget extends WP_Widget{
	    function __construct()
	    {
	        parent::__construct(
	            'product_viewed',
	            'Products Viewed',
	            array(
	            'description' => 'Display products viewed'
	        ));

	        ProductsViewedWidget::product_viewed_enqueue_scipt();
	    }
	    public function widget($args, $instance)
	    {	        
			extract( $args );
	        $title = apply_filters( 'widget_title', $instance['title'] );
			echo $before_widget;
			echo $before_title.$instance['title'].$after_title;
			
			if (isset($instance['title'])) {
	            $title = $instance['title'];
	        } else {
	            $title = 'Products Viewed';
	        }
			
			 if (isset($instance['count'])) {
	            $count = $instance['count'];
	        } else {
	            $count = 5;
	        }
			
			 if (isset($instance['orderby'])) {
	            $orderby = $instance['orderby'];
	        } else {
	            $orderby = 'date';
	        }
			
			if (isset($instance['image_type'])) {
	            $image_type = $instance['image_type'];
	        } else {
	            $image_type = 'post-thumbnail';
	        }
			
			if (isset($instance['image_width'])) {
	            $image_width = $instance['image_width'];
	        } else {
	            $image_width = '90';
	        }
			
			if (isset($instance['image_height'])) {
	            $image_height = $instance['image_height'];
	        } else {
	            $image_height = '90';
	        }
			
			if (isset($instance['font_size'])) {
	            $font_size = $instance['font_size'];
	        } else {
	            $font_size = '';
	        }
	        if (isset($instance['title_lines'])) {
	            $title_lines = $instance['title_lines'];
	        } else {
	            $title_lines = '2';
	        }
			if (isset($instance['round'])) {
	            $round = $instance['round'];
	        } else {
	            $round = '';
	        }
	        if (isset($instance['fix_to_square'])) {
	            $fix_to_square = $instance['fix_to_square'];
	        } else {
	            $fix_to_square = '';
	        }

			
			
			global $woocommerce;
			$viewed_products = ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ? (array) explode( '|', $_COOKIE['woocommerce_recently_viewed'] ) : array();
			$viewed_products = array_filter( array_map( 'absint', $viewed_products ) );
			$query_args = array(
				'posts_per_page' => $count, // Hiển thị số lượng sản phẩm đã xem
				'post_status'    => 'publish', 
				'post_type'      => 'product', 
				'post__in'       => $viewed_products, 
				'orderby'        => $orderby
			);
			$query_args['meta_query'] = array();
			$query_args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
			$products = new WP_Query( $query_args );

			// The Loop
			if ( $products->have_posts() ) :
			echo '<div class = "products-viewed">';
			while ( $products->have_posts() ) : $products->the_post();
			 	?>
				
				<div class = "product-viewed-item" >
					<div class = "product-viewed-image"  style="width:<?php echo esc_attr($image_width); ?>px; height: <?php echo esc_attr($image_height); ?>px;">
						<a href = "<?php echo get_the_permalink(); ?>"><img class = "<?php if($round !=='') { echo $round; }?> <?php if($fix_to_square !=='') { echo "square"; }?>" src="<?php  echo get_the_post_thumbnail_url(get_the_ID(), $image_type); ?>"/></a>
					</div>
					<div class = "product-viewed-text">
						<h5 class ="product-viewed-title <?php echo 'fs-'.$font_size; ?>" style="-webkit-line-clamp: <?php echo esc_attr($title_lines);?>;">
							<a class = "plain" href = "<?php echo get_the_permalink(); ?>"> <?php  echo get_the_title();?> </a>
						</h5>
						<span class = "product-viewed-price">
							<?php  
								if( get_post_meta(get_the_ID(), '_sale_price', true) == '') {
								   ?>
								   <span><b><?php if( get_post_meta( get_the_ID(), '_regular_price', true)) {echo number_format(( get_post_meta( get_the_ID(), '_regular_price', true))).get_woocommerce_currency_symbol();} ?></b></span>
								   <?php 
								}
								else {
								    ?>
								    <span><del><?php if( get_post_meta( get_the_ID(), '_regular_price', true)) {echo number_format(( get_post_meta( get_the_ID(), '_regular_price', true))).get_woocommerce_currency_symbol();} ?></del></span>
									<span><b><?php if(get_post_meta( get_the_ID(), '_sale_price', true)) echo number_format(get_post_meta( get_the_ID(), '_sale_price', true)).get_woocommerce_currency_symbol();?></b></span>
								    <?php
								} 
							?>	
						</span>
					</div>
				</div>
				<?php
			endwhile;
			echo '</div>';
			endif;

			// Reset Post Data
			wp_reset_postdata();
			
			echo $args['after_widget'];

	    }
	    // Widget Backend
	    public function form($instance)
	    {
	        if (isset($instance['title'])) {
	            $title = $instance['title'];
	        } else {
	            $title = 'Products Viewed';
	        }
			
			 if (isset($instance['count'])) {
	            $count = $instance['count'];
	        } else {
	            $count = 5;
	        }
			
			 if (isset($instance['orderby'])) {
	            $orderby = $instance['orderby'];
	        } else {
	            $orderby = 'date';
	        }
			
			if (isset($instance['image_type'])) {
	            $image_type = $instance['image_type'];
	        } else {
	            $image_type = 'post-thumbnail';
	        }
			
			if (isset($instance['image_width'])) {
	            $image_width = $instance['image_width'];
	        } else {
	            $image_width = '90';
	        }
			
			if (isset($instance['image_height'])) {
	            $image_height = $instance['image_height'];
	        } else {
	            $image_height = '90';
	        }
			
			if (isset($instance['font_size'])) {
	            $font_size = $instance['font_size'];
	        } else {
	            $font_size = '';
	        }
	        if (isset($instance['title_lines'])) {
	            $title_lines = $instance['title_lines'];
	        } else {
	            $title_lines = '2';
	        }
	        if (isset($instance['round'])) {
	            $round = $instance['round'];
	        } else {
	            $round = '';
	        }
	        if (isset($instance['fix_to_square'])) {
	            $fix_to_square = $instance['fix_to_square'];
	        } else {
	            $fix_to_square = '';
	        }
			
	?>
			<p><label for="<?php echo $this->get_field_id('title');?>"><?php _e('Title:'); ?>
			<input class="widefat" id="<?php echo $this->get_field_id('title');?>" name="<?php echo $this->get_field_name('title');?>" type="text" value="<?php echo esc_attr($title);?>" /></label></p>
			

			<p><b>Query</b></p>
				
			<p><label for="<?php echo $this->get_field_id('count');?>"><?php _e('Number of products:'); ?>
			<input class="widefat" id="<?php echo $this->get_field_id('count');?>" name="<?php echo $this->get_field_name('count');?>" type="number" value="<?php echo esc_attr($count);?>" /></label></p>
			
			<p><label for="<?php echo $this->get_field_id('orderby');?>"><?php _e('Chooise sort by:'); ?>
			<select class="widefat" id="<?php echo $this->get_field_id('orderby');?>" name="<?php echo $this->get_field_name('orderby');?>">
				<option  value="">Chooise</option>
				<option <?php if($orderby == 'rand') echo 'selected'; ?> value="rand">Random</option>
				<option <?php if($orderby == 'date') echo 'selected';?> value="date">Date</option>
	        </select></label></p>
				
	        <p><b>Image</b></p>
				
			<p><label for="<?php echo $this->get_field_id('image_width');?>"><?php _e('Image Width:'); ?>
			<input class="widefat" id="<?php echo $this->get_field_id('image_width');?>" name="<?php echo $this->get_field_name('image_width');?>" type="number" value="<?php echo esc_attr($image_width);?>" /></label></p>
				
			<p><label for="<?php echo $this->get_field_id('image_height');?>"><?php _e('Image Height:'); ?>
			<input class="widefat" id="<?php echo $this->get_field_id('image_height');?>" name="<?php echo $this->get_field_name('image_height');?>" type="number" value="<?php echo esc_attr($image_height);?>" /></label></p>

			<p><label for="<?php echo $this->get_field_id('image_type');?>"><?php _e('Chooise image type:'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('image_type');?>" name="<?php echo $this->get_field_name('image_type');?>">
				<option  value="">Chooise size</option>
				<option <?php if($image_type == 'post-thumbnail') echo 'selected'; ?> value="post-thumbnail">Thumbnail</option>
				<option <?php if($image_type == 'post-medium') echo 'selected';?> value="post-medium">Mediumn</option>
				<option <?php if($image_type == 'post-large') echo 'selected';?> value="post-large">Large</option>
				<option <?php if($image_type == 'post-origin') echo 'selected';?> value="post-origin">Origin</option>
	        </select></p>

	        <p><label for="<?php echo $this->get_field_id('round');?>"><?php _e('Chooise round type:'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('round');?>" name="<?php echo $this->get_field_name('round');?>">
				<option  value="">Chooise size</option>
				<option <?php if($round == 'round-3') echo 'selected'; ?> value="round-3">S</option>
				<option <?php if($round == 'round-5') echo 'selected';?> value="round-5">M</option>
				<option <?php if($round == 'round-10') echo 'selected';?> value="round-10">L</option>
				<option <?php if($round == 'round-full') echo 'selected';?> value="round-full">Full</option>
	        </select></p>

	        

	        <p><label for="<?php echo $this->get_field_id('fix_to_square');?>"><?php _e('Fix image to square:'); ?> 
			<input class="widefat" id="<?php echo $this->get_field_id('fix_to_square');?>" name="<?php echo $this->get_field_name('fix_to_square');?>" type="checkbox" value="yes"  <?php if($fix_to_square == 'yes') echo 'checked'; ?> />
	    	</label></p>


			<p><b>Title</b></p>

			<p><label for="<?php echo $this->get_field_id('title_lines');?>"><?php _e('Title lines:'); ?>
			<input class="widefat" id="<?php echo $this->get_field_id('title_lines');?>" name="<?php echo $this->get_field_name('title_lines');?>" type="number" value="<?php echo esc_attr($title_lines);?>" /></label></p>
				
			<p><label for="<?php echo $this->get_field_id('font_size');?>"><?php _e('Chooise title size:'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('font_size');?>" name="<?php echo $this->get_field_name('font_size');?>">
				<option  value="">Chooise size</option>
				<option <?php if($font_size == 'small') echo 'selected'; ?> value="small">S</option>
				<option <?php if($font_size == 'medium') echo 'selected';?> value="medium">M</option>
				<option <?php if($font_size == 'large') echo 'selected';?> value="large">L</option>
	        </select></p>
				<p class="description">Mọi ý kiến đóng góp về Plugin xin gửi email về <b>sheensilvers@gmail.com</b> hoặc <b>service.lbk.vn@gmail.com</b></p>

			<?php
	    }
	    // Updating widget replacing old instances with new
	    public function update($new_instance, $old_instance) {
	        $instance          = array();
	        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '' ;
			$instance['count'] = (!empty($new_instance['count'])) ? strip_tags($new_instance['count']) : '' ;
			$instance['image_width'] = (!empty($new_instance['image_width'])) ? strip_tags($new_instance['image_width']) : '' ;
			$instance['image_height'] = (!empty($new_instance['image_height'])) ? strip_tags($new_instance['image_height']) : '' ;
			$instance['font_size'] = (!empty($new_instance['font_size'])) ? strip_tags($new_instance['font_size']) : '' ;
			$instance['orderby'] = (!empty($new_instance['orderby'])) ? strip_tags($new_instance['orderby']) : 'date' ;
			$instance['image_type'] = (!empty($new_instance['image_type'])) ? strip_tags($new_instance['image_type']) : 'post-thumbnail' ;
			$instance['title_lines'] = (!empty($new_instance['title_lines'])) ? strip_tags($new_instance['title_lines']) : '2' ;
			$instance['round'] = (!empty($new_instance['round'])) ? strip_tags($new_instance['round']) : '' ;
			$instance['fix_to_square'] = (!empty($new_instance['fix_to_square'])) ? strip_tags($new_instance['fix_to_square']) : '' ;
			
			return $instance;
			
		}
		static function product_viewed_enqueue_scipt() {
			wp_enqueue_style('lbk_product_viewed_script', plugin_dir_url(__FILE__) . 'assets/css/frontend.css', array(), 'all');
		}

	} 
// Class ProductsViewedWidget ends here
// Register and load the widget
	if(!function_exists('create_products_viewed_widget')) {
		function create_products_viewed_widget() {
	     register_widget('ProductsViewedWidget');
		}
	}

	add_action('widgets_init', 'create_products_viewed_widget');
}
