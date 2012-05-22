<?php
/**
 * Current focus navigation block
 *
 * This block displays navigation items for just the currently active page.
 * This means it displays the active page, any siblings it has in the navigation structure, and its parent page.
 *
 * @package block_contextnavigation
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2012041200.00;        // The current plugin version (Date: YYYYMMDDXX)
$plugin->requires  = 2011070105.00;        // Requires this Moodle version
$plugin->component = 'block_contextnavigation'; // Full name of the plugin (used for diagnostics)
$plugin->maturity  = MATURITY_ALPHA;
