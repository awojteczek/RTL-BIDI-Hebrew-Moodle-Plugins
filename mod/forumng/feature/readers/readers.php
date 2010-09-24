<?php
require_once('../../../../config.php');
require_once($CFG->dirroot . '/mod/forumng/forum.php');

$d = required_param('d', PARAM_INT);
$groupid = optional_param('group', -1, PARAM_INT);

try {
    $discussion = forum_discussion::get_from_id($d);
    $forum = $discussion->get_forum();
    $course = $forum->get_course();
    $cm = $forum->get_course_module();
    $context = $forum->get_context();

    // Check permission for move
    $discussion->require_view();
    require_capability('mod/forumng:viewreadinfo',
        $discussion->get_forum()->get_context());

    // If discussion is grouped, use that group
    $groups = null;
    if ($discussion->get_group_id()) {
        // Ignore any user-specified group
        $groupid = $discussion->get_group_id();
        if (!$discussion->get_forum()->can_access_group($groupid, true)) {
            // They don't have this capability but it will give the right error
            require_capability('moodle/site:accessallgroups',$context);
        }
    } else {
        if ($course->groupmode) {
            // Get groups. Because the logic for this is slightly weird (you need
            // write access to see any other group; we're using course groups
            // regardless of activity group setting; etc) it is not possible to
            // use the standard functions.
            $aag = has_capability('moodle/site:accessallgroups',$context);
            $groups = groups_get_all_groups($course->id, $aag ? 0 : $USER->id,
                $course->defaultgroupingid);
            if (!$groups) {
                $groups = array();
            }
            foreach ($groups as $id=>$values) {
                $groups[$id] = $values->name;
            }
            if ($groupid == -1 && count($groups)>0) {
                reset($groups);
                $groupid = key($groups);
            }
            if ($groupid != 0) {
                if(!array_key_exists($groupid, $groups)) {
                    print_error('groupunknown');
                }
            } else {
                // Must have AAG to view all users
                require_capability('moodle/site:accessallgroups',$context);
            }
        } else {
            // No groups in use at all
            $groupid = 0;
        }
    }

    // Print page header
    $pagename = get_string('readersof', 'forumng');

    $navigation = array();
    $navigation[] = array(
        'name'=>shorten_text(htmlspecialchars(
            $discussion->get_subject())),
        'link'=>$discussion->get_url(), 'type'=>'forumng');
    $navigation[] = array(
        'name'=>$pagename, 'type'=>'forumng');

    $PAGEWILLCALLSKIPMAINDESTINATION = true;
    print_header_simple(format_string($forum->get_name()) . ': ' .
        $discussion->get_subject() . ': ' . $pagename,
        "", build_navigation($navigation, $cm), "", "", true,
        '', navmenu($course, $cm));

    print skip_main_destination();

    // If $groups is not null, print a group selector dropdown
    if ($groups) {
        if ($aag) {
            $groups[0] = get_string('allparticipants');
        }
        print '<form id="forumng-groupselector" method="get" ' .
            'action="readers.php"><div><input type="hidden" name="d" value="'.
            $d . '" /><label for="menugroup">' . get_string('group') .
            '</label> ';
        choose_from_menu($groups, 'group', $groupid, '');
        print '<input type="submit" value="' . get_string('set', 'forumng') .'" />' .
            '</div></form>';
    }

    // Show intro to table
    print '<div class="forumng-readersinfo"><p>' .
        get_string('readersinfo', 'forumng') . '</p><p>' .
        get_string('readersinfo2', 'forumng') . '</p></div>';

    // Get list of all users who viewed the discussion, ordered by date
    $readers = $discussion->get_readers($groupid ? $groupid : forum::ALL_GROUPS);

    $table = new stdClass;
    $table->head = array(get_string('time'), get_string('user'));
    if ($CFG->forumng_showusername) {
        $table->head[] = get_string('username');
    }
    if ($CFG->forumng_showidnumber) {
        $table->head[] = get_string('idnumber');
    }
    $table->data = array();

    foreach($readers as $reader) {
        $row = array();
        $row[] = userdate($reader->time);
        $row[] = $forum->display_user_link($reader->user);
        if ($CFG->forumng_showusername) {
            $row[] = htmlspecialchars($reader->user->username);
        }
        if ($CFG->forumng_showidnumber) {
            $row[] = htmlspecialchars($reader->user->idnumber);
        }
        $table->data[] = $row;
    }

    print_table($table);

    print link_arrow_left($discussion->get_subject(),
        '../../discuss.php?d=' . $d);
} catch(forum_exception $e) {
    forum_utils::handle_exception($e);
}
?>