<?php

/**
 * This extension overrides the generate function allowing more data to be made available to
 * the cells i.e month and year information. Also added new pseudo variable {day_padded}
 *
 * @package     Nails
 * @subpackage  common
 * @category    Library
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\CodeIgniter\Libraries;

use CI_Calendar;

class Calendar extends CI_Calendar
{

    /**
     * Generate the calendar
     *
     * @access  public
     *
     * @param   integer the year
     * @param   integer the month
     * @param   array   the data to be shown in the calendar cells
     *
     * @return  string
     */
    public function generate($year = '', $month = '', $data = [])
    {
        // Set and validate the supplied month/year
        if ($year == '') {
            $year = date("Y", $this->local_time);
        }

        if ($month == '') {
            $month = date("m", $this->local_time);
        }

        if (strlen($year) == 1) {
            $year = '200' . $year;
        }

        if (strlen($year) == 2) {
            $year = '20' . $year;
        }

        if (strlen($month) == 1) {
            $month = '0' . $month;
        }

        $adjusted_date = $this->adjust_date($month, $year);

        $month = $adjusted_date['month'];
        $year  = $adjusted_date['year'];

        // Determine the total days in the month
        $total_days = $this->get_total_days($month, $year);

        // Set the starting day of the week
        $start_days = [
            'sunday'    => 0,
            'monday'    => 1,
            'tuesday'   => 2,
            'wednesday' => 3,
            'thursday'  => 4,
            'friday'    => 5,
            'saturday'  => 6,
        ];
        $start_day  = (!isset($start_days[$this->start_day])) ? 0 : $start_days[$this->start_day];

        // Set the starting day number
        $local_date = mktime(12, 0, 0, $month, 1, $year);
        $date       = getdate($local_date);
        $day        = $start_day + 1 - $date["wday"];

        while ($day > 1) {
            $day -= 7;
        }

        // Set the current month/year/day
        // We use this to determine the "today" date
        $cur_year  = date("Y", $this->local_time);
        $cur_month = date("m", $this->local_time);
        $cur_day   = date("j", $this->local_time);

        $is_current_month = ($cur_year == $year && $cur_month == $month) ? true : false;

        // Generate the template data array
        $this->parse_template();

        // Begin building the calendar output
        $out = $this->temp['table_open'];
        $out .= "\n";

        $out .= "\n";
        $out .= $this->temp['heading_row_start'];
        $out .= "\n";

        // "previous" month link
        if ($this->show_next_prev == true) {
            // Add a trailing slash to the  URL if needed
            $this->next_prev_url = preg_replace("/(.+?)\/*$/", "\\1/", $this->next_prev_url);

            $adjusted_date = $this->adjust_date($month - 1, $year);
            $out           .= str_replace('{previous_url}', $this->next_prev_url . $adjusted_date['year'] . '/' . $adjusted_date['month'], $this->temp['heading_previous_cell']);
            $out           .= "\n";
        }

        // Heading containing the month/year
        $colspan = ($this->show_next_prev == true) ? 5 : 7;

        $this->temp['heading_title_cell'] = str_replace('{colspan}', $colspan, $this->temp['heading_title_cell']);
        $this->temp['heading_title_cell'] = str_replace('{heading}', $this->get_month_name($month) . "&nbsp;" . $year, $this->temp['heading_title_cell']);

        $out .= $this->temp['heading_title_cell'];
        $out .= "\n";

        // "next" month link
        if ($this->show_next_prev == true) {
            $adjusted_date = $this->adjust_date($month + 1, $year);
            $out           .= str_replace('{next_url}', $this->next_prev_url . $adjusted_date['year'] . '/' . $adjusted_date['month'], $this->temp['heading_next_cell']);
        }

        $out .= "\n";
        $out .= $this->temp['heading_row_end'];
        $out .= "\n";

        // Write the cells containing the days of the week
        $out .= "\n";
        $out .= $this->temp['week_row_start'];
        $out .= "\n";

        $day_names = $this->get_day_names();

        for ($i = 0; $i < 7; $i++) {
            $out .= str_replace('{week_day}', $day_names[($start_day + $i) % 7], $this->temp['week_day_cell']);
        }

        $out .= "\n";
        $out .= $this->temp['week_row_end'];
        $out .= "\n";

        // Build the main body of the calendar
        while ($day <= $total_days) {
            $out .= "\n";
            $out .= $this->temp['cal_row_start'];
            $out .= "\n";

            for ($i = 0; $i < 7; $i++) {
                $out .= ($is_current_month == true && $day == $cur_day) ? $this->temp['cal_cell_start_today'] : $this->temp['cal_cell_start'];

                if ($day > 0 && $day <= $total_days) {

                    /* --------------------------------------------------------------------------
                     * Start of Shed modification.
                     * Making more information available to cells
                    /* ------------------------------------------------------------------------*/

                    if (isset($data[$day])) {
                        // Cells with content
                        $temp = ($is_current_month == true && $day == $cur_day) ? $this->temp['cal_cell_content_today'] : $this->temp['cal_cell_content'];
                        $temp = str_replace('{day}', $day, $temp);
                        $temp = str_replace('{day_padded}', str_pad($day, 2, '0', STR_PAD_LEFT), $temp);
                        $temp = str_replace('{month}', $month, $temp);
                        $temp = str_replace('{year}', $year, $temp);
                        $temp = str_replace('{content}', $data[$day], $temp);

                        $out .= $temp;
                    } else {
                        // Cells with no content
                        $temp = ($is_current_month == true && $day == $cur_day) ? $this->temp['cal_cell_no_content_today'] : $this->temp['cal_cell_no_content'];
                        $temp = str_replace('{day}', $day, $temp);
                        $temp = str_replace('{day_padded}', str_pad($day, 2, '0', STR_PAD_LEFT), $temp);
                        $temp = str_replace('{month}', $month, $temp);
                        $temp = str_replace('{year}', $year, $temp);

                        $out .= $temp;
                    }

                    /* --------------------------------------------------------------------------
                     * End of Shed modification.
                    /* ------------------------------------------------------------------------*/

                } else {
                    // Blank cells
                    $out .= $this->temp['cal_cell_blank'];
                }

                $out .= ($is_current_month == true && $day == $cur_day) ? $this->temp['cal_cell_end_today'] : $this->temp['cal_cell_end'];
                $day++;
            }

            $out .= "\n";
            $out .= $this->temp['cal_row_end'];
            $out .= "\n";
        }

        $out .= "\n";
        $out .= $this->temp['table_close'];

        return $out;
    }
}
