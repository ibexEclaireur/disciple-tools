<?php
/**
 * Removes a few common methods for DDoS attacks on the site.
 */

add_filter( 'xmlrpc_methods', 'dt_block_xmlrpc_attacks' );

function dt_block_xmlrpc_attacks( $methods ) {
    unset( $methods['pingback.ping'] );
    unset( $methods['pingback.extensions.getPingbacks'] );
    return $methods;
}

add_filter( 'wp_headers', 'dt_remove_x_pingback_header' );

function dt_remove_x_pingback_header( $headers ) {
    unset( $headers['X-Pingback'] );
    return $headers;
}
