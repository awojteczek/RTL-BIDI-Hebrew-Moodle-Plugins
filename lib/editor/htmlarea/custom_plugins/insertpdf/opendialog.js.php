<?php
/**
 * Created by Nadav Kavalerchik.
 * Contact info: nadavkav@gmail.com
 * Date: 1/15/11 Time: 9:32 PM
 *
 * Description:
 *  Insert an EMBED of a PDF document into the HTML
 */

  require_once("../../../../../config.php");

  $courseid = optional_param('id', SITEID, PARAM_INT);

?>

function __insertpdf (editor) {

    nbDialog("<?php
    if(true or !empty($courseid) and has_capability('moodle/course:managefiles', get_context_instance(CONTEXT_COURSE, $courseid)) ) {
        echo $CFG->wwwroot."/lib/editor/htmlarea/custom_plugins/insertpdf/dialog.php?id=$courseid";
    } else {
        //echo "insert_swf.php?id=$id";
    }?>" ,750,560, function (param) {

        if (!param) {   // user must have pressed Cancel
            return false;
        }
        if (!img) {
            var sel = editor._getSelection();
            var range = editor._createRange(sel);
                if (HTMLArea.is_ie) {
                editor._doc.execCommand("insertimage", false, param.f_url);
                }
            if (HTMLArea.is_ie) {
                img = range.parentElement();
                // wonder if this works...
                if (img.tagName.toLowerCase() != "img") {
                    img = img.previousSibling;
                }
            } else {
                // MOODLE HACK: startContainer.perviousSibling
                // Doesn't work so we'll use createElement and
                // insertNodeAtSelection
                //img = range.startContainer.previousSibling;
                var img = editor._doc.createElement("embed"); 

                img.setAttribute("src",""+ param.f_url +"");
                img.setAttribute("alt",""+ param.f_alt +"");
                img.setAttribute("width",""+ param.f_width +"");
                img.setAttribute("height",""+ param.f_height +"");
                editor.insertNodeAtSelection(img);
            }
        } else {
            img.src = param.f_url;
        }
        for (field in param) {
            var value = param[field];
            switch (field) {
                case "f_alt"    : img.alt    = value; img.title = value; break;
                case "f_border" : img.border = parseInt(value || "0"); break;
                case "f_align"  : img.align  = value; break;
                case "f_vert"   : img.vspace = parseInt(value || "0"); break;
                case "f_horiz"  : img.hspace = parseInt(value || "0"); break;
                case "f_width"  :
                    if(value != 0) {
                        img.width = parseInt(value);
                    } else {
                        break;
                    }
                    break;
                case "f_height"  :
                    if(value != 0) {
                        img.height = parseInt(value);
                    } else {
                        break;
                    }
                    break;
            }
        }
    });
}