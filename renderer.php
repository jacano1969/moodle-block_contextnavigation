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
 * The renderer for this block
 *
 * @package block_contextnavigation
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_contextnavigation_renderer extends plugin_renderer_base {

    /**
     * An opening container for the content of this block
     * @return string
     */
    public function content_start() {
        return html_writer::start_tag('div', array('class' => 'context-navigation-content'));
    }

    /**
     * The closing container for this block
     * @return string
     */
    public function content_end() {
        return html_writer::end_tag('div');
    }

    /**
     * Displays a navigation item and its navigation node siblings
     *
     * @param navigation_node $node
     * @param string $optionaltitle
     * @return string
     */
    public function navigation_item(navigation_node $node, $optionaltitle = null) {
        $html = '';
        if ($optionaltitle) {
            $html .= $this->output->heading($optionaltitle, 3);
        }

        if ($node->parent) {
            // A normal navigation node
            $parent = $node->parent;
            $parentstorender = array();
            while ($parent) {
                if (!empty($parent->action)) {
                    array_unshift($parentstorender, $parent);
                    if ($parent->parent && $parent->type > navigation_node::TYPE_COURSE && count($parentstorender) < 4) {
                        $parent = $parent->parent;
                    } else {
                        $parent = false;
                    }
                } else {
                    $parent = $parent->parent;
                }
            }

            if ($node->parent->children->count() == 1 && $node->children->count() > 0) {
                $parentstorender[] = $node;
                $node = $node->children->last();
            }

            if (count($parentstorender)) {
                $html .= html_writer::start_tag('ul', array('class' => 'navigation-item-list navigation-parents'));
                foreach ($parentstorender as $parent) {
                    $html .= $this->render_navigation_node($parent);
                }
                $html .= html_writer::end_tag('ul');
            }

            $html .= html_writer::start_tag('ul', array('class' => 'navigation-item-list'));
            foreach ($node->parent->children as $child) {
                if (!empty($child->action)) {
                    $html .= $this->render($child);
                }
            }
            $html .= html_writer::end_tag('ul');
        } else {
            // This is the global navigation node
            $html .= html_writer::start_tag('ul', array('class' => 'navigation-item-list'));
            $html .= $this->render_navigation_node($node);
            $html .= html_writer::end_tag('ul');

            $html .= html_writer::start_tag('ul', array('class' => 'navigation-item-list'));
            foreach ($node->children as $child) {
                if (!empty($child->action)) {
                    $html .= $this->render($child);
                } else if ($child->get('participants')) {
                    foreach ($child->children as $n) {
                        if (!empty($n->action)) {
                            $html .= $this->render($n);
                        }
                    }
                }
            }
            $html .= html_writer::end_tag('ul');
        }
        return $html;
    }

    /**
     * Renders a navigation_node object
     *
     * @param navigation_node $node
     * @return string
     */
    public function render_navigation_node(navigation_node $node) {
        if (!$node->display) {
            return '';
        }


        $linkattributes = array();
        $linkattributes['class'] = array('navigation-item-link');
        $divattributes = array();
        $divattributes['class'] = array('navigation-item');

        $content = $node->get_content();

        if ($content == '') {
            return '';
        }

        if ($node->icon instanceof pix_icon && empty($node->hideicon)) {
            $content = $this->output->render($node->icon).$content;
        }
        if ($node->get_title() != '') {
            $linkattributes['title'] = $node->get_title();
        }
        if ($node->hidden) {
            $linkattributes['class'][] = 'dimmed_text';
        }
        if ($node->helpbutton !== null) {
            $content = trim($node->helpbutton).html_writer::tag('span', $content, array('class'=>'clearhelpbutton'));
            $divattributes['class'][] = 'hasicon';
        }
        if ($node->isactive === true) {
            $divattributes['class'][] = 'active-page';
        }

        $linkattributes['class'] = join(' ', $linkattributes['class']);
        $divattributes['class'] = join(' ', $divattributes['class']);
        if ($node->action instanceof action_link) {
            $link = $node->action;
            $link->text = $content;
            $link->attributes = array_merge($link->attributes, $linkattributes);
            $content = $this->output->render($link);
        } else if ($node->action instanceof moodle_url || is_string($node->action)) {
            $content = html_writer::link($node->action, $content, $linkattributes);
        }

        return html_writer::tag('li', $content, $divattributes);
    }
}
