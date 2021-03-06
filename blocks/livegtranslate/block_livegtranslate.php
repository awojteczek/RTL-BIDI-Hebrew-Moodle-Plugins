<?php

class block_livegtranslate extends block_base {

    function init() {
        $this->title = get_string('blockname', 'block_livegtranslate');
        $this->version = 2009060601;
    }

    function applicable_formats() {
        return array('all' => true, 'tag' => false);
    }

    function specialization() {
        $this->title = isset($this->config->title) ? $this->config->title : get_string('livegtranslate', 'block_livegtranslate');
    }

    function instance_allow_multiple() {
        return false;
    }

    function get_content() {
	include('following-floating-gtranslation.inc');
     
        if ($this->content !== NULL) {
            return $this->content;
        }
                                
        $this->content->text = get_string('instructions', 'block_livegtranslate').'<br/>';
	$this->content->text .= '<input type=button value="'.get_string('hide', 'block_livegtranslate').'" onclick="document.getElementById(\'divStayTopLeft\').style[\'display\']=\'none\';">';
	$this->content->text .= '<input type=button value="'.get_string('show', 'block_livegtranslate').'" onclick="document.getElementById(\'divStayTopLeft\').style[\'display\']=\'block\';">';
        $this->content->footer = '';

        return $this->content;
    }
}
?>
