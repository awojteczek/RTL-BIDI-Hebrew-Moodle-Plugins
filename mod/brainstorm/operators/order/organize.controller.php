<?php

/**
* Module Brainstorm V2
* Operator : locate
* @author Valery Fremaux
* @package Brainstorm
* @date 20/12/2007
*/
include_once("$CFG->dirroot/mod/brainstorm/operators/operator.class.php");

/********************************** Saves ordering ********************************/
if ($action == 'saveorder'){
    // first delete all old ordering data - the fastest way to do it
    if (!delete_records('brainstorm_operatordata', 'brainstormid', $brainstorm->id, 'userid', $USER->id, 'operatorid', 'order')){
        // NOT AN ERROR : there was nothing here
    }

    $keys = preg_grep("/^order_/", array_keys($_POST));
    if ($keys){
        $orderrecord->brainstormid = $brainstorm->id;
        $orderrecord->operatorid = 'order';
        $orderrecord->userid = $USER->id;
        $orderrecord->groupid = $currentgroup;
        $orderrecord->timemodified = time();
        foreach($keys as $key){
            preg_match("/^order_(.*)/", $key, $matches);
            $orderrecord->intvalue = $matches[1];
            $orderrecord->itemsource = required_param($key, PARAM_INT);
            if (!insert_record('brainstorm_operatordata', $orderrecord)){
                error("Could not insert order record");
            }
        }
    }
}
if ($action == 'clearall'){
    // delete all old ordering data
    if (!delete_records('brainstorm_operatordata', 'brainstormid', $brainstorm->id, 'userid', $USER->id, 'operatorid', 'order')){
        // NOT AN ERROR : there was nothing to clear here
    }
}
$result = include "{$CFG->dirroot}/mod/brainstorm/operators/paircompare.controller.php";
if ($result == -1) return $result;
/*********************************** Resuming pair compare procedure ************************/
// this use case is specific to order operator as we need producing a valid operator data set for ordering
if ($action == 'resumepaircompare'){
    $responses = brainstorm_get_responses($brainstorm->id, 0, $currentgroup, false);

    if (@$processedfinished){
        print_simple_box(get_string('finished', 'brainstorm'));
    }

    // get ordered
    $sql = "
        SELECT
            r.id,
            r.response,
            od.intvalue,
            od.id as odid,
            od.operatorid
        FROM
            {$CFG->prefix}brainstorm_responses as r
        LEFT JOIN
            {$CFG->prefix}brainstorm_operatordata as od
        ON
            r.id = od.itemsource AND
            od.operatorid = 'order' AND
            od.userid = {$USER->id}
        WHERE
            r.brainstormid = {$brainstorm->id}
        ORDER BY
            od.intvalue DESC
    ";
    $ordered = get_records_sql($sql);
    if ($ordered){
        $table->head = array(get_string('response', 'brainstorm'), get_string('rank', 'brainstorm'));
        $table->size = array('80%', '20%');
        $table->align = array('right', 'center');
        foreach($ordered as $response){
            $table->data[] = array($response->response, $response->intvalue);
        }
        print_table($table);

        print_string('reordering...', 'brainstorm');

        /// reordering
        $i = 0;
        foreach($ordered as $response){
            if (!empty($response->operatorid)){ // if was ordered within the procedure, just update order
                unset($record);
                $record->id = $response->odid;
                $record->intvalue = $i;
                if (!update_record('brainstorm_operatordata', $record)){
                    error("Could not update reordered record");
                }
            }
            else{ // mark order in new record
                $orderrecord->userid = $USER->id;
                $orderrecord->groupid = $currentgroup;
                $orderrecord->operatorid = 'order';
                $orderrecord->brainstormid = $brainstorm->id;
                $orderrecord->timemodified = time();
                $orderrecord->itemsource = $response->id;
                $orderrecord->intvalue = $i;
                if (!insert_record('brainstorm_operatordata', $orderrecord)){
                    error("Could not insert reordered record");
                }
            }
            $i++;
        }
    }

    /// clean up database of temp records
    $select = "
       brainstormid = {$brainstorm->id} AND
       operatorid = 'order' AND
       userid = {$USER->id} AND
       itemsource = 0
    ";
    delete_records_select('brainstorm_operatordata', $select);

    /// print final continue button
    print_continue("{$CFG->wwwroot}/mod/brainstorm/view.php?id={$cm->id}&amp;operator={$page}");

    return -1;
}

?>