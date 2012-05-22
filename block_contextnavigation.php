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

/**
 * The class that is this block
 *
 * @package block_contextnavigation
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_contextnavigation extends block_base {

    /** @var string The name of the block */
    public $blockname = null;

    /** @var bool A switch to indicate whether content has been generated or not. */
    protected $contentgenerated = false;

    /**
     * Initialises the block
     */
    public function init() {
        global $CFG;
        $this->blockname = get_class($this);
        $this->title = get_string('pluginname', $this->blockname);
    }

    /**
     * All multiple instances of this block
     * @return bool Returns false
     */
    public function instance_allow_multiple() {
        return false;
    }

    /**
     * Set the applicable formats for this block to all
     * @return array
     */
    public function applicable_formats() {
        return array('all' => true);
    }

    /**
     * Returns true if the instance can be hidden
     * @return boolean
     */
    public function instance_can_be_hidden() {
        return true;
    }

    /**
     * Generates and returns the content for this block
     * @return stdClass
     */
    public function get_content() {
        // First check if we have already generated, don't waste cycles
        if ($this->contentgenerated === true) {
            return $this->content;
        }
        $this->content = new stdClass;
        $this->content->text = '';
        $this->page->navigation->initialise();
        $this->page->settingsnav->initialise();

        $navigation = $this->page->navigation->find_active_node();
        $settings = $this->page->settingsnav->find_active_node();
        $this->contentgenerated = true;

        if (!$navigation && !$settings) {
            return $this->content;
        }

        $renderer = $this->page->get_renderer($this->blockname);
        $this->content->text .= $renderer->content_start();
        if ($navigation) {
            $this->content->text .= $renderer->navigation_item($navigation);
        }
        if ($settings) {
            $this->content->text .= $renderer->navigation_item($settings);
        }
        $this->content->text .= $renderer->content_end();

        return $this->content;
    }
}
