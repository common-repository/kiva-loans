<?php
/**
 * Kiva Loans widget class
 */
class Kiva_Loans_v1_0_w_Kiva_Loans extends WP_Widget {
  function __construct() {
    $widget_ops = array('classname' => 'Kiva_Loans_v1_0_w_Kiva_Loans', 'description' => __( 'Shows your Kiva Loans' ) );
        parent::__construct('Kiva_Loans_v1_0_w_Kiva_Loans', __('Kiva Loans'), $widget_ops );
  }

  function widget( $sidebar, $instance ) {
    echo $sidebar["before_widget"];
    $title = $instance['title'];
    $your_lender_id = $instance['your_lender_id'];
    $number_of_items = $instance['number_of_items'];
    $show_all_loans_link = $instance['show_all_loans_link'];
    $show_about_kiva_link = $instance['show_about_kiva_link'];

    /* Display */
    
    $title = apply_filters( "widget_title", $title );
    if ( ! empty( $title ) )
      echo $sidebar["before_title"] . $title . $sidebar["after_title"];
    
    if (!is_numeric($number_of_items)) { return; }
    if ($number_of_items < 1) { return; }
    if ($number_of_items > 20) { return; }
    
    $url = "http://api.kivaws.org/v1/lenders/" . $your_lender_id . "/loans.xml";
    $xml = file_get_contents($url);
    $xml = preg_replace("/[\r\n]/","",$xml);
    
    preg_match_all("/<name>([^<]*)/",$xml,$names);
    $names = $names[1];
    
    preg_match_all("/<image><id>([^<]*)/",$xml,$images);
    $images = $images[1];
    
    preg_match_all("/<loan><id>([^<]*)/",$xml,$loans);
    $loans = $loans[1];
    
    $x = 0;
    
    echo "<table>";
    foreach ($names as $n) {
        if ($x < $number_of_items) {
            echo "<tr><td valign='top'>";
            echo "<a href='http://www.kiva.org/lend/" . $loans[$x] . "' target=_new border=0>";
            echo "<img src='http://www.kiva.org//img/w80h80/" . $images[$x] . ".jpg' border=0>";
            echo "</a>";
            echo "</td><td valign='center'><center><b>";
            echo "<a href='http://www.kiva.org/lend/" . $loans[$x] . "' target=_new>";
            echo $names[$x];
            echo "</a>";
            echo "</b></center></td></tr>";
        }
        $x++;
    }
    if ($show_all_loans_link=="Show All Loans Link") {
        echo "<tr><td colspan=2 align=center><hr><b><a target=_new href='http://www.kiva.org/lender/" . $your_lender_id . "'>";
        echo "See All My Loans</a></b><hr></td></tr>";
    }
    if ($show_about_kiva_link=="Show About Kiva Link") {
        echo "<tr><td colspan=2 align=center><hr><b><a target=_new href='http://www.kiva.org/invitedby/" . $your_lender_id . "'>";
        echo "Learn about Kiva</a></b><hr></td></tr>";
    }
    echo "</table>";
    /* Display */

    echo $sidebar["after_widget"];
  }

  function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    $instance['title'] = strip_tags( $new_instance['title'] );
    $instance['your_lender_id'] = strip_tags( $new_instance['your_lender_id'] );
    $instance['number_of_items'] = strip_tags( $new_instance['number_of_items'] );
    if( isset( $new_instance['show_all_loans_link'] ) )
      $instance['show_all_loans_link'] = $new_instance['show_all_loans_link'] ;
    else
      $instance['show_all_loans_link'] = '' ;
    if( isset( $new_instance['show_about_kiva_link'] ) )
      $instance['show_about_kiva_link'] = $new_instance['show_about_kiva_link'] ;
    else
      $instance['show_about_kiva_link'] = '' ;
    return $instance;
  }

  function form( $instance ) {
    $defaults = array(
                 'title' => 'Kiva Loans',
                 'your_lender_id' => 'justme',
                 'number_of_items' => '10',
                 'show_all_loans_link' => 'Show All Loans Link',
                 'show_about_kiva_link' => 'Show About Kiva Link',
                );
    $instance = wp_parse_args( (array) $instance, $defaults);
    ?>
    <div id='<?php echo $this->get_field_id("wp_pde_form"); ?>' class="pde_widget ">
    <?php
    $title = esc_attr( $instance['title'] );
?>
    <div class="pde_form_field pde_form_text title">
      <label for="<?php echo $this->get_field_id('title'); ?>">
      <div class="pde_form_title"><?php esc_html_e( __('Title') ); ?></div>
      <input type="text" value="<?php echo $title; ?>" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" />
        <div class="description-small"><?php _e( 'If given, the title is displayed at the top of the widget.' ); ?></div>
      </label>
    </div> <!-- title -->
<?php 
    $your_lender_id = esc_attr( $instance['your_lender_id'] );
?>
    <div class="pde_form_field pde_form_text your_lender_id">
      <label for="<?php echo $this->get_field_id('your_lender_id'); ?>">
      <div class="pde_form_title"><?php esc_html_e( __('Your Lender ID') ); ?></div>
      <input type="text" value="<?php echo $your_lender_id; ?>" name="<?php echo $this->get_field_name('your_lender_id'); ?>" id="<?php echo $this->get_field_id('your_lender_id'); ?>" />
        <div class="description-small">If you don't know it you can find it here: <a href='http://www.kiva.org/myLenderId'>http://www.kiva.org/myLenderId</a></div>
      </label>
    </div> <!-- your_lender_id -->
<?php 
    $number_of_items = esc_attr( $instance['number_of_items'] );
?>
    <div class="pde_form_field pde_form_text number_of_items">
      <label for="<?php echo $this->get_field_id('number_of_items'); ?>">
      <div class="pde_form_title"><?php esc_html_e( __('Number of Items') ); ?></div>
      <input type="text" value="<?php echo $number_of_items; ?>" name="<?php echo $this->get_field_name('number_of_items'); ?>" id="<?php echo $this->get_field_id('number_of_items'); ?>" />
        <div class="description-small"><?php _e( 'Enter the Number of Loans to display. Between 1 and 20' ); ?></div>
      </label>
    </div> <!-- number_of_items -->
<?php 
?>
    <div class="pde_form_field pde_form_checkbox show_all_loans_link">
      <label for="<?php echo $this->get_field_id('show_all_loans_link'); ?>">
        <input class="wp_pde_checkbox" id="<?php echo $this->get_field_id('show_all_loans_link'); ?>"
           value="Show All Loans Link"
           name="<?php echo $this->get_field_name('show_all_loans_link'); ?>"
           type="checkbox"<?php checked(isset($instance['show_all_loans_link']) ? $instance['show_all_loans_link'] : '', 'Show All Loans Link'); ?> />
      <div class="pde_form_title"><?php esc_html_e( __('Show All Loans Link') ); ?></div>
      <div class="description-small"><?php _e( '' ); ?></div>
      </label>
    </div> <!-- show_all_loans_link -->
<?php 
?>
    <div class="pde_form_field pde_form_checkbox show_about_kiva_link">
      <label for="<?php echo $this->get_field_id('show_about_kiva_link'); ?>">
        <input class="wp_pde_checkbox" id="<?php echo $this->get_field_id('show_about_kiva_link'); ?>"
           value="Show About Kiva Link"
           name="<?php echo $this->get_field_name('show_about_kiva_link'); ?>"
           type="checkbox"<?php checked(isset($instance['show_about_kiva_link']) ? $instance['show_about_kiva_link'] : '', 'Show About Kiva Link'); ?> />
      <div class="pde_form_title"><?php esc_html_e( __('Show About Kiva Link') ); ?></div>
      <div class="description-small"><?php _e( '' ); ?></div>
      </label>
    </div> <!-- show_about_kiva_link -->
<?php 
    ?>
    </div>
    <?php
  }

  static function __widgets_init() {
    register_widget( 'Kiva_Loans_v1_0_w_Kiva_Loans' );
  }

  static function __enqueue_css() {
     $file = '';
     $script_id = '' ;
     wp_enqueue_style( $script_id, plugins_url( $file, __FILE__ ) );
     do_action( 'kiva-loans_enqueue_css', null );
  }
}

add_action("widgets_init", array('Kiva_Loans_v1_0_w_Kiva_Loans', '__widgets_init'));
add_action("load-widgets.php", array('Kiva_Loans_v1_0_w_Kiva_Loans', '__enqueue_css'));

?>
