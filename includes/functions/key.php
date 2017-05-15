<?php

/**
 * @param $value    mixed   The value to be translated by the key.
 * @param $type     string  Values: Key (meta_key), Value (meta_value, int), Display (friendly name)
 *
 */
function dt_key_overall_status ($item, $type) {

    switch ($type) {
        case 'key':

            switch ($item) {
                case 'unassigned':
                    return array('');
                break;
                default:
                    break;
            }


            break;
        case 'value':

            break;
        case 'display':

            break;
        default:
            break;
    }
}