<?php
/**
* My Handy Restaurant
*
* http://www.myhandyrestaurant.org
*
* My Handy Restaurant is a restaurant complete management tool.
* Visit {@link http://www.myhandyrestaurant.org} for more info.
* Copyright (C) 2003-2005 Fabio De Pascale
* 
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
* 
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*
* @author		Fabio 'Kilyerd' De Pascale <public@fabiolinux.com>
* @package		MyHandyRestaurant
* @copyright		Copyright 2003-2005, Fabio De Pascale
* @copyright	Copyright 2006-2012, Gjergj Sheldija
*/

$inizio=microtime();			//has to be before start.php requirement!!!
session_start();

define('ROOTDIR','..');
$dont_get_session_sourceid=true;
$dont_redirect_to_menu=true;
require_once(ROOTDIR."/includes.php");
require_once(ROOTDIR."/waiter/waiter_start.php");

$GLOBALS['end_require_time']=microtime();

unset_source_vars();

$time_refresh=1000*get_conf(__FILE__,__LINE__,'refresh_automatic_on_menu');
$target='tables.php?rndm='.rand(0,100000);
if($time_refresh) $tmp = redirect_timed($target,$time_refresh);
$tpl -> append ('scripts',$tmp);

$tpl -> set_waiter_template_file ('tables');

$user = new user($_SESSION['userid']);

if(!access_allowed(USER_BIT_WAITER) && !access_allowed(USER_BIT_CASHIER)) {
	access_denied_waiter();
};

if($user->level[USER_BIT_CASHIER])
	$tpl -> append ('tables',tables_list_all(1,1));

$tpl -> append ('tables',tables_list_all(1,2));

if($user->level[USER_BIT_CASHIER]) $cols=get_conf(__FILE__,__LINE__,'menu_tables_per_row_cashier');
else $cols=get_conf(__FILE__,__LINE__,'menu_tables_per_row_waiter');

$tpl -> append ('tables',tables_list_all($cols,0,false));

if(!$user->level[USER_BIT_CASHIER])
	$tpl -> append ('tables',tables_list_all(1,1));


// prints page generation time
$tmp = generating_time($inizio);
$tpl -> assign ('generating_time',$tmp);

// html closing stuff and disconnect line
$tmp = disconnect_line();
$tpl -> assign ('logout',$tmp);

if($err=$tpl->parse()) return $err; 

$tpl -> clean();
$output = $tpl->getOutput();

// prints everything to screen
echo $output;

if(CONF_DEBUG_PRINT_PAGE_SIZE) echo $tpl -> print_size();
?>
