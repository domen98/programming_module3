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
 * Definition of log events
 *
 * @package   mod_prog
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$logs = array(
    array('module'=>'prog', 'action'=>'add', 'mtable'=>'prog', 'field'=>'name'),
    array('module'=>'prog', 'action'=>'delete mod', 'mtable'=>'prog', 'field'=>'name'),
    array('module'=>'prog', 'action'=>'download all submissions', 'mtable'=>'prog', 'field'=>'name'),
    array('module'=>'prog', 'action'=>'grade submission', 'mtable'=>'prog', 'field'=>'name'),
    array('module'=>'prog', 'action'=>'lock submission', 'mtable'=>'prog', 'field'=>'name'),
    array('module'=>'prog', 'action'=>'reveal identities', 'mtable'=>'prog', 'field'=>'name'),
    array('module'=>'prog', 'action'=>'revert submission to draft', 'mtable'=>'prog', 'field'=>'name'),
    array('module'=>'prog', 'action'=>'set marking workflow state', 'mtable'=>'prog', 'field'=>'name'),
    array('module'=>'prog', 'action'=>'submission statement accepted', 'mtable'=>'prog', 'field'=>'name'),
    array('module'=>'prog', 'action'=>'submit', 'mtable'=>'prog', 'field'=>'name'),
    array('module'=>'prog', 'action'=>'submit for grading', 'mtable'=>'prog', 'field'=>'name'),
    array('module'=>'prog', 'action'=>'unlock submission', 'mtable'=>'prog', 'field'=>'name'),
    array('module'=>'prog', 'action'=>'update', 'mtable'=>'prog', 'field'=>'name'),
    array('module'=>'prog', 'action'=>'upload', 'mtable'=>'prog', 'field'=>'name'),
    array('module'=>'prog', 'action'=>'view', 'mtable'=>'prog', 'field'=>'name'),
    array('module'=>'prog', 'action'=>'view all', 'mtable'=>'course', 'field'=>'fullname'),
    array('module'=>'prog', 'action'=>'view confirm submit assignment form', 'mtable'=>'prog', 'field'=>'name'),
    array('module'=>'prog', 'action'=>'view grading form', 'mtable'=>'prog', 'field'=>'name'),
    array('module'=>'prog', 'action'=>'view submission', 'mtable'=>'prog', 'field'=>'name'),
    array('module'=>'prog', 'action'=>'view submission grading table', 'mtable'=>'prog', 'field'=>'name'),
    array('module'=>'prog', 'action'=>'view submit assignment form', 'mtable'=>'prog', 'field'=>'name'),
    array('module'=>'prog', 'action'=>'view feedback', 'mtable'=>'prog', 'field'=>'name'),
    array('module'=>'prog', 'action'=>'view batch set marking workflow state', 'mtable'=>'prog', 'field'=>'name'),
);
