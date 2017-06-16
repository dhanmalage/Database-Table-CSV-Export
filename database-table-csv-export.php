<?php

/*
Plugin Name: Database Table CSV Export
Plugin URI: https://github.com/dhananjaya3107/Database-Table-CSV-Export.git
Description: Plugin to export any database table in to CSV
Version: 1.0
Author: Dananjaya Maha Malage
Author URI: http://www.whenalive.com/
*/


class CSVExport
{
    /**
     * Constructor
     */
    public function __construct()
    {
        global $table_name;

        if(isset($_POST['table-name']))
        {
            $table_name = $_POST['table-name'];

            $csv = $this->generate_csv($table_name);

            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private", false);
            header("Content-Type: application/octet-stream");
            header("Content-Disposition: attachment; filename=\"Data-Table-Export.csv\";" );
            header("Content-Transfer-Encoding: binary");

            echo $csv;
            exit;
        }

    // Add extra menu items for admins
        add_action('admin_menu', array($this, 'admin_menu'));

    // Create end-points
        add_filter('query_vars', array($this, 'query_vars'));
        add_action('parse_request', array($this, 'parse_request'));
    }

    /**
     * Add extra menu items for admins
     */
    public function admin_menu()
    {
        add_menu_page('CSV Export', 'CSV Export', 'manage_options', 'download_report', array($this, 'download_report'));
    }

    /**
     * Allow for custom query variables
     */
    public function query_vars($query_vars)
    {
        $query_vars[] = 'download_report';
        return $query_vars;
    }

    /**
     * Parse the request
     */
    public function parse_request(&$wp)
    {
        if(array_key_exists('download_report', $wp->query_vars))
        {
            $this->download_report();
            exit;
        }
    }

    /**
     * Download report
     */
    public function download_report()
    {
        echo '<div class="wrap">';
        echo '<div id="icon-tools" class="icon32"></div>';
        echo '<h2>Export Data Tables</h2>';
        echo '<p>Select the table from drop down you wish to export!</p>';

        ?>
        <form action="" method="post">
            <select name="table-name">
                <option value="">Select a Table</option>
                <option value="posts">Posts</option>
                <option value="postmeta">Postmeta</option>
                <option value="users">Users</option>
                <option value="usermeta">Usersmeta</option>
            </select>
            <input type="submit">
        </form>

<?php
    }

    /**
     * Converting data to CSV
     */
    public function generate_csv($table_name)
    {
        global $wpdb;

        $results = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix.$table_name.";",ARRAY_A);

        if (empty($results)) {
            return;
        }

        $csv_output = '"'.implode('","',array_keys($results[0])).'",'."\n";;

        foreach ($results as $row) {
            $csv_output .= '"'.implode('","',$row).'",'."\n";
        }
        $csv_output .= "\n";

        return $csv_output;

    }
}

// Instantiate a singleton of this plugin
$csvExport = new CSVExport();