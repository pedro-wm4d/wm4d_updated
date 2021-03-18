<?php

class sl_plugin_db_settings
{
    /**
     * Creates database tables for the plugin using a *.sql file.
     *
     * @param string $sql_file
     * @param string $plugin_prefix
     *
     * @return bool
     */
    public function apphp_db_install($sql_file, $plugin_prefix)
    {
        if (file_exists($sql_file)) {
            $fd = fopen($sql_file, 'rb');
            $restore_query = fread($fd, filesize($sql_file));
            fclose($fd);
        } else {
            $db_error = 'SQL file does not exist: '.$sql_file;

            return false;
        }
        $sql_array = [];
        $sql_length = strlen($restore_query);
        $pos = strpos($restore_query, ';');
        for ($i = $pos; $i < $sql_length; ++$i) {
            if ($restore_query[0] == '#') {
                $restore_query = ltrim(substr($restore_query, strpos($restore_query, "\n")));
                $sql_length = strlen($restore_query);
                $i = strpos($restore_query, ';') - 1;
                continue;
            }
            if ($restore_query[($i + 1)] == "\n") {
                for ($j = ($i + 2); $j < $sql_length; ++$j) {
                    if (trim($restore_query[$j]) != '') {
                        $next = substr($restore_query, $j, 6);
                        if ($next[0] == '#') {
                            // /*find out where the break position is so we can remove this line (#comment line)*/
                            for ($k = $j; $k < $sql_length; ++$k) {
                                if ($restore_query[$k] == "\n") {
                                    break;
                                }
                            }
                            $query = substr($restore_query, 0, $i + 1);
                            $restore_query = substr($restore_query, $k);
                            // /*join the query before the comment appeared, with the rest of the dump*/
                            $restore_query = $query.$restore_query;
                            $sql_length = strlen($restore_query);
                            $i = strpos($restore_query, ';') - 1;
                            continue 2;
                        }
                        break;
                    }
                }
                if ($next == '') { // get the last insert query
                    $next = 'insert';
                }
                if ((preg_match('/create/i', $next)) || (preg_match('/insert/i', $next)) || (preg_match('/drop t/i', $next))) {
                    $next = '';
                    $sql_array[] = substr($restore_query, 0, $i);
                    $restore_query = ltrim(substr($restore_query, $i + 1));
                    $sql_length = strlen($restore_query);
                    $i = strpos($restore_query, ';') - 1;
                }
            }
        }
        for ($i = 0; $i < sizeof($sql_array); ++$i) {
            $next_new = explode('`', $sql_array[$i]);
            $get_query_type = substr($sql_array[$i], 0, 6);
            $str_sql_string = '';
            if (preg_match('/create/i', $get_query_type) || (preg_match('/insert/i', $get_query_type))) {
                $sql_array[$i] = str_replace($next_new[1], $plugin_prefix.$next_new[1], $sql_array[$i]); //add the database prefi to the query
            }
            $this->apphp_db_query($sql_array[$i]);
        }

        return true;
    }

    private function apphp_db_query($query)
    {
        global $link;
        $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD);
        mysqli_select_db($link, DB_NAME);
        $res = mysqli_query($link, $query);

        return $res;
    }
}
