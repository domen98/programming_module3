<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * The mod_prog assignment batch set workflow stated viewed event.
 *
 * @package    mod_prog
 * @copyright  2014 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_prog\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The mod_prog assignment batch set workflow stated viewed event.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - int progid: the id of the assignment.
 * }
 *
 * @package    mod_prog
 * @since      Moodle 2.7
 * @copyright  2014 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class batch_set_workflow_state_viewed extends base {
    /**
     * Flag for prevention of direct create() call.
     * @var bool
     */
    protected static $preventcreatecall = true;

    /**
     * Create instance of event.
     *
     * @param \prog $prog
     * @return batch_set_workflow_state_viewed
     */
    public static function create_from_prog(\prog $prog) {
        $data = array(
            'context' => $prog->get_context(),
            'other' => array(
                'progid' => $prog->get_instance()->id,
            ),
        );
        self::$preventcreatecall = false;
        /** @var batch_set_workflow_state_viewed $event */
        $event = self::create($data);
        self::$preventcreatecall = true;
        $event->set_prog($prog);
        return $event;
    }

    /**
     * Init method.
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventbatchsetworkflowstateviewed', 'mod_prog');
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' viewed the batch set workflow for the assignment with course " .
            "module id '$this->contextinstanceid'.";
    }

    /**
     * Return legacy data for add_to_log().
     *
     * @return array
     */
    protected function get_legacy_logdata() {
        $logmessage = get_string('viewbatchsetmarkingworkflowstate', 'prog');
        $this->set_legacy_logdata('view batch set marking workflow state', $logmessage);
        return parent::get_legacy_logdata();
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        if (self::$preventcreatecall) {
            throw new \coding_exception('cannot call batch_set_workflow_state_viewed::create() directly, use batch_set_workflow_state_viewed::create_from_prog() instead.');
        }

        parent::validate_data();

        if (!isset($this->other['progid'])) {
            throw new \coding_exception('The \'progid\' value must be set in other.');
        }
    }

    public static function get_other_mapping() {
        $othermapped = array();
        $othermapped['progid'] = array('db' => 'prog', 'restore' => 'prog');

        return $othermapped;
    }
}
