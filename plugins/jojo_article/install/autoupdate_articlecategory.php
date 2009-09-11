<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2008 Jojo CMS
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Michael Cochrane <mikec@jojocms.org>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */

$default_td['articlecategory'] = array(
        'td_name' => "articlecategory",
        'td_primarykey' => "articlecategoryid",
        'td_displayfield' => "ac_url",
        'td_filter' => "yes",
        'td_topsubmit' => "yes",
        'td_deleteoption' => "yes",
        'td_menutype' => "list",
        'td_help' => "News Article Categories are managed from here.",
    );


/* Content Tab */

// Articlecategoryid Field
$default_fd['articlecategory']['articlecategoryid'] = array(
        'fd_name' => "Articlecategoryid",
        'fd_type' => "readonly",
        'fd_help' => "A unique ID, automatically assigned by the system",
        'fd_order' => "1",
        'fd_tabname' => "Content",
        'fd_mode' => "advanced",
    );

// URL Field
$default_fd['articlecategory']['ac_url'] = array(
        'fd_name' => "URL",
        'fd_type' => "internalurl",
        'fd_required' => "yes",
        'fd_size' => "60",
        'fd_help' => "URL for the Article Category. This will be used for the base URL for all articles in this category. The Page url for this category's home page MUST match the category URL.",
        'fd_order' => "2",
        'fd_tabname' => "Content",
    );

// Sortby Field
$default_fd['articlecategory']['sortby'] = array(
        'fd_name' => "Sortby",
        'fd_type' => "radio",
        'fd_options' => "ar_title asc:Title\nar_date desc:Article Date\nar_livedate desc:Go Live Date",
        'fd_default' => "ar_date desc",
        'fd_order' => "3",
        'fd_tabname' => "Content",
    );

