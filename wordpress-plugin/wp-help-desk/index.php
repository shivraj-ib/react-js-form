<?php
/*
  Plugin Name: WP Help Desk
  Plugin URI: infobeans.com
  Description: Help Desk Ticketing System.
  Author: Shivraj Singh Rawat [shivraj.singh@infobeans.com]
  Version: 1.0
  Author URI: http://www.infobeans.com
  Compatibility: WordPress 4.9.0
 * 
 */

class WpHelpDesk {
    
    private $is_logged_in = false;
    private $user_id;
    private $user_role;
    private $user_email;
    private $user_display_name;
    
    private $statusOptions = array(
        array(
            'value' => '',
            'label' => 'Please select'
        ),
        array(
            'value' => 'open',
            'label' => 'Open'
        ),
        array(
            'value' => 'closed',
            'label' => 'Closed'
        ),
        array(
            'value' => 're-opened',
            'label' => 'Re Opened'
        )
    );

    public function __construct() {
        //initiate custom post type and taxonomies
        add_action('init', array($this, 'setUser'));
        add_action('init', array($this, 'registerTicketCategory'));
        add_action('init', array($this, 'registerTicketPostType'));        
        //ajax action for initial data setup
        add_action('wp_ajax_help_desk_init', array($this, 'init'));        
        add_action('wp_ajax_mark_ticket_readed', array($this, 'markTicketRead'));
        add_action('wp_ajax_add_new_ticket', array($this, 'addNewTicket'));
        add_action('wp_ajax_add_comment', array($this, 'addComment'));        
        //add shortcode
        add_shortcode('wp-help-desk', array($this, 'shortCodeCallBack'));
        //add meta box for setting ticket status and assign ticket.
        add_action('add_meta_boxes', array($this, 'addAssignToMetaBox'));
        add_action('add_meta_boxes', array($this, 'addTicketStatusMetaBox'));
        //save meta settings
        add_action('save_post', array($this, 'saveMetaSettings'));
    }
    
    public function addAssignToMetaBox(){
        add_meta_box(
            'ticket_assigned_to', __('Assigned To', 'ticket_assigned_to'), array($this,'assignToMetaCallback'), 'help_ticket');
    }
    
    public function addTicketStatusMetaBox(){
        add_meta_box(
            'ticket_status', __('Ticket Status', 'ticket_status'), array($this,'ticketStatusMetaCallback'), 'help_ticket');
    }
    
    public function assignToMetaCallback($post) {
        //checked
        $assigned = get_post_meta($post->ID, 'ticket_assigned_to', true);
        echo '<label for="ticket_assigned_to">';
        _e('Assigned To', 'ticket_assigned_to');
        echo '</label> ';
        echo '<select id="ticket_assigned_to" name="ticket_assigned_to" >';
        echo '<option>Please select</option>';
        $users = get_users(array('role__in' => array('helpdesk_agent')));
        foreach ($users as $user) {
            $selected = ($user->ID == $assigned) ? 'selected' : '';
            echo '<option value="' . esc_html($user->ID) . '" ' . $selected . '>' . esc_html($user->display_name) . '</option>';
        }
        echo '</select>';
    }

    public function ticketStatusMetaCallback($post){
        //checked
        $current_status = get_post_meta($post->ID, 'ticket_status', true);
        echo '<label for="ticket_status">';
        _e('Ticket Status', 'ticket_status');
        echo '</label> ';
        echo '<select id="ticket_status" name="ticket_status" >';        
        foreach ($this->statusOptions as $value) {
            $selected = ($current_status == $value['value']) ? 'selected' : '';
            echo '<option value="' . $value['value'] . '" ' . $selected . '>' .$value['label']. '</option>';
        }
        echo '</select>';
    }
    
    public function saveMetaSettings($post_id) {
        if (!current_user_can('edit_post'))
            return;
        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (!empty($_POST['ticket_assigned_to'])) {
            // Update the meta field in the database.
            update_post_meta($post_id, 'ticket_assigned_to', $_POST['ticket_assigned_to']);
        }
        if (!empty($_POST['ticket_status'])) {
            // Update the meta field in the database.
            update_post_meta($post_id, 'ticket_status', $_POST['ticket_status']);
        }
    }

    public function setUser() {
        if (is_user_logged_in()) {
            $this->is_logged_in = true;
            $current_user = wp_get_current_user();
            $this->user_id = $current_user->ID;
            $this->user_role = $current_user->roles ? $current_user->roles[0] : false;
            $this->user_email = $current_user->user_email;
            $this->user_display_name = $current_user->display_name;
        }
    }

    public function init(){
        $return = array();
        $return['is_logged_in'] = $this->is_logged_in;                  
        $return['user_name'] = $this->user_display_name;
        $return['user_role'] = $this->user_role;
        $terms = get_terms( 'ticket_category', array('hide_empty' => false) );
        
        $return['categories'] = array(
            array(
                'value' => '',
                'label' => 'Please select category'
            ),
        );
        if(!empty($terms) && !is_wp_error($terms)){
            foreach($terms as $term){
                $return['categories'][] = array(
                    'value' => $term->term_id,
                    'label' => $term->name
                );
            }
        }
        $return['status'] = $this->statusOptions;

        //query tickets
        if($this->user_role == 'helpdesk_agent'){
            $args = array(
                'posts_per_page' => -1,            
                'orderby' => 'date',
                'order' => 'DESC',
                'meta_key' => 'ticket_assigned_to',
                'meta_value' => $this->user_id,
                'post_type' => 'help_ticket',
                'post_status' => 'publish',
                'suppress_filters' => true
            );
        }else{
           $args = array(
                'posts_per_page' => -1,            
                'orderby' => 'date',
                'order' => 'DESC',
                'author' => $this->user_id,
                'post_type' => 'help_ticket',
                'post_status' => 'publish',
                'suppress_filters' => true
            );            
        }

        $return['clock_rate'] = 50000; //in mili seconds set to 0 for no auto refresh

        $tickets = get_posts($args);
        
        $return['ajaxurl'] = admin_url('admin-ajax.php'); 

        $return['tickets'] = array();

        foreach ($tickets as $ticket) {
            $meta_key = ($this->user_role == 'helpdesk_agent' ? 'unreaded_for_agent':'unreaded_for_customer');
            $return['tickets'][$ticket->ID] = array(
                'ID' => $ticket->ID,
                'post_title' => $ticket->post_title,
                'post_date' => $ticket->post_date,
                'post_modified' => $ticket->post_modified,
                'comment_count' => $ticket->comment_count,
                'comments' => get_comments(array('post_id' => $ticket->ID, 'order' => 'ASC')),
                'post_content' => $ticket->post_content,
                'unreaded' => get_post_meta($ticket->ID,$meta_key),
                'category' => wp_get_post_terms($ticket->ID, 'ticket_category', array("fields" => "names")),
                'status' => get_post_meta($ticket->ID,'ticket_status'),        
            );    
        }

        echo json_encode($return);
        exit();
    }
    
    public function markTicketRead() {
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'mark_ticket_readed') {
            //based on current user role set this
            
            $meta_key = ($this->user_role == 'helpdesk_agent' ? 'unreaded_for_agent':'unreaded_for_customer');
            
            if (false === update_post_meta($_REQUEST['post_id'], $meta_key, 0)) {
                $response = array(
                    'success' => 0,
                    'message' => 'Unable to mark ticket as readed'
                );
            } else {
                $response = array(
                    'success' => 1,
                    'message' => 'Ticket marked as reded'
                );
            }
            echo json_encode($response);

            exit();
        }
    }
    
    public function addNewTicket() {
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'add_new_ticket') {
            $title = $_REQUEST['title'];
            $content = $_REQUEST['description'];

            // Create post object
            $my_post = array(
                'post_author' => $this->user_id,
                'post_title' => wp_strip_all_tags($title),
                'post_content' => $content,
                'post_status' => 'publish',                
                'post_type' => 'help_ticket',
                'meta_input' => array('ticket_status' => 'open', 'unreaded_for_agent' => 1)                
            );

            // Insert the post into the database
            if (false === ($post_id = wp_insert_post($my_post))) {
                $response = array(
                    'success' => 0,
                    'message' => 'Unable to create a new ticket'
                );
            } else {
                wp_set_object_terms($post_id, intval($_REQUEST['category']),'ticket_category');
                $response = array(
                    'success' => 1,
                    'message' => 'New Ticket Added'
                );
            }
            echo json_encode($response);

            exit();
        }
    }
    
    public function addComment() {
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'add_comment' && isset($_REQUEST['post_id'])) {
            $time = current_time('mysql');
            $response = array();
            $data = array(
                'comment_post_ID' => $_REQUEST['post_id'],
                'comment_author' => $this->user_display_name,
                'comment_author_email' => $this->user_email,
                'comment_content' => $_REQUEST['comment_msg'],
                'comment_type' => '',
                'user_id' => $this->user_id,
                'comment_date' => $time,
                'comment_approved' => 1,
            );

            if (false === wp_insert_comment($data)) {
                $response = array(
                    'success' => 0,
                    'message' => 'Unable to insert comment.'
                );
            } else {
                update_post_meta($_REQUEST['post_id'], 'ticket_status', $_REQUEST['status']);
                $meta_key = ($this->user_role == 'helpdesk_agent' ? 'unreaded_for_customer' : 'unreaded_for_agent');
                update_post_meta(intval($_REQUEST['post_id']), $meta_key, 1);
                $response = array(
                    'success' => 1,
                    'message' => 'Your comment added successfully.'
                );
            }

            echo json_encode($response);

            exit();
        }
    }

    public function shortCodeCallBack() {
        //to do enque require js and css
        wp_enqueue_script('bootstrap', plugin_dir_url(__FILE__) . 'js/bootstrap.min.js', array('jquery'));
        wp_enqueue_style('bootstrap', plugin_dir_url(__FILE__) . 'css/bootstrap.min.css');
        wp_enqueue_script('main.d48fa7d1', plugin_dir_url(__FILE__) . 'js/main.d48fa7d1.js');
        wp_enqueue_style('main.1f4a6845', plugin_dir_url(__FILE__) . 'css/main.1f4a6845.css');
        return '<div id="wp-help-desk" data-ajaxurl="' . admin_url('admin-ajax.php') . '?action=help_desk_init"></div>';        
    }

    public function registerTicketPostType() {
        $args = array(
            'labels' =>
            array(
                'name' => 'HelpDesk Ticket',
                'singular_name' => 'HelpDesk Ticket',
                'add_new_item' => 'HelpDesk Tickets',
                'edit_item' => 'Edit HelpDesk Ticket',
                'new_item' => 'New HelpDesk Ticket',
                'view_item' => 'View HelpDesk Ticket',
                'search_items' => 'Search HelpDesk Ticket',
                'not_found' => 'No HelpDesk Ticket found.',
                'not_found_in_trash' => 'No HelpDesk Ticket found in trash.',
            ),
            'description' => '',
            'public' => false,
            'show_ui' => true,
            'has_archive' => false,
            'show_in_menu' => true,
            'exclude_from_search' => true,
            'capability_type' => 'post',
            'map_meta_cap' => true,
            'hierarchical' => false,
            'menu_position' => NULL,
            'menu_icon' => NULL,
            'query_var' => true,
            'supports' =>
            array(
                0 => 'title',
                1 => 'editor',                
                2 => 'comments',
                3 => 'author'
            )
        );

        register_post_type("help_ticket", $args);
    }

    public function registerTicketCategory() {
        $args = array(
            'labels' =>
            array(
                'name' => 'Ticket Categories',
                'singular_name' => 'Ticket Category',
                'search_items' => 'Search Ticket Categories',
                'popular_items' => 'Popular Ticket Categories',
                'all_items' => 'All Ticket Categories',
                'parent_item' => 'Parent Ticket Category',
                'parent_item_colon' => 'Parent Ticket Category:',
                'edit_item' => 'Edit Ticket Category',
                'update_item' => 'Update Ticket Category',
                'add_new_item' => 'Add new Ticket Category',
                'new_item_name' => 'New Ticket Category name',
                'separate_items_with_commas' => 'Separate Ticket Categories with commas',
                'add_or_remove_items' => 'Add or remove Ticket Categories',
                'choose_from_most_used' => 'Choose from the most used Ticket Categories',
            ),
            'label' => 'Ticket Categories',
            'hierarchical' => true,
            'show_ui' => true,
            'public' => true,
            'query_var' => true,
            'show_admin_column' => true,
        );

        $object_type = array(
            0 => 'help_ticket',
        );
        register_taxonomy("ticket_category", $object_type, $args);
    }

    public function addAgentUserRole() {
        add_role('helpdesk_agent', __('Help Desk Agent'));
    }

}

$helpDesk = new WpHelpDesk();

register_activation_hook(__FILE__, array($helpDesk, 'addAgentUserRole'));
