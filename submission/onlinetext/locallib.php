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
 * This file contains the definition for the library class for onlinetext submission plugin
 *
 * This class provides all the functionality for the new prog module.
 *
 * @package progsubmission_onlinetext
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
// File area for online text submission assignment.
define('PROGSUBMISSION_ONLINETEXT_FILEAREA', 'submissions_onlinetext');

/**
 * library class for onlinetext submission plugin extending submission plugin base class
 *
 * @package progsubmission_onlinetext
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class prog_submission_onlinetext extends prog_submission_plugin {

    /**
     * Get the name of the online text submission plugin
     * @return string
     */
    public function get_name() {
        return get_string('onlinetext', 'progsubmission_onlinetext');
    }


    /**
     * Get onlinetext submission information from the database
     *
     * @param  int $submissionid
     * @return mixed
     */
    private function get_onlinetext_submission($submissionid) {
        global $DB;

        return $DB->get_record('progsubmission_onlinetext', array('submission'=>$submissionid));
    }

    /**
     * Get the settings for onlinetext submission plugin
     *
     * @param MoodleQuickForm $mform The form to add elements to
     * @return void
     */
    public function get_settings(MoodleQuickForm $mform) {
        global $CFG, $COURSE;

        $defaultwordlimit = $this->get_config('wordlimit') == 0 ? '' : $this->get_config('wordlimit');
        $defaultwordlimitenabled = $this->get_config('wordlimitenabled');

        $options = array('size' => '6', 'maxlength' => '6');
        $name = get_string('wordlimit', 'progsubmission_onlinetext');

        // Create a text box that can be enabled/disabled for onlinetext word limit.
        $wordlimitgrp = array();
        $wordlimitgrp[] = $mform->createElement('text', 'progsubmission_onlinetext_wordlimit', '', $options);
        $wordlimitgrp[] = $mform->createElement('checkbox', 'progsubmission_onlinetext_wordlimit_enabled',
                '', get_string('enable'));
        $mform->addGroup($wordlimitgrp, 'progsubmission_onlinetext_wordlimit_group', $name, ' ', false);
        $mform->addHelpButton('progsubmission_onlinetext_wordlimit_group',
                              'wordlimit',
                              'progsubmission_onlinetext');
        $mform->disabledIf('progsubmission_onlinetext_wordlimit',
                           'progsubmission_onlinetext_wordlimit_enabled',
                           'notchecked');

        // Add numeric rule to text field.
        $wordlimitgrprules = array();
        $wordlimitgrprules['progsubmission_onlinetext_wordlimit'][] = array(null, 'numeric', null, 'client');
        $mform->addGroupRule('progsubmission_onlinetext_wordlimit_group', $wordlimitgrprules);

        // Rest of group setup.
        $mform->setDefault('progsubmission_onlinetext_wordlimit', $defaultwordlimit);
        $mform->setDefault('progsubmission_onlinetext_wordlimit_enabled', $defaultwordlimitenabled);
        $mform->setType('progsubmission_onlinetext_wordlimit', PARAM_INT);
        $mform->disabledIf('progsubmission_onlinetext_wordlimit_group',
                           'progsubmission_onlinetext_enabled',
                           'notchecked');
    }

    /**
     * Save the settings for onlinetext submission plugin
     *
     * @param stdClass $data
     * @return bool
     */
    public function save_settings(stdClass $data) {
        if (empty($data->progsubmission_onlinetext_wordlimit) || empty($data->progsubmission_onlinetext_wordlimit_enabled)) {
            $wordlimit = 0;
            $wordlimitenabled = 0;
        } else {
            $wordlimit = $data->progsubmission_onlinetext_wordlimit;
            $wordlimitenabled = 1;
        }

        $this->set_config('wordlimit', $wordlimit);
        $this->set_config('wordlimitenabled', $wordlimitenabled);

        return true;
    }

    /**
     * Add form elements for settings
     *
     * @param mixed $submission can be null
     * @param MoodleQuickForm $mform
     * @param stdClass $data
     * @return true if elements were added to the form
     */
    public function get_form_elements($submission, MoodleQuickForm $mform, stdClass $data) {
        $elements = array();

        $editoroptions = $this->get_edit_options();
        $submissionid = $submission ? $submission->id : 0;

        if (!isset($data->onlinetext)) {
            $data->onlinetext = '';
        }
        if (!isset($data->onlinetextformat)) {
            $data->onlinetextformat = editors_get_preferred_format();
        }
        if ($submission) {
            $onlinetextsubmission = $this->get_onlinetext_submission($submission->id);
            if ($onlinetextsubmission) {
                $data->onlinetext = $onlinetextsubmission->onlinetext;
                $data->onlinetextformat = $onlinetextsubmission->onlineformat;
            }

        }
		
		global $CFG;
		//var_dump($submission);//die();
		$RETURN_LOC = $CFG->wwwroot.'/course/view.php?id='.$this->assignment->get_course()->id;
		$SITE_LOC = $CFG->wwwroot;
		$AUTOSAVE_TIMER = 120000;
		$editor = <<<EOD
		<script language="Javascript" type="text/javascript" src="$SITE_LOC/mod/prog/submission/onlinetext/edit_area/edit_area_full.js"></script>
		<script language="Javascript" type="text/javascript" src="$SITE_LOC/mod/prog/submission/onlinetext/php_js/php.js"></script>
		<script language="Javascript" type="text/javascript">
				editAreaLoader.init({
					id: "id_text_editarea"
					,start_highlight: true
					,allow_resize: "both"
					,allow_toggle: false
					,language: "en"
					,syntax: "html"	
					,toolbar: "search, go_to_line, |, undo, redo, |, syntax_selection, |, help"
					,syntax_selection_allow: "python,java,pas,c,cpp,csharp,vb"
					,is_multi_files: false
					,replace_tab_by_spaces: 4
					,show_line_colors: true
				});
		
		function saveAndCommit(){
			if (savingMutex) {
				return;
			}
			
			savingMutex=1;
			trySaveNum++;
			disableButtons();
			clearTimeout(autoSaveTimer);
			
			var http = new XMLHttpRequest();
			var url = "$SITE_LOC/mod/prog/view.php";
			var submittedText = editAreaLoader.getValue("id_text_editarea");
			var params = "";
			params += "_qf__mod_prog_submission_form="+document.getElementsByName("_qf__mod_prog_submission_form")[0].value;
			params += "&action=savesubmission";
			params += "&id="+document.getElementsByName("id")[0].value;
			params += "&onlinetext_editor[text]=" + encodeURIComponent(submittedText);
			params += "&sesskey="+document.getElementsByName("sesskey")[0].value;
			params += "&submitbutton="+encodeURIComponent(document.getElementById("id_submitbutton").value);
			params += "&userid="+document.getElementsByName("userid")[0].value;
			
			http.open("POST", url, true);
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.setRequestHeader("Content-length", params.length);
			http.setRequestHeader("Connection", "close");
			
			http.onreadystatechange = function() {
				if(http.readyState == 4) {
					if (http.status == 200) {
						savingMutex=0;
						trySaveNum=0;
						autoSaveTimer = setTimeout("saveAndCommit()", $AUTOSAVE_TIMER);
						
/*						domParser = new DOMParser();
						savedDoc = domParser.parseFromString(http.responseText, "text/html");
						savedSubmission = savedDoc.getElementsByClassName("no-overflow")[1].innerHTML; // submission status and content table
						if (savedSubmission != submittedText) {
							alert("Napaka pri shranjevanju.\\nVerzija na strežniku:\\n"+savedSubmission+"\\nPoslana verzija:\\n"+submittedText);
						}*/
					} else if (http.status!=0) {
						alert("Napaka pri shranjevanju. http.readyState = "+http.readyState+", http.status="+http.status);
					}
				}
				enableButtons();
			}
			
			http.send(params);
		}
		
		function disableButtons() {
			document.getElementById("save_btn").disabled = "disabled";
			document.getElementById("save_btn").value = "Shranjevanje...";
			document.getElementById("save_and_close_btn").style.pointerEvents = "none";
		}
		
		function enableButtons() {
			document.getElementById("save_btn").disabled = "";
			document.getElementById("save_btn").value = saveBtnLabel;
			document.getElementById("save_and_close_btn").style.pointerEvents = "";
		}
		
		</script>


		<br /><input type="button" id="save_btn" value="Shrani spremembe" style="margin-left: 20em;" onClick="disableButtons(); document.getElementById('id_submitbutton').click();" />
		<a id="save_and_close_btn" href="$RETURN_LOC" onClick="saveAndCommit();" style="margin-left: 10em;" style="">Shrani in zapri</a>
		
		<script language="Javascript" type="text/javascript">
			savingMutex=0;
			trySaveNum=0;
			autoSaveTimer = setTimeout("saveAndCommit()", $AUTOSAVE_TIMER);
			saveBtnLabel = document.getElementById("save_btn").value;
		</script>
		
EOD;
		
		
   /*     $data = file_prepare_standard_editor($data,
                                             'onlinetext',
                                             $editoroptions,
                                             $this->assignment->get_context(),
                                             'progsubmission_onlinetext',
                                             PROGSUBMISSION_ONLINETEXT_FILEAREA,
                                             $submissionid);*/
        //$mform->addElement('progarea', 'onlinetext_editor', $this->get_name(), null, $editoroptions);
		$mform->addElement('textarea', 'onlinetext_editor[text]', null, array( 'class'=>'', 'style'=>'height: 600px; width: 100%;', 'id'=>"id_text_editarea"));
		$mform->getElement('onlinetext_editor[text]')->setValue($data->onlinetext);
		$mform->addElement('html', $editor);
		//<textarea style="height: 600px; width: 100%;" id="id_text_editarea" name="onlinetext_editor[text]"></textarea>
		//<input  style="display:none" type="text" name="onlinetext_editor[format]" value="1" />

        return true;
    }

    /**
     * Editor format options
     *
     * @return array
     */
    private function get_edit_options() {
         $editoroptions = array(
           'noclean' => false,
           'maxfiles' => EDITOR_UNLIMITED_FILES,
           'maxbytes' => $this->assignment->get_course()->maxbytes,
           'context' => $this->assignment->get_context(),
           'return_types' => FILE_INTERNAL | FILE_EXTERNAL
        );
        return $editoroptions;
    }

    /**
     * Save data to the database and trigger plagiarism plugin,
     * if enabled, to scan the uploaded content via events trigger
     *
     * @param stdClass $submission
     * @param stdClass $data
     * @return bool
     */
    public function save(stdClass $submission, stdClass $data) {
        global $USER, $DB;

        $editoroptions = $this->get_edit_options();
		
        $data = file_postupdate_standard_editor($data,
                                                'onlinetext',
                                                $editoroptions,
                                                $this->assignment->get_context(),
                                                'progsubmission_onlinetext',
                                                PROGSUBMISSION_ONLINETEXT_FILEAREA,
                                                $submission->id);

        $onlinetextsubmission = $this->get_onlinetext_submission($submission->id);

        $fs = get_file_storage();

        $files = $fs->get_area_files($this->assignment->get_context()->id,
                                     'progsubmission_onlinetext',
                                     PROGSUBMISSION_ONLINETEXT_FILEAREA,
                                     $submission->id,
                                     'id',
                                     false);

        // Check word count before submitting anything.
        $exceeded = $this->check_word_count(trim($data->onlinetext));
        if ($exceeded) {
            $this->set_error($exceeded);
            return false;
        }
		
        $params = array(
            'context' => context_module::instance($this->assignment->get_course_module()->id),
            'courseid' => $this->assignment->get_course()->id,
            'objectid' => $submission->id,
            'other' => array(
                'pathnamehashes' => array_keys($files),
                'content' => trim($data->onlinetext),
                'format' => 0 //$data->onlinetext_editor['format']
            )
        );
        if (!empty($submission->userid) && ($submission->userid != $USER->id)) {
            $params['relateduserid'] = $submission->userid;
        }
        $event = \progsubmission_onlinetext\event\assessable_uploaded::create($params);
        $event->trigger();

        $groupname = null;
        $groupid = 0;
        // Get the group name as other fields are not transcribed in the logs and this information is important.
        if (empty($submission->userid) && !empty($submission->groupid)) {
            $groupname = $DB->get_field('groups', 'name', array('id' => $submission->groupid), '*', MUST_EXIST);
            $groupid = $submission->groupid;
        } else {
            $params['relateduserid'] = $submission->userid;
        }

        $count = count_words($data->onlinetext);
		
        // Unset the objectid and other field from params for use in submission events.
        unset($params['objectid']);
        unset($params['other']);
        $params['other'] = array(
            'submissionid' => $submission->id,
            'submissionattempt' => $submission->attemptnumber,
            'submissionstatus' => $submission->status,
            'onlinetextwordcount' => $count,
            'groupid' => $groupid,
            'groupname' => $groupname
        );

        if ($onlinetextsubmission) {

            $onlinetextsubmission->onlinetext = $data->onlinetext;
            $onlinetextsubmission->onlineformat = 1; //$data->onlinetext_editor['format'];
            $params['objectid'] = $onlinetextsubmission->id;
			
            $updatestatus = $DB->update_record('progsubmission_onlinetext', $onlinetextsubmission);
            $event = \progsubmission_onlinetext\event\submission_updated::create($params);
            $event->set_prog($this->assignment);
            $event->trigger();
            return $updatestatus;
        } else {

            $onlinetextsubmission = new stdClass();
            $onlinetextsubmission->onlinetext = $data->onlinetext;
            $onlinetextsubmission->onlineformat = 1; //$data->onlinetext_editor['format'];

            $onlinetextsubmission->submission = $submission->id;
            $onlinetextsubmission->assignment = $this->assignment->get_instance()->id;
            $onlinetextsubmission->id = $DB->insert_record('progsubmission_onlinetext', $onlinetextsubmission);
            $params['objectid'] = $onlinetextsubmission->id;
            $event = \progsubmission_onlinetext\event\submission_created::create($params);
            $event->set_prog($this->assignment);
            $event->trigger();
            return $onlinetextsubmission->id > 0;
        }
    }

    /**
     * Return a list of the text fields that can be imported/exported by this plugin
     *
     * @return array An array of field names and descriptions. (name=>description, ...)
     */
    public function get_editor_fields() {
        return array('onlinetext' => get_string('pluginname', 'progsubmission_comments'));
    }

    /**
     * Get the saved text content from the editor
     *
     * @param string $name
     * @param int $submissionid
     * @return string
     */
    public function get_editor_text($name, $submissionid) {
        if ($name == 'onlinetext') {
            $onlinetextsubmission = $this->get_onlinetext_submission($submissionid);
            if ($onlinetextsubmission) {
                return $onlinetextsubmission->onlinetext;
            }
        }

        return '';
    }

    /**
     * Get the content format for the editor
     *
     * @param string $name
     * @param int $submissionid
     * @return int
     */
    public function get_editor_format($name, $submissionid) {
        if ($name == 'onlinetext') {
            $onlinetextsubmission = $this->get_onlinetext_submission($submissionid);
            if ($onlinetextsubmission) {
                return $onlinetextsubmission->onlineformat;
            }
        }

        return 0;
    }


     /**
      * Display onlinetext word count in the submission status table
      *
      * @param stdClass $submission
      * @param bool $showviewlink - If the summary has been truncated set this to true
      * @return string
      */
    public function view_summary(stdClass $submission, & $showviewlink) {
        global $CFG;

        $onlinetextsubmission = $this->get_onlinetext_submission($submission->id);
        // Always show the view link.
        $showviewlink = true;

        if ($onlinetextsubmission) {
            $text = $this->assignment->render_editor_content(PROGSUBMISSION_ONLINETEXT_FILEAREA,
                                                             $onlinetextsubmission->submission,
                                                             $this->get_type(),
                                                             'onlinetext',
                                                             'progsubmission_onlinetext');

            $shorttext = shorten_text($text, 140);
            $plagiarismlinks = '';

            if (!empty($CFG->enableplagiarism)) {
                require_once($CFG->libdir . '/plagiarismlib.php');

                $plagiarismlinks .= plagiarism_get_links(array('userid' => $submission->userid,
                    'content' => trim($text),
                    'cmid' => $this->assignment->get_course_module()->id,
                    'course' => $this->assignment->get_course()->id,
                    'assignment' => $submission->assignment));
            }
            if ($text != $shorttext) {
                $wordcount = get_string('numwords', 'progsubmission_onlinetext', count_words($text));

                return $shorttext . $plagiarismlinks . $wordcount;
            } else {
                return $shorttext . $plagiarismlinks;
            }
        }
        return '';
    }

    /**
     * Produce a list of files suitable for export that represent this submission.
     *
     * @param stdClass $submission - For this is the submission data
     * @param stdClass $user - This is the user record for this submission
     * @return array - return an array of files indexed by filename
     */
    public function get_files(stdClass $submission, stdClass $user) {
        global $DB;

        $files = array();
        $onlinetextsubmission = $this->get_onlinetext_submission($submission->id);

        if ($onlinetextsubmission) {
            $finaltext = $this->assignment->download_rewrite_pluginfile_urls($onlinetextsubmission->onlinetext, $user, $this);
            $formattedtext = format_text($finaltext,
                                         $onlinetextsubmission->onlineformat,
                                         array('context'=>$this->assignment->get_context()));
            $head = '<head><meta charset="UTF-8"></head>';
            $submissioncontent = '<!DOCTYPE html><html>' . $head . '<body>'. $formattedtext . '</body></html>';

            $filename = get_string('onlinetextfilename', 'progsubmission_onlinetext');
            $files[$filename] = array($submissioncontent);

            $fs = get_file_storage();

            $fsfiles = $fs->get_area_files($this->assignment->get_context()->id,
                                           'progsubmission_onlinetext',
                                           PROGSUBMISSION_ONLINETEXT_FILEAREA,
                                           $submission->id,
                                           'timemodified',
                                           false);

            foreach ($fsfiles as $file) {
                $files[$file->get_filename()] = $file;
            }
        }

        return $files;
    }

    /**
     * Display the saved text content from the editor in the view table
     *
     * @param stdClass $submission
     * @return string
     */
    public function view(stdClass $submission) {
        $result = '';

        $onlinetextsubmission = $this->get_onlinetext_submission($submission->id);

        if ($onlinetextsubmission) {

            // Render for portfolio API.
            $result .= $this->assignment->render_editor_content(PROGSUBMISSION_ONLINETEXT_FILEAREA,
                                                                $onlinetextsubmission->submission,
                                                                $this->get_type(),
                                                                'onlinetext',
                                                                'progsubmission_onlinetext');

        }

        return $result;
    }

    /**
     * Return true if this plugin can upgrade an old Moodle 2.2 assignment of this type and version.
     *
     * @param string $type old assignment subtype
     * @param int $version old assignment version
     * @return bool True if upgrade is possible
     */
    public function can_upgrade($type, $version) {
        if ($type == 'online' && $version >= 2011112900) {
            return true;
        }
        return false;
    }


    /**
     * Upgrade the settings from the old assignment to the new plugin based one
     *
     * @param context $oldcontext - the database for the old assignment context
     * @param stdClass $oldassignment - the database for the old assignment instance
     * @param string $log record log events here
     * @return bool Was it a success?
     */
    public function upgrade_settings(context $oldcontext, stdClass $oldassignment, & $log) {
        // No settings to upgrade.
        return true;
    }

    /**
     * Upgrade the submission from the old assignment to the new one
     *
     * @param context $oldcontext - the database for the old assignment context
     * @param stdClass $oldassignment The data record for the old assignment
     * @param stdClass $oldsubmission The data record for the old submission
     * @param stdClass $submission The data record for the new submission
     * @param string $log Record upgrade messages in the log
     * @return bool true or false - false will trigger a rollback
     */
    public function upgrade(context $oldcontext,
                            stdClass $oldassignment,
                            stdClass $oldsubmission,
                            stdClass $submission,
                            & $log) {
        global $DB;

        $onlinetextsubmission = new stdClass();
        $onlinetextsubmission->onlinetext = $oldsubmission->data1;
        $onlinetextsubmission->onlineformat = $oldsubmission->data2;

        $onlinetextsubmission->submission = $submission->id;
        $onlinetextsubmission->assignment = $this->assignment->get_instance()->id;

        if ($onlinetextsubmission->onlinetext === null) {
            $onlinetextsubmission->onlinetext = '';
        }

        if ($onlinetextsubmission->onlineformat === null) {
            $onlinetextsubmission->onlineformat = editors_get_preferred_format();
        }

        if (!$DB->insert_record('progsubmission_onlinetext', $onlinetextsubmission) > 0) {
            $log .= get_string('couldnotconvertsubmission', 'mod_prog', $submission->userid);
            return false;
        }

        // Now copy the area files.
        $this->assignment->copy_area_files_for_upgrade($oldcontext->id,
                                                        'mod_assignment',
                                                        'submission',
                                                        $oldsubmission->id,
                                                        $this->assignment->get_context()->id,
                                                        'progsubmission_onlinetext',
                                                        PROGSUBMISSION_ONLINETEXT_FILEAREA,
                                                        $submission->id);
        return true;
    }

    /**
     * Formatting for log info
     *
     * @param stdClass $submission The new submission
     * @return string
     */
    public function format_for_log(stdClass $submission) {
        // Format the info for each submission plugin (will be logged).
        $onlinetextsubmission = $this->get_onlinetext_submission($submission->id);
        $onlinetextloginfo = '';
        $onlinetextloginfo .= get_string('numwordsforlog',
                                         'progsubmission_onlinetext',
                                         count_words($onlinetextsubmission->onlinetext));

        return $onlinetextloginfo;
    }

    /**
     * The assignment has been deleted - cleanup
     *
     * @return bool
     */
    public function delete_instance() {
        global $DB;
        $DB->delete_records('progsubmission_onlinetext',
                            array('assignment'=>$this->assignment->get_instance()->id));

        return true;
    }

    /**
     * No text is set for this plugin
     *
     * @param stdClass $submission
     * @return bool
     */
    public function is_empty(stdClass $submission) {
        $onlinetextsubmission = $this->get_onlinetext_submission($submission->id);

        return empty($onlinetextsubmission->onlinetext);
    }

    /**
     * Get file areas returns a list of areas this plugin stores files
     * @return array - An array of fileareas (keys) and descriptions (values)
     */
    public function get_file_areas() {
        return array(PROGSUBMISSION_ONLINETEXT_FILEAREA=>$this->get_name());
    }

    /**
     * Copy the student's submission from a previous submission. Used when a student opts to base their resubmission
     * on the last submission.
     * @param stdClass $sourcesubmission
     * @param stdClass $destsubmission
     */
    public function copy_submission(stdClass $sourcesubmission, stdClass $destsubmission) {
        global $DB;

        // Copy the files across (attached via the text editor).
        $contextid = $this->assignment->get_context()->id;
        $fs = get_file_storage();
        $files = $fs->get_area_files($contextid, 'progsubmission_onlinetext',
                                     PROGSUBMISSION_ONLINETEXT_FILEAREA, $sourcesubmission->id, 'id', false);
        foreach ($files as $file) {
            $fieldupdates = array('itemid' => $destsubmission->id);
            $fs->create_file_from_storedfile($fieldupdates, $file);
        }

        // Copy the progsubmission_onlinetext record.
        $onlinetextsubmission = $this->get_onlinetext_submission($sourcesubmission->id);
        if ($onlinetextsubmission) {
            unset($onlinetextsubmission->id);
            $onlinetextsubmission->submission = $destsubmission->id;
            $DB->insert_record('progsubmission_onlinetext', $onlinetextsubmission);
        }
        return true;
    }

    /**
     * Return a description of external params suitable for uploading an onlinetext submission from a webservice.
     *
     * @return external_description|null
     */
    public function get_external_parameters() {
        $editorparams = array('text' => new external_value(PARAM_TEXT, 'The text for this submission.'),
                              'format' => new external_value(PARAM_INT, 'The format for this submission'));
        $editorstructure = new external_single_structure($editorparams);
        return array('onlinetext_editor' => $editorstructure);
    }

    /**
     * Compare word count of onlinetext submission to word limit, and return result.
     *
     * @param string $submissiontext Onlinetext submission text from editor
     * @return string Error message if limit is enabled and exceeded, otherwise null
     */
    public function check_word_count($submissiontext) {
        global $OUTPUT;

        $wordlimitenabled = $this->get_config('wordlimitenabled');
        $wordlimit = $this->get_config('wordlimit');

        if ($wordlimitenabled == 0) {
            return null;
        }

        // Count words and compare to limit.
        $wordcount = count_words($submissiontext);
        if ($wordcount <= $wordlimit) {
            return null;
        } else {
            $errormsg = get_string('wordlimitexceeded', 'progsubmission_onlinetext',
                    array('limit' => $wordlimit, 'count' => $wordcount));
            return $OUTPUT->error_text($errormsg);
        }
    }

}


