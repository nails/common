<?php

/**
 * Alters CI Profiler functionality so that array and objects cna be printed to screen
 *
 * @package     Nails
 * @subpackage  common
 * @category    Library
 * @author      Nails Dev Team
 * @link
 */

class CORE_NAILS_Profiler extends CI_Profiler {

    /**
     * Compile config information
     *
     * Lists developer config variables
     *
     * @return  string
     */
    protected function _compile_config()
    {
        $output  = "\n\n";
        $output .= '<fieldset id="ci_profiler_config" style="border:1px solid #000;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee">';
        $output .= "\n";
        $output .= '<legend style="color:#000;">&nbsp;&nbsp;'.$this->CI->lang->line('profiler_config').'&nbsp;&nbsp;(<span style="cursor: pointer;" onclick="var s=document.getElementById(\'ci_profiler_config_table\').style;s.display=s.display==\'none\'?\'\':\'none\';this.innerHTML=this.innerHTML==\''.$this->CI->lang->line('profiler_section_show').'\'?\''.$this->CI->lang->line('profiler_section_hide').'\':\''.$this->CI->lang->line('profiler_section_show').'\';">'.$this->CI->lang->line('profiler_section_show').'</span>)</legend>';
        $output .= "\n";

        $output .= "\n\n<table style='width:100%; display:none' id='ci_profiler_config_table'>\n";

        foreach ($this->CI->config->config as $config=>$val)
        {
            if (is_array($val)||is_object($val))
            {
                $val = print_r($val, TRUE);
            }

            $output .= "<tr><td style='padding:5px; vertical-align: top;color:#900;background-color:#ddd;'>".$config."&nbsp;&nbsp;</td><td style='padding:5px; color:#000;background-color:#ddd;'>".htmlspecialchars($val)."</td></tr>\n";
        }

        $output .= "</table>\n";
        $output .= "</fieldset>";

        return $output;
    }

    // --------------------------------------------------------------------

    /**
     * Compile session userdata; NOTE: parent method modified from private -> protected,
     *
     * @return  string
     */
    protected function _compile_session_data()
    {
        if ( ! isset($this->CI->session))
        {
            return;
        }

        $output = '<fieldset id="ci_profiler_csession" style="border:1px solid #000;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee">';
        $output .= '<legend style="color:#000;">&nbsp;&nbsp;'.$this->CI->lang->line('profiler_session_data').'&nbsp;&nbsp;(<span style="cursor: pointer;" onclick="var s=document.getElementById(\'ci_profiler_session_data\').style;s.display=s.display==\'none\'?\'\':\'none\';this.innerHTML=this.innerHTML==\''.$this->CI->lang->line('profiler_section_show').'\'?\''.$this->CI->lang->line('profiler_section_hide').'\':\''.$this->CI->lang->line('profiler_section_show').'\';">'.$this->CI->lang->line('profiler_section_show').'</span>)</legend>';
        $output .= "<table style='width:100%;display:none' id='ci_profiler_session_data'>";

        foreach ($this->CI->session->all_userdata() as $key => $val)
        {
            if (is_array($val)||is_object($val))
            {
                $val = print_r($val, TRUE);
            }

            $output .= "<tr><td style='padding:5px; vertical-align: top;color:#900;background-color:#ddd;'>".$key."&nbsp;&nbsp;</td><td style='padding:5px; color:#000;background-color:#ddd;'>".htmlspecialchars($val)."</td></tr>\n";
        }

        $output .= '</table>';
        $output .= "</fieldset>";
        return $output;
    }
}
