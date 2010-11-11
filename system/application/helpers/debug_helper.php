<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Outputs an array or variable
*
* @param    $var array, string, integer
* @return    string
*/

    function debug_var($var = '')
        {
        $return = _before();
            if (is_array($var))
            {
                $return .= print_r($var, true);
            }
                else
            {
                $return .= $var;
            }
            $return .= _after();
        return $return;
        }

//------------------------------------------------------------------------------

/**
* _before
*
* @return    string
*/
    function _before()
    {
        $before = '<div style="padding:10px 20px 10px 20px; background-color:#fbe6f2; border:1px solid #d893a1; color: #000; font-size: 12px;>'."\n";
        $before .= '<h5 style="font-family:verdana,sans-serif; font-weight:bold; font-size:18px;">Debug Helper Output</h5>'."\n";
        $before .= '<pre>'."\n";
        return $before;
    }
    
//------------------------------------------------------------------------------

/**
* _after
*
* @return    string
*/

    function _after()
    {
        $after = '</pre>'."\n";
        $after .= '</div>'."\n";
        return $after;
    }

