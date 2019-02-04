<?php
// add Custom tab on Groups Page
// 
/**
 * The bp_is_active( 'groups' ) check is recommended, to prevent problems 
 * during upgrade or when the Groups component is disabled
 */
if ( bp_is_active( 'groups' ) ) :
 
class Group_Extension_Example_1 extends BP_Group_Extension {
    /**
     * Your __construct() method will contain configuration options for 
     * your extension, and will pass them to parent::init()
     */
    function __construct() {
        $args = array(
            'slug' => 'group-extension-example-1',
            'name' => 'Group Extension Example 1',
        );
        parent::init( $args );
    }
 
    /**
     * display() contains the markup that will be displayed on the main 
     * plugin tab
     */
    function display( $group_id = NULL ) {
        $group_id = bp_get_group_id();
        echo 'What a cool plugin!';
    }
 
    /**
     * settings_screen() is the catch-all method for displaying the content 
     * of the edit, create, and Dashboard admin panels
     */
    function settings_screen( $group_id = NULL ) {
        $setting = groups_get_groupmeta( $group_id, 'group_extension_example_1_setting' );
 
        ?>
        Save your plugin setting here: <input type="text" name="group_extension_example_1_setting" value="<?php echo esc_attr( $setting ) ?>" />
        <?php
    }
 
    /**
     * settings_sceren_save() contains the catch-all logic for saving 
     * settings from the edit, create, and Dashboard admin panels
     */
    function settings_screen_save( $group_id = NULL ) {
        $setting = '';
 
        if ( isset( $_POST['group_extension_example_1_setting'] ) ) {
            $setting = $_POST['group_extension_example_1_setting'];
        }
 
        groups_update_groupmeta( $group_id, 'group_extension_example_1_setting', $setting );
    }
}
bp_register_group_extension( 'Group_Extension_Example_1' );
 
endif; // if ( bp_is_active( 'groups' ) )