<?PHP  // $Id: categorise.php,v 1.1 2004/08/24 16:40:57 cmcclean Exp $

/**
* Module Brainstorm V2
* Operator : order
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

if (!isset($currentoperator->configdata->absolute)){
    $currentoperator->configdata->absolute = 1;
}
if (!isset($currentoperator->configdata->blindness)){
    $currentoperator->configdata->blindness = $brainstorm->privacy;
}
if (!isset($currentoperator->configdata->requirement)){
    $currentoperator->configdata->requirement = '';
}

$noselected0 = (!$currentoperator->configdata->absolute) ? 'checked="checked"' : '' ;
$yesselected0 = ($currentoperator->configdata->absolute) ? 'checked="checked"' : '' ;
$noselected1 = (!$currentoperator->configdata->blindness) ? 'checked="checked"' : '' ;
$yesselected1 = ($currentoperator->configdata->blindness) ? 'checked="checked"' : '' ;

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
        <td align="left" width="20%"><b><?php print_string('requirement', 'brainstorm') ?>:</b></td>
        <td align="right" width="80%">
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
        <td align="left" width="20%"><b><?php print_string('absolute', 'brainstorm') ?>:</b></td>
        <td align="right" width="80%">
            <input type="radio" name="config_absolute" value="0" <?php echo $noselected0 ?> /> <?php print_string('no') ?>&nbsp;-&nbsp;
            <input type="radio" name="config_absolute" value="1" <?php echo $yesselected0 ?> /> <?php print_string('yes') ?>
            <?php helpbutton('operatorrouter.html&amp;operator=order&amp;helpitem=absolute', get_string('absolute', 'brainstorm'), 'brainstorm'); ?>
        </td>
    </tr>
</table>
<?php
if (has_capability('mod/brainstorm:manage', $context)){
?>
<fieldset class="privateform">
<legend><?php print_string('foradminsonly', 'brainstorm') ?></legend>
<table width="60%" cellspacing="5">
    <tr>
        <td align="left" width="50%"><b><?php print_string('blindness', 'brainstorm') ?>:</b></td>
        <td align="right" width="50%">
            <input type="radio" name="config_blindness" value="0" <?php echo $noselected1 ?> /> <?php print_string('no') ?>&nbsp;-&nbsp;
            <input type="radio" name="config_blindness" value="1" <?php echo $yesselected1 ?> /> <?php print_string('yes') ?>
            <?php helpbutton('blindness', get_string('blindness', 'brainstorm'), 'brainstorm'); ?>
        </td>
    </tr>
</table>
</fieldset>
<?php
}
else{
// not very secure, but might be enough for the security context we are in
?>
<input type="hidden" name="config_blindness" value="<?php echo $currentoperator->configdata->blindness ?>" />
<?php
}
?>
<table width="80%" cellspacing="5">
    <tr>
        <td colspan="2">
            <br/><input type="submit" name="go_btn" value="<?php print_string('saveconfig', 'brainstorm') ?>" />
        </td>
    </tr>
</table>
</form>
</center>