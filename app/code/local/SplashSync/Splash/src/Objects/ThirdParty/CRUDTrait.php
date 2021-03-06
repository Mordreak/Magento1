<?php
/*
 * Copyright (C) 2017   Splash Sync       <contact@splashsync.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
*/

namespace Splash\Local\Objects\ThirdParty;

use Splash\Core\SplashCore      as Splash;

// Magento Namespaces
use Mage;
use Mage_Customer_Exception;

/**
 * @abstract    Magento 1 Customers CRUD Functions
 */
trait CRUDTrait
{
    
    /**
     * @abstract    Load Request Object
     *
     * @param       array   $Id               Object id
     *
     * @return      mixed
     */
    public function Load($Id)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace(__CLASS__, __FUNCTION__);
        //====================================================================//
        // Init Object
        $Customer   =   Mage::getModel('customer/customer')->load($Id);
        if ($Customer->getEntityId() != $Id) {
            return Splash::log()->err("ErrLocalTpl", __CLASS__, __FUNCTION__, " Unable to load Customer (" . $Id . ").");
        }
        return $Customer;
    }
    
    /**
     * @abstract    Create Request Object
     *
     * @param       array   $List         Given Object Data
     *
     * @return      object     New Object
     */
    public function Create()
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace(__CLASS__, __FUNCTION__);
        //====================================================================//
        // Check Required Fields
        if (empty($this->In["firstname"])) {
            return Splash::log()->err("ErrLocalFieldMissing", __CLASS__, __FUNCTION__, "firstname");
        }
        if (empty($this->In["lastname"])) {
            return Splash::log()->err("ErrLocalFieldMissing", __CLASS__, __FUNCTION__, "lastname");
        }
        if (empty($this->In["email"])) {
            return Splash::log()->err("ErrLocalFieldMissing", __CLASS__, __FUNCTION__, "email");
        }
        //====================================================================//
        // Create Empty Customer
        $Customer   =   Mage::getModel('customer/customer')
                // Setup Customer in default store
                ->setStore($this->getSplashOriginStore());
        $Customer->setData("firstname", $this->In["firstname"]);
        $Customer->setData("lastname", $this->In["lastname"]);
        $Customer->setData("email", $this->In["email"]);
        //====================================================================//
        // Save Object
        try {
            $Customer->save();
        } catch (Mage_Customer_Exception $ex) {
            Splash::log()->deb($ex->getTraceAsString());
            return Splash::log()->err("ErrLocalTpl", __CLASS__, __FUNCTION__, $ex->getMessage());
        }
        return $Customer;
    }
    
    /**
     * @abstract    Update Request Object
     *
     * @param       array   $Needed         Is This Update Needed
     *
     * @return      string      Object Id
     */
    public function Update($Needed)
    {
        return $this->CoreUpdate($Needed);
    }
        
    /**
     * @abstract    Delete requested Object
     *
     * @param       int     $Id     Object Id.  If NULL, Object needs to be created.
     *
     * @return      bool
     */
    public function Delete($Id = null)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace(__CLASS__, __FUNCTION__);
        //====================================================================//
        // Execute Generic Magento Delete Function ...
        return $this->CoreDelete('customer/customer', $Id);
    }
}
