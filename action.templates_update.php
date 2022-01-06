<?php
# Module: Shop Made Simple - A product maintenance module for CMS - CMS Made Simple
# Copyright (c) 2008 by Duketown <duketown@mantox.nl>
#
# This function will update the templates as set in the admin part of module Shop Made Simple
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
# The module's homepage is: http://dev.cmsmadesimple.org/projects/sms
#
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------

if (!isset($gCms)) exit;

if (!$this->CheckPermission('Administer SimpleShop')) {
	return $this->DisplayErrorPage($id, $params, $returnid, $this->Lang('accessdenied'));
}

if (isset($params['catlist_template']) && !empty($params['catlist_template'])) {
	$this->SetTemplate('catlist_template', $params['catlist_template']);
}
if (isset($params['categories_template']) && !empty($params['categories_template'])) {
	$this->SetTemplate('categories_template', $params['categories_template']);
}
if (isset($params['proddetail_template']) && !empty($params['proddetail_template'])) {
	$this->SetTemplate('proddetail_template', $params['proddetail_template']);
}
if (isset($params['prodfeat_template']) && !empty($params['prodfeat_template'])) {
	$this->SetTemplate('prodfeat_template', $params['prodfeat_template']);
}

$this->Audit(0, $this->Lang('friendlyname'), $this->Lang('templatesupdated'));
$params['tab_message'] = $this->Lang('templatesupdated');
$params['active_tab'] = 'templates';
$this->DoAction('defaultadmin', $id, $params);
