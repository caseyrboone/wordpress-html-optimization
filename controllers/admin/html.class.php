<?php
namespace O10n;

/**
 * HTML Optimization Admin Controller
 *
 * @package    optimization
 * @subpackage optimization/controllers/admin
 * @author     Optimization.Team <info@optimization.team>
 */
if (!defined('ABSPATH')) {
    exit;
}

class AdminHtml extends ModuleAdminController implements Module_Admin_Controller_Interface
{
    protected $admin_base = 'tools.php';

    // tab menu
    protected $tabs = array(
        'intro' => array(
            'title' => '<span class="dashicons dashicons-admin-home"></span>',
            'title_attr' => 'Intro'
        ),
        'code' => array(
            'title' => '<span class="dashicons dashicons-editor-code"></span> Code Optimization',
            'title_attr' => 'HTML Code Optimization'
        ),
        'links' => array(
            'title' => '<span class="dashicons dashicons-admin-links"></span> Link Optimization',
            'title_attr' => 'HTML Link Optimization'
        ),
        'images' => array(
            'title' => '<span class="dashicons dashicons-format-image"></span> Image Optimization',
            'title_attr' => 'HTML Image Optimization'
        )
    );
    /**
     * Load controller
     *
     * @param  Core       $Core Core controller instance.
     * @return Controller Controller instance.
     */
    public static function &load(Core $Core)
    {
        // instantiate controller
        return parent::construct($Core, array(
            'AdminView'
        ));
    }

    /**
     * Setup controller
     */
    protected function setup()
    {
        // settings link on plugin index
        add_filter('plugin_action_links_' . $this->core->modules('html')->basename(), array($this, 'settings_link'));

        // meta links on plugin index
        add_filter('plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2);

        // title on plugin index
        add_action('pre_current_active_plugins', array( $this, 'plugin_title'), 10);

        // admin options page
        add_action('admin_menu', array($this, 'admin_menu'), 50);
    }
    
    /**
     * Admin menu option
     */
    public function admin_menu()
    {
        global $submenu;

        // WPO plugin or more than 1 optimization module, add to optimization menu
        if (defined('O10N_WPO_VERSION') || count($this->core->modules()) > 1) {
            add_submenu_page('o10n', __('HTML Optimization', 'o10n'), __('HTML', 'o10n'), 'manage_options', 'o10n-html', array(
                 &$this->AdminView,
                 'display'
             ));

            // change base to admin.php
            $this->admin_base = 'admin.php';
        } else {

        // add menu entry
            add_submenu_page('tools.php', __('HTML Optimization', 'o10n'), __('HTML Optimization', 'o10n'), 'manage_options', 'o10n-html', array(
                 &$this->AdminView,
                 'display'
             ));
        }
    }

    /**
     * Settings link on plugin overview.
     *
     * @param  array $links Plugin settings links.
     * @return array Modified plugin settings links.
     */
    final public function settings_link($links)
    {
        $settings_link = '<a href="'.esc_url(add_query_arg(array('page' => 'o10n-html','tab' => 'code'), admin_url($this->admin_base))).'">'.__('Settings').'</a>';
        array_unshift($links, $settings_link);

        return $links;
    }

    /**
     * Return help tab data
     */
    final public function help_tab()
    {
        $data = array(
            'name' => __('HTML Optimization', 'o10n'),
            'github' => 'https://github.com/o10n-x/wordpress-html-optimization',
            'wordpress' => 'https://wordpress.org/support/plugin/html-optimization',
            'docs' => 'https://github.com/o10n-x/wordpress-html-optimization/tree/master/docs'
        );

        return $data;
    }

    /**
     * Show row meta on the plugin screen.
     */
    final public function plugin_row_meta($links, $file)
    {
        if ($file == $this->core->modules('html')->basename()) {
            $lgcode = strtolower(get_locale());
            if (strpos($lgcode, '_') !== false) {
                $lgparts = explode('_', $lgcode);
                $lgcode = $lgparts[0];
            }
            if ($lgcode === 'en') {
                $lgcode = '';
            }
            
            $plugin_links = $this->help_tab();

            if ($plugin_links && isset($plugin_links['github'])) {
                $row_meta = array(
                    'o10n_version' => '<a href="'.trailingslashit($plugin_links['github']).'releases/" target="_blank" title="' . esc_attr(__('View Version History', 'o10n')) . '" style=""><span class="dashicons dashicons-clock"></span> ' . __('Version History', 'o10n') . '</a>'
                );
            }

            return array_merge($links, $row_meta);
        }

        return (array) $links;
    }

    /**
     * Plugin title modification
     */
    public function plugin_title()
    {
        ?><script>jQuery(function($){var r=$('*[data-plugin="<?php print $this->core->modules('html')->basename(); ?>"]');
            $('.plugin-title strong',r).html('<?php print $this->core->modules('html')->name(); ?><a href="https://optimization.team" class="g100" style="font-size: 10px;float: right;font-weight: normal;opacity: .2;line-height: 14px;">O10N</span>');
});</script><?php
    }
}
