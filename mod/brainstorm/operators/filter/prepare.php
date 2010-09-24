<?PHP  // $Id: categorise.php,v 1.1 2004/08/24 16:40:57 cmcclean Exp $

/**
* Module Brainstorm V2
* Operator : filter
* @author Valery Fremaux
* @package Brainstorm
* @date 20/12/2007
*/
include_once("$CFG->dirroot/mod/brainstorm/operators/operator.class.php");

if (!brainstorm_legal_include()){
    error("The way you loaded this page is not the way this script is done for.");
}

$responses = brainstorm_get_responses($brainstorm->id, 0, 0);
$currentoperator = new BrainstormOperator($brainstorm->id, $page);
$usehtmleditor = can_use_html_editor();

if (!isset($currentoperator->configdata->maxideasleft)){
    $currentoperator->configdata->maxideasleft = count($responses);
}
if (!isset($currentoperator->configdata->requirement)){
    $currentoperator->configdata->requirement = '';
}
if (!isset($currentoperator->configdata->allowreducesource)){
    $currentoperator->configdata->allowreducesource = 0;
}
if (!isset($currentoperator->configdata->candeletemore)){
    $currentoperator->configdata->candeletemore = 0;
}

$noselected = (!$currentoperator->configdata->allowreducesource) ? 'checked="checked"' : '' ;
$yesselected = ($currentoperator->configdata->allowreducesource) ? 'checked="checked"' : '' ;
$noselected1 = (!$currentoperator->configdata->candeletemore) ? 'checked="checked"' : '' ;
$yesselected1 = ($currentoperator->configdata->candeletemore) ? 'checked="checked"' : '' ;

print_heading(get_string("{$page}settings", 'brainstorm'));
?>
<center>
<img src="<?php echo "$CFG->wwwroot/mod/brainstorm/operators/{$page}/pix/enabled.gif" ?>" align="left" />

<form name="addform" method="post" action="view.php">
<input type="hidden" name="id" value="<?php p($cm->id) ?>" />
<input type="hidden" name="operator" value="<?php echo $page ?>" />
<input type="hidden" name="what" value="saveconfig" />
<table width="80%" cellspacing="10">
    <tr valign="top">
        <td align="left"><b><?php print_string('requirement', 'brainstorm') ?>:</b></td>
        <td align="right">
			<?php
			if ($brainstorm->oprequirementtype == 0){
			?>
            <input type="text" size="60" name="config_requirement" value="<?php echo stripslashes($currentoperator->configdata->requirement); ?>" />
			<?php
			}
			elseif ($brainstorm->oprequirementtype == 2){
				print_textarea($usehtmleditor, 20, 50, 680, 400, 'config_requirement', stripslashes($currentoperator->configdata->requirement));
				if (!$usehtmleditor){
					$brainstorm->oprequirementtype = 1;
				}
				else{
					$htmleditorneeded = true;
				}
			}
			elseif ($brainstorm->oprequirementtype == 1){
			?>
            <textarea style="width:100%;height:150px" name="config_requirement"><?php echo stripslashes($currentoperator->configdata->requirement); ?></textarea>
			<?php
			}
			?>
            <?php helpbutton('requirement', get_string('requirement', 'brainstorm'), 'brainstorm'); ?>
        </td>
    </tr>
    <tr>
        <td align="left" width="50%"><b><?php print_string('maxideasleft', 'brainstorm') ?>:</b></td>
        <td align="right" width="50%">
            <?php
            for($i = 1 ; $i <= count($responses) ; $i++){
                $maxitems_options[$i] = $i;
            }
            choose_from_menu($maxitems_options, 'config_maxideasleft', $currentoperator->configdata->maxideasleft);
            helpbutton('operatorrouter.html&amp;operator=filter&amp;helpitem=maxideasleft', get_string('maxideasleft', 'brainstorm'), 'brainstorm');
            ?>
        </td>
    </tr>
    <tr>
        <td align="left" width="50%"><b><?php print_string('candeletemore', 'brainstorm') ?>:</b></td>
        <td align="right" width="50%">
            <input type="radio" name="config_candeletemore" value="0" <?php echo $noselected1 ?> /> <?php print_string('no') ?>&nbsp;-&nbsp;
            <input type="radio" name="config_candeletemore" value="1" <?php echo $yesselected1 ?> /> <?php print_string('yes') ?>
            <?php helpbutton('operatorrouter.html&amp;operator=filter&amp;helpitem=candeletemore', get_string('candeletemore', 'brainstorm'), 'brainstorm'); ?>
        </td>
    </tr>
    <tr>
        <td align="left" width="50%"><b><?php print_string('allowreducesource', 'brainstorm') ?>:</b></td>
        <td align="right" width="50%">
            <input type="radio" name="config_allowreducesource" value="0" <?php echo $noselected ?> /> <?php print_string('no') ?>&nbsp;-&nbsp;
            <input type="radio" name="config_allowreducesource" value="1" <?php echo $yesselected ?> /> <?php print_string('yes') ?>
            <?php helpbutton('operatorrouter.html&amp;operator=filter&amp;helpitem=allowreducesource', get_string('allowreducesource', 'brainstorm'), 'brainstorm'); ?>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <br/><input type="submit" name="go_btn" value="<?php print_string('saveconfig', 'brainstorm') ?>" />
        </td>
    </tr>
</table>
</form>
</center>