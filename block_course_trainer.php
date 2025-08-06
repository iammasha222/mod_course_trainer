<?php


/**
 * Course trainer block
 */

defined('MOODLE_INTERNAL') || die();

class block_course_trainer extends block_base {

    /**
     * Initialises this block instance. 
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_course_trainer');
    }

    /**
     * Returns the content for this block.
     *
     * @return stdClass
     */
    public function get_content() {

        global $COURSE, $CFG, $DB; // COURSE for current course, 
                                   // CFG for global Moodle settings, 
                                   // DB for global object for database connection

        if ($this->content !== null) {
            return $this->content;
        }
        // lib with functions(for users)
        require_once($CFG->dirroot . '/user/lib.php');

        $this->content = new stdClass();
        $this->content->text = ''; // the main content of the block is text
        $this->content->footer = ''; // empty footer

        // get course content 
        $context = context_course::instance($COURSE->id);
        // get list of required shortname roles
        $targetroles = ['teacher'];

        // get id roles with shortname(SQL)
        list($in_sql, $params) = $DB->get_in_or_equal($targetroles);

        // get from table "role" all data with required shortname
        $roles = $DB->get_records_select('role', "shortname $in_sql", $params);


        // than there are no required roles in the table - show nothingtodisplay-message
        if (empty($roles)) {
            $this->content->text = get_string('nothingtodisplay', 'block_course_trainer');
            return $this->content;
        }

        // user's array
        $users = [];

        // collect ALL users with this role and save it in array $users
        foreach ($roles as $role) {
            foreach (get_role_users($role->id, $context, true) as $user) {
                $users[$user->id] = $user;
            }
        }

        // output
        $renderer = $this->page->get_renderer('block_course_trainer');
        $this->content->text = $renderer->render_trainer_card($users, $COURSE->id);

        return $this->content;
    }

    /**
     * Where this block can be added.
     *
     * @return array
     */
    public function applicable_formats() {
        return array('all' => true);
    }

    /**
     * Whether multiple instance of this block can be added to a page.
     *
     * @return bool
     */
    public function instance_allow_multiple()
    {
        return false;
    }

} 
 