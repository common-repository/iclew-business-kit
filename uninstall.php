<?php

//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) { 
    exit();
}

delete_option( 'iclew_key' );
delete_option( 'iclew_secret' );
delete_option( 'iclew_email' );
delete_option( 'iclew_connected' );
